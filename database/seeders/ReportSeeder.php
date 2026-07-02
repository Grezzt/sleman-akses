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
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=1',
        'latitude' => -7.759216,
        'longitude' => 110.395107,
        'timestamp' => now()->subDays(22),
        'details' => [
          ['Ramp', true],
          ['Parkir Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Pakuwon Mall Jogja tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(21),
        ],
        'map' => [
          'place_name' => 'Pakuwon Mall Jogja',
          'address' => 'Jl. Ring Road Utara, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'MALL',
          'latitude' => -7.759247,
          'longitude' => 110.395175,
          'publish_date' => now()->subDays(21),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=2',
        'latitude' => -7.782685,
        'longitude' => 110.401409,
        'timestamp' => now()->subDays(21),
        'details' => [
          ['Lift', true],
          ['Parkir Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Plaza Ambarrukmo tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(20),
        ],
        'map' => [
          'place_name' => 'Plaza Ambarrukmo',
          'address' => 'Jl. Laksda Adisucipto, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'MALL',
          'latitude' => -7.782728,
          'longitude' => 110.40149,
          'publish_date' => now()->subDays(20),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664144/lift-kursi-roda-di-tangga-umum-4_waife0.jpg?v=3',
        'latitude' => -7.724352,
        'longitude' => 110.363917,
        'timestamp' => now()->subDays(29),
        'details' => [
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Sleman City Hall tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(28),
        ],
        'map' => [
          'place_name' => 'Sleman City Hall',
          'address' => 'Jl. Magelang Km 9.6, Sleman',
          'status' => 'active',
          'facility_type' => 'MALL',
          'latitude' => -7.724285,
          'longitude' => 110.363991,
          'publish_date' => now()->subDays(28),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663875/toilet_wfpm1h.jpg?v=4',
        'latitude' => -7.767971,
        'longitude' => 110.378712,
        'timestamp' => now()->subDays(22),
        'details' => [
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Universitas Gadjah Mada (UGM) tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(21),
        ],
        'map' => [
          'place_name' => 'Universitas Gadjah Mada (UGM)',
          'address' => 'Bulaksumur, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'PENDIDIKAN',
          'latitude' => -7.76798,
          'longitude' => 110.378775,
          'publish_date' => now()->subDays(21),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663779/WhatsApp-Image-2025-03-18-at-11.20.00-scaled_feiyao.jpg?v=5',
        'latitude' => -7.760126,
        'longitude' => 110.409362,
        'timestamp' => now()->subDays(15),
        'details' => [
          ['Toilet Difabel', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di UPN Veteran Yogyakarta tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(14),
        ],
        'map' => [
          'place_name' => 'UPN Veteran Yogyakarta',
          'address' => 'Jl. SWK 104, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'PENDIDIKAN',
          'latitude' => -7.760126,
          'longitude' => 110.409395,
          'publish_date' => now()->subDays(14),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664061/Parkir-khusus-difabel_qkxvmp.jpg?v=6',
        'latitude' => -7.773321,
        'longitude' => 110.385929,
        'timestamp' => now()->subDays(16),
        'details' => [
          ['Parkir Difabel', true],
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Universitas Negeri Yogyakarta (UNY) tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(15),
        ],
        'map' => [
          'place_name' => 'Universitas Negeri Yogyakarta (UNY)',
          'address' => 'Jl. Colombo, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'PENDIDIKAN',
          'latitude' => -7.773418,
          'longitude' => 110.386001,
          'publish_date' => now()->subDays(15),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663998/WhatsApp_Image_2023-03-29_at_12.39.01_ucwwue.jpg?v=7',
        'latitude' => -7.768703,
        'longitude' => 110.373351,
        'timestamp' => now()->subDays(22),
        'details' => [
          ['Parkir Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di RSUP Dr. Sardjito tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(21),
        ],
        'map' => [
          'place_name' => 'RSUP Dr. Sardjito',
          'address' => 'Jl. Kesehatan, Senayan, Sleman',
          'status' => 'active',
          'facility_type' => 'RUMAH SAKIT',
          'latitude' => -7.768783,
          'longitude' => 110.373449,
          'publish_date' => now()->subDays(21),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=8',
        'latitude' => -7.757074,
        'longitude' => 110.403966,
        'timestamp' => now()->subDays(8),
        'details' => [
          ['Ramp', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di RS JIH tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(7),
        ],
        'map' => [
          'place_name' => 'RS JIH',
          'address' => 'Jl. Ring Road Utara, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'RUMAH SAKIT',
          'latitude' => -7.757049,
          'longitude' => 110.403986,
          'publish_date' => now()->subDays(7),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=9',
        'latitude' => -7.697423,
        'longitude' => 110.334122,
        'timestamp' => now()->subDays(3),
        'details' => [
          ['Lift', true],
          ['Ramp', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di RSUD Sleman tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(2),
        ],
        'map' => [
          'place_name' => 'RSUD Sleman',
          'address' => 'Jl. Bhayangkara, Sleman',
          'status' => 'active',
          'facility_type' => 'RUMAH SAKIT',
          'latitude' => -7.697486,
          'longitude' => 110.334057,
          'publish_date' => now()->subDays(2),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664144/lift-kursi-roda-di-tangga-umum-4_waife0.jpg?v=10',
        'latitude' => -7.742794,
        'longitude' => 110.350366,
        'timestamp' => now()->subDays(8),
        'details' => [
          ['Lift', true],
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di RSA UGM tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(7),
        ],
        'map' => [
          'place_name' => 'RSA UGM',
          'address' => 'Jl. Kabupaten, Gamping, Sleman',
          'status' => 'active',
          'facility_type' => 'RUMAH SAKIT',
          'latitude' => -7.742841,
          'longitude' => 110.350325,
          'publish_date' => now()->subDays(7),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663875/toilet_wfpm1h.jpg?v=11',
        'latitude' => -7.756223,
        'longitude' => 110.395792,
        'timestamp' => now()->subDays(3),
        'details' => [
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Puskesmas Depok II tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(2),
        ],
        'map' => [
          'place_name' => 'Puskesmas Depok II',
          'address' => 'Jl. Affandi, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'KESEHATAN',
          'latitude' => -7.756184,
          'longitude' => 110.395724,
          'publish_date' => now()->subDays(2),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663779/WhatsApp-Image-2025-03-18-at-11.20.00-scaled_feiyao.jpg?v=12',
        'latitude' => -7.734123,
        'longitude' => 110.350742,
        'timestamp' => now()->subDays(7),
        'details' => [
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Puskesmas Mlati II tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(6),
        ],
        'map' => [
          'place_name' => 'Puskesmas Mlati II',
          'address' => 'Jl. Cebongan, Mlati, Sleman',
          'status' => 'active',
          'facility_type' => 'KESEHATAN',
          'latitude' => -7.734181,
          'longitude' => 110.350811,
          'publish_date' => now()->subDays(6),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664061/Parkir-khusus-difabel_qkxvmp.jpg?v=13',
        'latitude' => -7.712601,
        'longitude' => 110.353324,
        'timestamp' => now()->subDays(1),
        'details' => [
          ['Parkir Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Kantor Bupati Sleman tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(1),
        ],
        'map' => [
          'place_name' => 'Kantor Bupati Sleman',
          'address' => 'Jl. Parasamya, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'PELAYANAN PUBLIK',
          'latitude' => -7.712615,
          'longitude' => 110.353383,
          'publish_date' => now()->subDays(1),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663998/WhatsApp_Image_2023-03-29_at_12.39.01_ucwwue.jpg?v=14',
        'latitude' => -7.715336,
        'longitude' => 110.356330,
        'timestamp' => now()->subDays(30),
        'details' => [
          ['Parkir Difabel', true],
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Disdukcapil Sleman tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(29),
        ],
        'map' => [
          'place_name' => 'Disdukcapil Sleman',
          'address' => 'Jl. KRT Pringgodiningrat, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'PELAYANAN PUBLIK',
          'latitude' => -7.715367,
          'longitude' => 110.356247,
          'publish_date' => now()->subDays(29),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=15',
        'latitude' => -7.713887,
        'longitude' => 110.352547,
        'timestamp' => now()->subDays(24),
        'details' => [
          ['Ramp', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Mal Pelayanan Publik Sleman tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(23),
        ],
        'map' => [
          'place_name' => 'Mal Pelayanan Publik Sleman',
          'address' => 'Jl. Beran, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'PELAYANAN PUBLIK',
          'latitude' => -7.713911,
          'longitude' => 110.352467,
          'publish_date' => now()->subDays(23),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=16',
        'latitude' => -7.734811,
        'longitude' => 110.363346,
        'timestamp' => now()->subDays(22),
        'details' => [
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Terminal Jombor tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(21),
        ],
        'map' => [
          'place_name' => 'Terminal Jombor',
          'address' => 'Jl. Magelang, Mlati, Sleman',
          'status' => 'active',
          'facility_type' => 'TRANSPORTASI',
          'latitude' => -7.734891,
          'longitude' => 110.363292,
          'publish_date' => now()->subDays(21),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664144/lift-kursi-roda-di-tangga-umum-4_waife0.jpg?v=17',
        'latitude' => -7.754690,
        'longitude' => 110.397702,
        'timestamp' => now()->subDays(15),
        'details' => [
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Terminal Condongcatur tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(14),
        ],
        'map' => [
          'place_name' => 'Terminal Condongcatur',
          'address' => 'Jl. Anggajaya, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'TRANSPORTASI',
          'latitude' => -7.754719,
          'longitude' => 110.397637,
          'publish_date' => now()->subDays(14),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663875/toilet_wfpm1h.jpg?v=18',
        'latitude' => -7.783731,
        'longitude' => 110.438472,
        'timestamp' => now()->subDays(20),
        'details' => [
          ['Toilet Difabel', true],
          ['Ramp', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Stasiun Maguwo tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(19),
        ],
        'map' => [
          'place_name' => 'Stasiun Maguwo',
          'address' => 'Maguwoharjo, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'TRANSPORTASI',
          'latitude' => -7.783637,
          'longitude' => 110.438515,
          'publish_date' => now()->subDays(19),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663779/WhatsApp-Image-2025-03-18-at-11.20.00-scaled_feiyao.jpg?v=19',
        'latitude' => -7.784528,
        'longitude' => 110.435537,
        'timestamp' => now()->subDays(3),
        'details' => [
          ['Toilet Difabel', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Bandara Adisutjipto tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(2),
        ],
        'map' => [
          'place_name' => 'Bandara Adisutjipto',
          'address' => 'Jl. Raya Solo, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'TRANSPORTASI',
          'latitude' => -7.784534,
          'longitude' => 110.435451,
          'publish_date' => now()->subDays(2),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664061/Parkir-khusus-difabel_qkxvmp.jpg?v=20',
        'latitude' => -7.748321,
        'longitude' => 110.489770,
        'timestamp' => now()->subDays(26),
        'details' => [
          ['Parkir Difabel', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Pasar Prambanan tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(25),
        ],
        'map' => [
          'place_name' => 'Pasar Prambanan',
          'address' => 'Jl. Raya Solo - Yogyakarta, Sleman',
          'status' => 'active',
          'facility_type' => 'PASAR',
          'latitude' => -7.748364,
          'longitude' => 110.489728,
          'publish_date' => now()->subDays(25),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663998/WhatsApp_Image_2023-03-29_at_12.39.01_ucwwue.jpg?v=21',
        'latitude' => -7.766296,
        'longitude' => 110.293533,
        'timestamp' => now()->subDays(7),
        'details' => [
          ['Parkir Difabel', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Pasar Godean tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(6),
        ],
        'map' => [
          'place_name' => 'Pasar Godean',
          'address' => 'Jl. Godean, Godean, Sleman',
          'status' => 'active',
          'facility_type' => 'PASAR',
          'latitude' => -7.766395,
          'longitude' => 110.293465,
          'publish_date' => now()->subDays(6),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=22',
        'latitude' => -7.750031,
        'longitude' => 110.395724,
        'timestamp' => now()->subDays(3),
        'details' => [
          ['Ramp', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Pasar Colombo tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(2),
        ],
        'map' => [
          'place_name' => 'Pasar Colombo',
          'address' => 'Jl. Kaliurang, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'PASAR',
          'latitude' => -7.750106,
          'longitude' => 110.395648,
          'publish_date' => now()->subDays(2),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=23',
        'latitude' => -7.751939,
        'longitude' => 110.491542,
        'timestamp' => now()->subDays(13),
        'details' => [
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Candi Prambanan tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(12),
        ],
        'map' => [
          'place_name' => 'Candi Prambanan',
          'address' => 'Bokoharjo, Prambanan, Sleman',
          'status' => 'active',
          'facility_type' => 'WISATA',
          'latitude' => -7.75202,
          'longitude' => 110.491467,
          'publish_date' => now()->subDays(12),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664144/lift-kursi-roda-di-tangga-umum-4_waife0.jpg?v=24',
        'latitude' => -7.770577,
        'longitude' => 110.489328,
        'timestamp' => now()->subDays(29),
        'details' => [
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Candi Ratu Boko tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(28),
        ],
        'map' => [
          'place_name' => 'Candi Ratu Boko',
          'address' => 'Bokoharjo, Prambanan, Sleman',
          'status' => 'active',
          'facility_type' => 'WISATA',
          'latitude' => -7.770541,
          'longitude' => 110.489415,
          'publish_date' => now()->subDays(28),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663875/toilet_wfpm1h.jpg?v=25',
        'latitude' => -7.616264,
        'longitude' => 110.424404,
        'timestamp' => now()->subDays(29),
        'details' => [
          ['Toilet Difabel', true],
          ['Ramp', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Museum Gunungapi Merapi tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(28),
        ],
        'map' => [
          'place_name' => 'Museum Gunungapi Merapi',
          'address' => 'Jl. Kaliurang, Pakem, Sleman',
          'status' => 'active',
          'facility_type' => 'WISATA',
          'latitude' => -7.616335,
          'longitude' => 110.424364,
          'publish_date' => now()->subDays(28),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663779/WhatsApp-Image-2025-03-18-at-11.20.00-scaled_feiyao.jpg?v=26',
        'latitude' => -7.749634,
        'longitude' => 110.369600,
        'timestamp' => now()->subDays(2),
        'details' => [
          ['Toilet Difabel', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Monumen Yogya Kembali tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(1),
        ],
        'map' => [
          'place_name' => 'Monumen Yogya Kembali',
          'address' => 'Jl. Ring Road Utara, Ngaglik, Sleman',
          'status' => 'active',
          'facility_type' => 'WISATA',
          'latitude' => -7.74959,
          'longitude' => 110.369606,
          'publish_date' => now()->subDays(1),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664061/Parkir-khusus-difabel_qkxvmp.jpg?v=27',
        'latitude' => -7.749847,
        'longitude' => 110.419087,
        'timestamp' => now()->subDays(26),
        'details' => [
          ['Parkir Difabel', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Stadion Maguwoharjo tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(25),
        ],
        'map' => [
          'place_name' => 'Stadion Maguwoharjo',
          'address' => 'Maguwoharjo, Depok, Sleman',
          'status' => 'active',
          'facility_type' => 'OLAH RAGA',
          'latitude' => -7.749877,
          'longitude' => 110.419142,
          'publish_date' => now()->subDays(25),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663998/WhatsApp_Image_2023-03-29_at_12.39.01_ucwwue.jpg?v=28',
        'latitude' => -7.711905,
        'longitude' => 110.347301,
        'timestamp' => now()->subDays(9),
        'details' => [
          ['Parkir Difabel', true],
          ['Toilet Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di GOR Pangukan tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(8),
        ],
        'map' => [
          'place_name' => 'GOR Pangukan',
          'address' => 'Jl. Pangukan, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'OLAH RAGA',
          'latitude' => -7.711822,
          'longitude' => 110.347313,
          'publish_date' => now()->subDays(8),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=29',
        'latitude' => -7.715618,
        'longitude' => 110.354639,
        'timestamp' => now()->subDays(8),
        'details' => [
          ['Ramp', true],
          ['Lift', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Masjid Agung Sleman tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(7),
        ],
        'map' => [
          'place_name' => 'Masjid Agung Sleman',
          'address' => 'Jl. Parasamya, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'TEMPAT IBADAH',
          'latitude' => -7.71556,
          'longitude' => 110.35467,
          'publish_date' => now()->subDays(7),
        ],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=30',
        'latitude' => -7.715879,
        'longitude' => 110.354548,
        'timestamp' => now()->subDays(15),
        'details' => [
          ['Lift', true],
          ['Parkir Difabel', true]
        ],
        'validation' => [
          'approval_status' => 'approved',
          'evaluation_note' => 'Fasilitas di Alun-Alun Sleman tersedia dan dalam kondisi baik.',
          'validation_date' => now()->subDays(14),
        ],
        'map' => [
          'place_name' => 'Alun-Alun Sleman',
          'address' => 'Jl. Parasamya, Tridadi, Sleman',
          'status' => 'active',
          'facility_type' => 'RUANG PUBLIK',
          'latitude' => -7.715833,
          'longitude' => 110.354516,
          'publish_date' => now()->subDays(14),
        ],
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

    // --- DATA DUMMY UNTUK AKUN RIWAYAT KHUSUS ---
    $riwayatUser = User::updateOrCreate(
      ['email' => 'riwayat@contoh.id'],
      [
        'full_name' => 'Pengguna Aktif Laporan',
        'password' => Hash::make('user12345'),
        'role' => 'user',
      ]
    );

    $historyReportsData = [
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=31',
        'latitude' => -7.755421,
        'longitude' => 110.382991,
        'timestamp' => now()->subDays(21),
        'details' => [['Lift', true]],
        'validation' => ['approval_status' => 'approved', 'evaluation_note' => 'OK', 'validation_date' => now()->subDays(20)],
        'map' => ['place_name' => 'Halte Malioboro 1', 'address' => 'Jl. Malioboro', 'status' => 'active', 'facility_type' => 'TRANSPORTASI', 'latitude' => -7.755380, 'longitude' => 110.382950, 'publish_date' => now()->subDays(20)],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=32',
        'latitude' => -7.756421,
        'longitude' => 110.383991,
        'timestamp' => now()->subDays(22),
        'details' => [['Ramp', true]],
        'validation' => ['approval_status' => 'pending', 'evaluation_note' => null, 'validation_date' => null],
        'map' => null,
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663875/toilet_wfpm1h.jpg?v=33',
        'latitude' => -7.757421,
        'longitude' => 110.384991,
        'timestamp' => now()->subDays(23),
        'details' => [['Toilet Difabel', false]],
        'validation' => ['approval_status' => 'rejected', 'evaluation_note' => 'Foto tidak valid', 'validation_date' => now()->subDays(22)],
        'map' => null,
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664061/Parkir-khusus-difabel_qkxvmp.jpg?v=34',
        'latitude' => -7.758421,
        'longitude' => 110.385991,
        'timestamp' => now()->subDays(24),
        'details' => [['Parkir Difabel', true], ['Ramp', true]],
        'validation' => ['approval_status' => 'approved', 'evaluation_note' => 'OK', 'validation_date' => now()->subDays(23)],
        'map' => ['place_name' => 'RS Panti Rapih', 'address' => 'Jl. Cik Di Tiro', 'status' => 'inactive', 'facility_type' => 'RUMAH SAKIT', 'latitude' => -7.758380, 'longitude' => 110.385950, 'publish_date' => now()->subDays(23)],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664144/lift-kursi-roda-di-tangga-umum-4_waife0.jpg?v=35',
        'latitude' => -7.759421,
        'longitude' => 110.386991,
        'timestamp' => now()->subDays(25),
        'details' => [['Lift', true], ['Toilet Difabel', true]],
        'validation' => ['approval_status' => 'pending', 'evaluation_note' => null, 'validation_date' => null],
        'map' => null,
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663998/WhatsApp_Image_2023-03-29_at_12.39.01_ucwwue.jpg?v=36',
        'latitude' => -7.760421,
        'longitude' => 110.387991,
        'timestamp' => now()->subDays(26),
        'details' => [['Parkir Difabel', true]],
        'validation' => ['approval_status' => 'approved', 'evaluation_note' => 'Sesuai standar', 'validation_date' => now()->subDays(25)],
        'map' => ['place_name' => 'Kampus UGM', 'address' => 'Bulaksumur', 'status' => 'active', 'facility_type' => 'PENDIDIKAN', 'latitude' => -7.760380, 'longitude' => 110.387950, 'publish_date' => now()->subDays(25)],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663779/WhatsApp-Image-2025-03-18-at-11.20.00-scaled_feiyao.jpg?v=37',
        'latitude' => -7.761421,
        'longitude' => 110.388991,
        'timestamp' => now()->subDays(27),
        'details' => [['Toilet Difabel', true]],
        'validation' => ['approval_status' => 'rejected', 'evaluation_note' => 'Tidak jelas lokasinya', 'validation_date' => now()->subDays(26)],
        'map' => null,
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779663916/IMG_2082-scaled_y65ag6.jpg?v=38',
        'latitude' => -7.762421,
        'longitude' => 110.389991,
        'timestamp' => now()->subDays(28),
        'details' => [['Ramp', true]],
        'validation' => ['approval_status' => 'pending', 'evaluation_note' => null, 'validation_date' => null],
        'map' => null,
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664170/lift-kursi-roda-di-tangga-umum-1-1024x768_gnmiba.jpg?v=39',
        'latitude' => -7.763421,
        'longitude' => 110.390991,
        'timestamp' => now()->subDays(29),
        'details' => [['Lift', true]],
        'validation' => ['approval_status' => 'approved', 'evaluation_note' => 'Bagus dan terawat', 'validation_date' => now()->subDays(28)],
        'map' => ['place_name' => 'Ambarrukmo Plaza', 'address' => 'Jl. Laksda Adisucipto', 'status' => 'active', 'facility_type' => 'MALL', 'latitude' => -7.763380, 'longitude' => 110.390950, 'publish_date' => now()->subDays(28)],
      ],
      [
        'photo_url' => 'https://res.cloudinary.com/debmlrrkg/image/upload/v1779664061/Parkir-khusus-difabel_qkxvmp.jpg?v=40',
        'latitude' => -7.764421,
        'longitude' => 110.391991,
        'timestamp' => now()->subDays(30),
        'details' => [['Parkir Difabel', false]],
        'validation' => ['approval_status' => 'rejected', 'evaluation_note' => 'Area parkir disalahgunakan, laporan ditolak', 'validation_date' => now()->subDays(29)],
        'map' => null,
      ]
    ];

    foreach ($historyReportsData as $reportData) {
      $report = Report::updateOrCreate(
        ['photo_url' => $reportData['photo_url']],
        [
          'user_id' => $riwayatUser->id,
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
