<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\Rendezvous;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Initialize Faker
        $faker = Faker::create();

        // Seed users
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

        // Seed Patients
        for ($i = 0; $i < 5; $i++) {
            Patient::create([
                'cin' => $faker->unique()->numerify('########'),
                'nom' => $faker->name(),
                'sexe' => $faker->randomElement(['M', 'F']),
                'date_naissance' => $faker->date(),
                'contact' => $faker->phoneNumber(),
            ]);
        }

        // Seed Rendezvous
        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();
        $secretaires = User::where('role', 'secretaire')->get();

        for ($i = 0; $i < 5; $i++) {
            Rendezvous::create([
                'patient_id' => $patients->random()->id,
                'medecin_id' => $medecins->random()->id,
                'secretaire_id' => $secretaires->random()->id,
                'date' => $faker->dateTimeThisYear(),
                'statut' => $faker->randomElement(['en attente', 'confirmé', 'annulé']),
                'motif' => $faker->sentence(),
            ]);
        }

        // Seed Factures
        for ($i = 0; $i < 5; $i++) {
            Facture::create([
                'patient_id' => $patients->random()->id,
                'medecin_id' => $medecins->random()->id,
                'secretaire_id' => $secretaires->random()->id,
                'montant' => $faker->randomFloat(2, 50, 500),
                'statut' => $faker->randomElement(['payée', 'en attente']),
                'date' => $faker->date(),
                'utilisateur_id' => $secretaires->random()->id,
            ]);
        }

        // Seed Paiements
        $factures = Facture::all();
        for ($i = 0; $i < 5; $i++) {
            Paiement::create([
                'facture_id' => $factures->random()->id,
                'montant' => $faker->randomFloat(2, 50, 500),
                'date_paiement' => $faker->date(),
                'mode_paiement' => $faker->randomElement(['cash', 'carte', 'virement']),
                'statut' => $faker->randomElement(['effectué', 'en attente']),
            ]);
        }
    }
}