<?php

namespace Database\Seeders;

use App\Models\SPST;
use Illuminate\Database\Seeder;

class SPSTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spsts = [
            [
                'name' => 'ASTBTP MARSEILLE-MICHELET',
                'address' => '344 bd Michelet',
                'postal_code' => '13009',
                'city' => 'MARSEILLE',
                'phone' => '04 91 23 03 30',
                'url' => 'https://www.astbtp.fr',
                'message' => 'Service disponible 24/7'
            ],
            [
                'name' => 'ASTBTP LYON',
                'address' => '23 Avenue Jean Jaurès',
                'postal_code' => '69007',
                'city' => 'LYON',
                'phone' => '04 72 78 58 50',
                'url' => 'https://www.astbtp.fr',
                'message' => 'Horaires: Lundi-Vendredi 8h-17h'
            ],
            [
                'name' => 'ASTBTP PARIS',
                'address' => '110 Avenue de la République',
                'postal_code' => '75011',
                'city' => 'PARIS',
                'phone' => '01 43 70 37 61',
                'url' => 'https://www.astbtp.fr',
                'message' => 'Service sur rendez-vous uniquement'
            ],
        ];

        foreach ($spsts as $spstData) {
            SPST::create($spstData);
        }
    }
}