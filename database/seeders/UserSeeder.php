<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel users terlebih dahulu
        User::query()->delete();

        // Buat Super Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@perpustakaan.app',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Buat contoh Pustakawan (Librarian)
        User::create([
            'name' => 'Pustakawan A',
            'email' => 'librarian@perpustakaan.app',
            'password' => Hash::make('password'),
            'role' => 'librarian',
            'email_verified_at' => now(),
        ]);

        // Buat contoh Anggota (Member)
        User::create([
            'name' => 'Anggota B',
            'email' => 'member@perpustakaan.app',
            'password' => Hash::make('password'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);
    }
}
