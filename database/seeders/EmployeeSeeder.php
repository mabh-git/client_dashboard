<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $clients = Client::all();

        $departments = ['IT', 'Finances', 'RH', 'Marketing', 'Ventes', 'Créatif', 'Management', 'Production'];
        $contractTypes = ['CDI', 'CDD', 'Intérim', 'Apprentissage', 'Stage'];
        $roles = ['Développeur', 'Ingénieur', 'Manager', 'Assistant', 'Directeur', 'Comptable', 'Technicien', 'Commercial'];
        $surveillance = ['Standard', 'Renforcée', 'Adaptée', 'Particulière'];

        foreach ($clients as $client) {
            // Create between 5 and 20 employees per client
            $employeesCount = $faker->numberBetween(5, 20);

            for ($i = 0; $i < $employeesCount; $i++) {
                Employee::create([
                    'client_id' => $client->id,
                    'name' => $faker->name(),
                    'matricule' => str_pad($faker->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
                    'gender' => $faker->randomElement(['M', 'F']),
                    'birthdate' => $faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
                    'contractType' => $faker->randomElement($contractTypes),
                    'startDate' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                    'spst' => $faker->randomElement(['SIR A', 'SIR B', 'SIA', 'SIG']),
                    'role' => $faker->randomElement($roles),
                    'poste' => $faker->jobTitle(),
                    'departement' => $faker->randomElement($departments),
                    'surveillance' => $faker->randomElement($surveillance),
                    'pcsCode' => $faker->numerify('PCS###'),
                    'is_active' => $faker->boolean(90), // 90% chance of being active
                ]);
            }
        }
    }
}