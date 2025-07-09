<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'nom' => 'Admin',
                'Cin' => 'AA111111',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin@123'),
                'role' => 'admin',
            ],
            [
                'nom' => 'Reda',
                'Cin' => 'BB222222',
                'email' => 'reda@gmail.com',
                'password' => Hash::make('reda@123'),
                'role' => 'medecin',
            ],
            [
                'nom' => 'Hamza',
                'Cin' => 'BB333333',
                'email' => 'hamza@gmail.com',
                'password' => Hash::make('hamza@123'),
                'role' => 'medecin',
            ],
            [
                'nom' => 'Youssef',
                'Cin' => 'CC444444',
                'email' => 'youssef@gmail.com',
                'password' => Hash::make('youssef@123'),
                'role' => 'secretaire',
            ],
            [
                'nom' => 'Haitam',
                'Cin' => 'CC555555',
                'email' => 'haitam@gmail.com',
                'password' => Hash::make('haitam@123'),
                'role' => 'secretaire',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
