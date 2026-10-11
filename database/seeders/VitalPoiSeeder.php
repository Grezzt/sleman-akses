<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use App\Models\MapLocation;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class VitalPoiSeeder extends Seeder
{
    /**
     * Jalankan proses seeding 40 titik baseline infrastruktur vital Kabupaten Sleman.
     * Catatan: Seeder ini dirancang secara idempotent (aman dijalankan berulang tanpa duplikasi).
     */
    public function run(): void
    {
        $jsonPath = database_path('data/sleman_vital_poi.json');

        if (! File::exists($jsonPath)) {
            $this->command->error("File dataset tidak ditemukan di: {$jsonPath}");
            return;
        }

        $poiData = json_decode(File::get($jsonPath), true);

        if (! is_array($poiData)) {
            $this->command->error('Format file JSON tidak valid!');
            return;
        }

        // 1. Akun Admin Verifikator (Kurator Sistem)
        $adminUser = User::where('role', 'admin')->first();
        if (! $adminUser) {
            $adminUser = User::create([
                'email' => 'admin@sleman-akses.id',
                'full_name' => 'Admin Sleman Akses',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        // 2. Akun Pelapor Khusus Baseline (Volunteered Geographic Information)
        $surveyorUser = User::firstOrCreate(
            ['email' => 'survei.baseline@sleman-akses.id'],
            [
                'full_name' => 'Tim Kurasi Baseline Sleman Akses',
                'password' => Hash::make('baseline12345'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        // 0. Pembersihan data dummy lama pelaporan untuk menjamin Zero Dummy Data
        MapLocation::query()->delete();
        Validation::query()->delete();
        ReportDetail::query()->delete();
        Report::query()->delete();

        // 3. Peta Kategori Fasilitas
        $categories = FacilityCategory::all()->keyBy('facility_name');

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($poiData as $item) {
            // A. Entitas Laporan (Report)
            $report = Report::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
                'user_id' => $surveyorUser->id,
                'photo_url' => $item['photo_url'] ?? 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg',
                'timestamp' => now()->subDays(rand(5, 30)),
            ]);

            // B. Entitas Detail Fasilitas (Report Details)
            ReportDetail::where('report_id', $report->id)->delete();

            $facilityMapping = [
                'Ramp' => $item['accessibility']['ramp'] ?? false,
                'Toilet Difabel' => $item['accessibility']['toilet_difabel'] ?? false,
                'Parkir Difabel' => $item['accessibility']['parkir_difabel'] ?? false,
                'Lift' => $item['accessibility']['lift'] ?? false,
            ];

            foreach ($facilityMapping as $categoryName => $isAvailable) {
                if ($categories->has($categoryName)) {
                    ReportDetail::create([
                        'report_id' => $report->id,
                        'category_id' => $categories->get($categoryName)->id,
                        'availability_status' => (bool) $isAvailable,
                    ]);
                }
            }

            // C. Entitas Validasi Admin (Validation)
            $validation = Validation::updateOrCreate(
                ['report_id' => $report->id],
                [
                    'user_id' => $adminUser->id,
                    'approval_status' => 'approved',
                    'evaluation_note' => "Data terverifikasi kurator: Baseline Infrastruktur Vital Sleman ({$item['place_name']})",
                    'validation_date' => now()->subDays(rand(1, 4)),
                ]
            );

            // D. Entitas Peta Publik (Map Location)
            $mapLocation = MapLocation::updateOrCreate(
                ['validation_id' => $validation->id],
                [
                    'place_name' => $item['place_name'],
                    'address' => $item['address'],
                    'status' => 'active',
                    'facility_type' => $item['facility_type'],
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'publish_date' => now()->subDays(rand(1, 4)),
                ]
            );

            if ($mapLocation->wasRecentlyCreated) {
                $insertedCount++;
            } else {
                $updatedCount++;
            }
        }

        $this->command->info("Selesai! {$insertedCount} data baru ditambahkan, {$updatedCount} data diperbarui.");
    }
}
