<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
  /**
   * Seed the facility categories.
   */
  public function run(): void
  {
    $categories = [
      ['facility_name' => 'Ramp', 'icon_marker' => 'ramp.png'],
      ['facility_name' => 'Toilet Difabel', 'icon_marker' => 'toilet-difabel.png'],
      ['facility_name' => 'Parkir Difabel', 'icon_marker' => 'parkir-difabel.png'],
      ['facility_name' => 'Lift', 'icon_marker' => 'lift.png'],
    ];

    foreach ($categories as $category) {
      FacilityCategory::updateOrCreate(
        ['facility_name' => $category['facility_name']],
        $category
      );
    }
  }
}
