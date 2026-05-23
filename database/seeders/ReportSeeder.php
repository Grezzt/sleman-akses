<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use App\Models\MapLocation;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReportSeeder extends Seeder
{
  /**
   * Seed reports, validations, report details, and map locations.
   */
  public function run(): void
  {
    $users = User::where('role', 'user')->get();

    if ($users->isEmpty()) {
      $users = collect([
        User::updateOrCreate(
          ['email' => 'sri@contoh.id'],
          [
            'full_name' => 'Sri Wulandari',
            'password' => Hash::make('user12345'),
            'role' => 'user',
          ]
        ),
        User::updateOrCreate(
          ['email' => 'budi@contoh.id'],
          [
            'full_name' => 'Budi Santoso',
            'password' => Hash::make('user12345'),
            'role' => 'user',
          ]
        ),
        User::updateOrCreate(
          ['email' => 'lilis@contoh.id'],
          [
            'full_name' => 'Lilis Pratama',
            'password' => Hash::make('user12345'),
            'role' => 'user',
          ]
        ),
      ]);
    }

    $categories = FacilityCategory::all()->keyBy('facility_name');

    $reportsData = [
      [
        'photo_url' => 'https://example.com/photos/report-01.jpg',
        'latitude' => -7.715421,
        'longitude' => 110.352991,
        'timestamp' => now()->subDays(6),
        'details' => [
          ['Ramp', true],
          ['Toilet Difabel', false],
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas ramp tersedia, toilet masih perlu perbaikan.',
          'validation_date' => now()->subDays(5),
        ],
        'map' => [
          'place_name' => 'Alun-Alun Sleman',
          'address' => 'Jl. Parasamya, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'RUANG PUBLIK',
          'latitude' => -7.715380,
          'longitude' => 110.352950,
          'publish_date' => now()->subDays(5),
        ],
      ],
      [
        'photo_url' => 'https://example.com/photos/report-02.jpg',
        'latitude' => -7.727812,
        'longitude' => 110.368445,
        'timestamp' => now()->subDays(4),
        'details' => [
          ['Parkir Difabel', true],
          ['Lift', true],
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Parkir difabel dan lift berfungsi dengan baik.',
          'validation_date' => now()->subDays(3),
        ],
        'map' => [
          'place_name' => 'RSUD Sleman',
          'address' => 'Jl. Bhayangkara, Sleman',
          'status' => 'active',
          'facility_type' => 'RUMAH SAKIT',
          'latitude' => -7.727760,
          'longitude' => 110.368410,
          'publish_date' => now()->subDays(3),
        ],
      ],
      [
        'photo_url' => 'https://example.com/photos/report-03.jpg',
        'latitude' => -7.703210,
        'longitude' => 110.357112,
        'timestamp' => now()->subDays(3),
        'details' => [
          ['Ramp', true],
          ['Lift', false],
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Lift tidak beroperasi, ramp tersedia.',
          'validation_date' => now()->subDays(2),
        ],
        'map' => [
          'place_name' => 'Gor Triharjo',
          'address' => 'Jl. Magelang, Sleman',
          'status' => 'active',
          'facility_type' => 'OLAH RAGA',
          'latitude' => -7.703150,
          'longitude' => 110.357070,
          'publish_date' => now()->subDays(2),
        ],
      ],
      [
        'photo_url' => 'https://example.com/photos/report-04.jpg',
        'latitude' => -7.689932,
        'longitude' => 110.343208,
        'timestamp' => now()->subDays(2),
        'details' => [
          ['Toilet Difabel', true],
          ['Parkir Difabel', false],
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Toilet difabel tersedia, parkir difabel belum tersedia.',
          'validation_date' => now()->subDay(),
        ],
        'map' => [
          'place_name' => 'Kantor Kecamatan Mlati',
          'address' => 'Jl. Magelang, Mlati, Sleman',
          'status' => 'active',
          'facility_type' => 'PELAYANAN PUBLIK',
          'latitude' => -7.689890,
          'longitude' => 110.343160,
          'publish_date' => now()->subDay(),
        ],
      ],
      [
        'photo_url' => 'https://example.com/photos/report-05.jpg',
        'latitude' => -7.741552,
        'longitude' => 110.356901,
        'timestamp' => now()->subDay(),
        'details' => [
          ['Ramp', false],
          ['Toilet Difabel', false],
        ],
        'validation' => [
          'approval_status' => 'pending',
          'evaluation_note' => null,
          'validation_date' => null,
        ],
        'map' => null,
      ],
      [
        'photo_url' => 'https://example.com/photos/report-06.jpg',
        'latitude' => -7.701113,
        'longitude' => 110.331772,
        'timestamp' => now()->subDays(1),
        'details' => [
          ['Parkir Difabel', true],
          ['Lift', false],
        ],
        'validation' => [
          'approval_status' => 'rejected',
          'evaluation_note' => 'Data lokasi kurang jelas, perlu foto ulang.',
          'validation_date' => now()->subDay(),
        ],
        'map' => null,
      ],
    ];

    foreach ($reportsData as $index => $reportData) {
      $user = $users->get($index % $users->count());

      $report = Report::updateOrCreate(
        ['photo_url' => $reportData['photo_url']],
        [
          'user_id' => $user->id,
          'latitude' => $reportData['latitude'],
          'longitude' => $reportData['longitude'],
          'timestamp' => $reportData['timestamp'],
        ]
      );

      ReportDetail::where('report_id', $report->id)->delete();

      foreach ($reportData['details'] as $detail) {
        $categoryName = $detail[0];
        $status = $detail[1];

        if (! $categories->has($categoryName)) {
          continue;
        }

        ReportDetail::create([
          'report_id' => $report->id,
          'category_id' => $categories->get($categoryName)->id,
          'availability_status' => $status,
        ]);
      }

      $validationPayload = [
        'user_id' => User::where('role', 'admin')->first()?->id,
        'approval_status' => $reportData['validation']['approval_status'],
        'evaluation_note' => $reportData['validation']['evaluation_note'],
        'validation_date' => $reportData['validation']['validation_date'],
      ];

      $validation = Validation::updateOrCreate(
        ['report_id' => $report->id],
        $validationPayload
      );

      if ($reportData['map'] && $validation->approval_status === 'approved') {
        MapLocation::updateOrCreate(
          ['validation_id' => $validation->id],
          [
            'place_name' => $reportData['map']['place_name'],
            'address' => $reportData['map']['address'],
            'status' => $reportData['map']['status'],
            'facility_type' => $reportData['map']['facility_type'],
            'latitude' => $reportData['map']['latitude'],
            'longitude' => $reportData['map']['longitude'],
            'publish_date' => $reportData['map']['publish_date'],
          ]
        );
      } else {
        MapLocation::where('validation_id', $validation->id)->delete();
      }
    }
  }
}
