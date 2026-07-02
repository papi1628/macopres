<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DirecteurSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['login' => 'pdg'],
            [
                'password' => Hash::make('12345678'),
                'role' => 'directeur',
            ]
        );
    }
}