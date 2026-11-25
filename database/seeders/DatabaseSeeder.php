<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sekretariat.go.id'],
            [
                'name' => 'Raden Ayu Prameswari',
                'position' => 'Kepala Sekretariat',
                'phone' => '0812-3456-7890',
                'role' => 'admin',
                'password' => 'password',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
