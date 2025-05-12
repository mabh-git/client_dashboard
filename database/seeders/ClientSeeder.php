<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        
        // Generate clients with standardized names as requested
        for ($i = 1; $i <= 10; $i++) {
            Client::create([
                'name' => "Client{$i}",
                'description' => $faker->paragraph(),
                'logo_path' => 'assets/logos/client.png', // Same logo path for all clients as requested
                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'postal_code' => $faker->postcode(),
                'phone' => $faker->phoneNumber(),
                'email' => "contact@client{$i}.com",
                'website' => "www.client{$i}.com",
                'contact_person' => $faker->name(),
                'contact_email' => "manager@client{$i}.com",
                'contact_phone' => $faker->phoneNumber(),
                'is_active' => true,
                'is_favorite' => $faker->boolean(30),
                'contact' => [
                    'address' => $faker->streetAddress(),
                    'city' => $faker->city(),
                    'phone' => $faker->phoneNumber(),
                    'email' => "contact@client{$i}.com",
                    'website' => "www.client{$i}.com",
                    'person' => $faker->name(),
                    'assistant' => $faker->boolean(70) ? $faker->name() : null,
                ],
                'indicators' => [
                    'programmees' => $faker->numberBetween(100, 200),
                    'suspendues' => $faker->numberBetween(50, 100),
                    'sensibles' => $faker->numberBetween(5, 20),
                    'sans_as' => $faker->numberBetween(5, 20),
                    'mouvements' => $faker->numberBetween(0, 15),
                    'jours_retards' => $faker->numberBetween(0, 10),
                    'etablissements_inconnus' => $faker->numberBetween(0, 8),
                    'imports_en_attente' => $faker->numberBetween(0, 5),
                    'factures_en_attente' => $faker->numberBetween(0, 8),
                    'factures_rapprochement' => $faker->numberBetween(0, 4),
                    'rejet_import' => $faker->numberBetween(0, 3),
                ],
            ]);
        }
    }
}