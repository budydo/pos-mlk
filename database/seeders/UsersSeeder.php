<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Pemilik (owner)
        User::factory()->pemilik()->create([
            'name' => 'Pemilik POS-MLK',
            'email' => 'owner@pos-mlk.test',
            'password' => bcrypt('password'),
        ]);

        // Karyawan contoh
        User::factory()->count(2)->create([
            'password' => bcrypt('password'),
        ]);
    }
}
