<?php

namespace App\Services\Spatial;

use App\Models\MapLocation;
use Illuminate\Support\Facades\File;

class DbscanService
{
    private const EARTH_RADIUS_METERS = 6371000.0;

    /**
     * Hitung jarak geodesik antara dua koordinat menggunakan Formula Haversine (meter).
     */
    public function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Eksekusi analisis spasial DBSCAN dan pemetaan dinamis 17 Kapanewon.
     *
     * @param float $epsilon Jarak jangkauan tetangga dalam meter (default 800m)
     * @param int $minPts Jumlah minimum titik untuk membentuk klaster (default 3)
     * @return array
     */
    public function run(float $epsilon = 800.0, int $minPts = 3): array
    {
        // 1. Ambil seluruh fasilitas publik aktif beserta detail relasi
        $locations = MapLocation::with([
            'validation.report.details.category',
            'validation.report.user',
        ])
            ->where('status', 'active')
            ->get();

        $n = $locations->count();

        // 2. Load data referensi baseline Kapanewon dari JSON / GeoJSON
        $vitalPoiLookup = $this->loadVitalPoiLookup();
        $kapanewonList = $this->loadKapanewonBaseline();

        if ($n === 0) {
            return $this->buildEmptyResult($epsilon, $minPts, $kapanewonList);
        }

        // 3. Matriks Jarak Haversine
        $points = [];
        $distanceMatrix = [];

        foreach ($locations as $i => $loc) {
            $lat = (float) $loc->latitude;
            $lng = (float) $loc->longitude;

            // Resolusi Kapanewon
            $kapanewon = $vitalPoiLookup[$loc->place_name] ?? $this->findKapanewonForPoint($lat, $lng, $kapanewonList);

            // Ekstraksi aksesibilitas fisik
            $accessibility = [
                'ramp' => false,
                'toilet_difabel' => false,
                'parkir_difabel' => false,
                'lift' => false,
            ];

            if ($loc->validation?->report?->details) {
                foreach ($loc->validation->report->details as $detail) {
                    $catName = $detail->category?->facility_name;
                    if ($catName === 'Ramp') $accessibility['ramp'] = (bool) $detail->availability_status;
                    if ($catName === 'Toilet Difabel') $accessibility['toilet_difabel'] = (bool) $detail->availability_status;
                    if ($catName === 'Parkir Difabel') $accessibility['parkir_difabel'] = (bool) $detail->availability_status;
                    if ($catName === 'Lift') $accessibility['lift'] = (bool) $detail->availability_status;
                }
            }

            $points[$i] = [
                'index' => $i,
                'id' => $loc->id,
                'place_name' => $loc->place_name,
                'address' => $loc->address,
                'facility_type' => $loc->facility_type,
                'kapanewon' => $kapanewon,
                'latitude' => $lat,
                'longitude' => $lng,
                'accessibility' => $accessibility,
                'cluster_id' => null, // null = belum dikunjungi, -1 = noise, 1..N = ID klaster
                'point_type' => 'UNVISITED',
            ];
        }

        // Hitung matriks jarak
        for ($i = 0; $i < $n; $i++) {
            $distanceMatrix[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $distanceMatrix[$i][$j] = 0.0;
                } elseif ($j > $i) {
                    $d = $this->haversineDistance(
                        $points[$i]['latitude'],
                        $points[$i]['longitude'],
                        $points[$j]['latitude'],
                        $points[$j]['longitude']
                    );
                    $distanceMatrix[$i][$j] = $d;
                } else {
                    $distanceMatrix[$i][$j] = $distanceMatrix[$j][$i];
                }
            }
        }

        // 4. Algoritma DBSCAN
        $clusterId = 0;
        $visited = array_fill(0, $n, false);
        $isCore = array_fill(0, $n, false);

        // Pre-hitung tetangga tiap titik
        $neighbors = [];
        for ($i = 0; $i < $n; $i++) {
            $neighbors[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                if ($distanceMatrix[$i][$j] <= $epsilon) {
                    $neighbors[$i][] = $j;
                }
            }
            if (count($neighbors[$i]) >= $minPts) {
                $isCore[$i] = true;
            }
        }

        for ($i = 0; $i < $n; $i++) {
            if ($visited[$i]) {
                continue;
            }

            $visited[$i] = true;

            if (!$isCore[$i]) {
                $points[$i]['cluster_id'] = -1; // Noise sementara
                $points[$i]['point_type'] = 'NOISE';
                continue;
            }

            // Mulai klaster baru
            $clusterId++;
            $points[$i]['cluster_id'] = $clusterId;
            $points[$i]['point_type'] = 'CORE';

            // Antrean ekspansi klaster (seeds)
            $queue = $neighbors[$i];

            for ($k = 0; $k < count($queue); $k++) {
                $neighborIdx = $queue[$k];

                if (!$visited[$neighborIdx]) {
                    $visited[$neighborIdx] = true;

                    if ($isCore[$neighborIdx]) {
                        // Tambahkan tetangga dari core point baru ke queue
                        foreach ($neighbors[$neighborIdx] as $nIdx) {
                            if (!in_array($nIdx, $queue, true)) {
                                $queue[] = $nIdx;
                            }
                        }
                    }
                }

                // Masukkan titik ke klaster jika belum memiliki klaster atau sebelumnya noise
                if ($points[$neighborIdx]['cluster_id'] === null || $points[$neighborIdx]['cluster_id'] === -1) {
                    $points[$neighborIdx]['cluster_id'] = $clusterId;
                    $points[$neighborIdx]['point_type'] = $isCore[$neighborIdx] ? 'CORE' : 'BORDER';
                }
            }
        }

        // 5. Palet Warna Neobrutalism untuk Klaster
        $clusterColors = [
            1 => '#037940', // Hijau Sleman Primary
            2 => '#2563EB', // Biru Kontras
            3 => '#D97706', // Oranye Amber
            4 => '#7C3AED', // Ungu
            5 => '#DB2777', // Merah Muda
            6 => '#0D9488', // Teal
            7 => '#4F46E5', // Indigo
        ];

        // 6. Ringkasan Klaster
        $clustersSummary = [];
        for ($c = 1; $c <= $clusterId; $c++) {
            $cPoints = array_filter($points, fn($p) => $p['cluster_id'] === $c);
            $cCount = count($cPoints);
            $cCoreCount = count(array_filter($cPoints, fn($p) => $p['point_type'] === 'CORE'));
            $cBorderCount = count(array_filter($cPoints, fn($p) => $p['point_type'] === 'BORDER'));

            $lats = array_column($cPoints, 'latitude');
            $lngs = array_column($cPoints, 'longitude');
            $kapanewonCounts = array_count_values(array_column($cPoints, 'kapanewon'));
            arsort($kapanewonCounts);
            $dominantKapanewon = array_key_first($kapanewonCounts) ?? 'Tidak Diketahui';

            $clustersSummary[] = [
                'cluster_id' => $c,
                'label' => "Klaster {$c} (Aglomerasi {$dominantKapanewon})",
                'color' => $clusterColors[$c] ?? '#0F0F0F',
                'points_count' => $cCount,
                'core_count' => $cCoreCount,
                'border_count' => $cBorderCount,
                'dominant_kapanewon' => $dominantKapanewon,
                'center' => [
                    array_sum($lats) / $cCount,
                    array_sum($lngs) / $cCount,
                ],
            ];
        }

        // 7. Agregasi Dinamis 17 Kapanewon & Deteksi Blank Spot
        $kapanewonAggregations = [];
        $blankSpotCount = 0;

        foreach ($kapanewonList as $kapName => $kapMeta) {
            $kapPoints = array_filter($points, fn($p) => $p['kapanewon'] === $kapName);
            $count = count($kapPoints);

            $cIds = array_unique(array_filter(array_column($kapPoints, 'cluster_id'), fn($id) => $id > 0));
            $noiseInKap = count(array_filter($kapPoints, fn($p) => $p['cluster_id'] === -1));

            // Klasifikasi Status Kapanewon
            if ($count === 0) {
                $status = 'BLANK_SPOT';
                $statusLabel = 'Belum Ada Fasilitas (Defisit)';
                $color = '#DC2626'; // Merah
                $blankSpotCount++;
            } elseif ($count >= 10) {
                $status = 'HIGH_DENSITY';
                $statusLabel = 'Kawasan Kepadatan Tinggi';
                $color = '#037940'; // Hijau Primary
            } elseif (!empty($cIds)) {
                $status = 'MEDIUM_DENSITY';
                $statusLabel = 'Kawasan Fasilitas Memadai';
                $color = '#2563EB'; // Biru
            } else {
                $status = 'ISOLATED_FACILITIES';
                $statusLabel = 'Fasilitas Tersebar Mandiri';
                $color = '#D97706'; // Amber
            }

            // Metrik Aksesibilitas di Kapanewon
            $rampCount = 0;
            $toiletCount = 0;
            $parkirCount = 0;
            $liftCount = 0;

            foreach ($kapPoints as $kp) {
                if ($kp['accessibility']['ramp']) $rampCount++;
                if ($kp['accessibility']['toilet_difabel']) $toiletCount++;
                if ($kp['accessibility']['parkir_difabel']) $parkirCount++;
                if ($kp['accessibility']['lift']) $liftCount++;
            }

            $totalPossible = $count * 4;
            $actualSum = $rampCount + $toiletCount + $parkirCount + $liftCount;
            $accessPercentage = $totalPossible > 0 ? round(($actualSum / $totalPossible) * 100, 1) : 0.0;

            $kapanewonAggregations[] = [
                'kapanewon' => $kapName,
                'luas_km2' => $kapMeta['luas_km2'] ?? 0.0,
                'center' => $kapMeta['center'] ?? [-7.75, 110.36],
                'facilities_count' => $count,
                'status' => $status,
                'status_label' => $statusLabel,
                'color' => $color,
                'cluster_ids' => array_values($cIds),
                'noise_count' => $noiseInKap,
                'accessibility_percentage' => $accessPercentage,
                'details' => [
                    'ramp' => $rampCount,
                    'toilet_difabel' => $toiletCount,
                    'parkir_difabel' => $parkirCount,
                    'lift' => $liftCount,
                ],
            ];
        }

        // Urutkan Kapanewon: fasilitas terbanyak lebih dahulu
        usort($kapanewonAggregations, fn($a, $b) => $b['facilities_count'] <=> $a['facilities_count']);

        $noisePointsCount = count(array_filter($points, fn($p) => $p['cluster_id'] === -1));
        $corePointsCount = count(array_filter($points, fn($p) => $p['point_type'] === 'CORE'));
        $borderPointsCount = count(array_filter($points, fn($p) => $p['point_type'] === 'BORDER'));

        return [
            'parameters' => [
                'epsilon' => $epsilon,
                'min_pts' => $minPts,
            ],
            'summary' => [
                'total_facilities' => $n,
                'total_clusters' => $clusterId,
                'total_core_points' => $corePointsCount,
                'total_border_points' => $borderPointsCount,
                'total_noise_points' => $noisePointsCount,
                'total_kapanewon' => count($kapanewonList),
                'blank_spot_count' => $blankSpotCount,
                'blank_spot_ratio' => round(($blankSpotCount / count($kapanewonList)) * 100, 1),
            ],
            'clusters' => $clustersSummary,
            'facilities' => array_values($points),
            'kapanewon' => $kapanewonAggregations,
        ];
    }

    /**
     * Muat lookup Kapanewon dari sleman_vital_poi.json (Google Maps scrape).
     */
    private function loadVitalPoiLookup(): array
    {
        $path = database_path('data/sleman_vital_poi.json');
        if (!File::exists($path)) {
            return [];
        }

        $items = json_decode(File::get($path), true);
        if (!is_array($items)) {
            return [];
        }

        $lookup = [];
        foreach ($items as $item) {
            if (!empty($item['place_name']) && !empty($item['kapanewon'])) {
                $lookup[$item['place_name']] = $item['kapanewon'];
            }
        }

        return $lookup;
    }

    /**
     * Muat daftar 17 Kapanewon resmi beserta metadata luas dari GeoJSON.
     */
    private function loadKapanewonBaseline(): array
    {
        $path = public_path('data/sleman_kapanewon.geojson');
        if (!File::exists($path)) {
            // Fallback daftar 17 Kapanewon Sleman
            return [
                'Berbah' => ['luas_km2' => 25.05, 'center' => [-7.8066, 110.4466]],
                'Cangkringan' => ['luas_km2' => 47.99, 'center' => [-7.6185, 110.4578]],
                'Depok' => ['luas_km2' => 35.55, 'center' => [-7.7592, 110.4048]],
                'Gamping' => ['luas_km2' => 29.25, 'center' => [-7.7955, 110.3292]],
                'Godean' => ['luas_km2' => 26.84, 'center' => [-7.7667, 110.2934]],
                'Kalasan' => ['luas_km2' => 35.85, 'center' => [-7.7684, 110.4682]],
                'Minggir' => ['luas_km2' => 27.27, 'center' => [-7.7317, 110.2458]],
                'Mlati' => ['luas_km2' => 28.52, 'center' => [-7.7371, 110.3621]],
                'Moyudan' => ['luas_km2' => 27.62, 'center' => [-7.7852, 110.2526]],
                'Ngaglik' => ['luas_km2' => 38.52, 'center' => [-7.7118, 110.3957]],
                'Ngemplak' => ['luas_km2' => 35.71, 'center' => [-7.7029, 110.4504]],
                'Pakem' => ['luas_km2' => 43.84, 'center' => [-7.6582, 110.4208]],
                'Prambanan' => ['luas_km2' => 41.35, 'center' => [-7.7779, 110.4998]],
                'Seyegan' => ['luas_km2' => 26.63, 'center' => [-7.7289, 110.3061]],
                'Sleman' => ['luas_km2' => 31.32, 'center' => [-7.6893, 110.3478]],
                'Tempel' => ['luas_km2' => 32.49, 'center' => [-7.6534, 110.3228]],
                'Turi' => ['luas_km2' => 43.09, 'center' => [-7.6247, 110.3804]],
            ];
        }

        $geo = json_decode(File::get($path), true);
        $result = [];

        if (isset($geo['features']) && is_array($geo['features'])) {
            foreach ($geo['features'] as $f) {
                $name = $f['properties']['kapanewon'] ?? null;
                if ($name) {
                    $result[$name] = [
                        'luas_km2' => (float) ($f['properties']['luas_km2'] ?? 0.0),
                        'center' => $f['properties']['center'] ?? [-7.75, 110.36],
                    ];
                }
            }
        }

        ksort($result);
        return $result;
    }

    /**
     * Fallback pencarian Kapanewon berdasarkan jarak terdekat dengan pusat Kapanewon.
     */
    private function findKapanewonForPoint(float $lat, float $lng, array $kapanewonList): string
    {
        $minDist = INF;
        $closest = 'Sleman';

        foreach ($kapanewonList as $name => $meta) {
            $cLat = $meta['center'][0] ?? -7.75;
            $cLng = $meta['center'][1] ?? 110.36;
            $dist = $this->haversineDistance($lat, $lng, $cLat, $cLng);
            if ($dist < $minDist) {
                $minDist = $dist;
                $closest = $name;
            }
        }

        return $closest;
    }

    /**
     * Format hasil kosong jika tidak ada data aktif.
     */
    private function buildEmptyResult(float $epsilon, int $minPts, array $kapanewonList): array
    {
        return [
            'parameters' => ['epsilon' => $epsilon, 'min_pts' => $minPts],
            'summary' => [
                'total_facilities' => 0,
                'total_clusters' => 0,
                'total_core_points' => 0,
                'total_border_points' => 0,
                'total_noise_points' => 0,
                'total_kapanewon' => count($kapanewonList),
                'blank_spot_count' => count($kapanewonList),
                'blank_spot_ratio' => 100.0,
            ],
            'clusters' => [],
            'facilities' => [],
            'kapanewon' => array_map(function ($name, $meta) {
                return [
                    'kapanewon' => $name,
                    'luas_km2' => $meta['luas_km2'] ?? 0,
                    'facilities_count' => 0,
                    'status' => 'BLANK_SPOT',
                    'status_label' => 'Belum Ada Fasilitas (Defisit)',
                    'color' => '#DC2626',
                    'cluster_ids' => [],
                    'noise_count' => 0,
                    'accessibility_percentage' => 0.0,
                    'details' => ['ramp' => 0, 'toilet_difabel' => 0, 'parkir_difabel' => 0, 'lift' => 0],
                ];
            }, array_keys($kapanewonList), array_values($kapanewonList)),
        ];
    }
}
