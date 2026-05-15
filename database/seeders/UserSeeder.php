<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Seed the default admin user.
   */
  public function run(): void
  {
    User::updateOrCreate(
      ['email' => 'admin@sleman-akses.id'],
      [
        'full_name' => 'Admin Sleman Akses',
        'password' => Hash::make('admin12345'),
        'role' => 'admin',
      ]
    );
  }
}
