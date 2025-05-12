<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $employees = Employee::all();

        $visitTypes = ['VIPP', 'VIPI', 'VIP', 'VMS'];
        $visitStates = ['Programmée', 'Effectuée', 'Annulée', 'Reportée'];
        $suiviTypes = ['SIR A', 'SIR B', 'SIA', 'SIG'];

        // Get current date
        $currentDate = Carbon::now();

        foreach ($employees as $employee) {
            // Create between 1 and 5 visits per employee
            $visitCount = $faker->numberBetween(1, 5);

            for ($i = 0; $i < $visitCount; $i++) {
                $etat = $faker->randomElement($visitStates);

                // Generate a random date between 1 year ago and 6 months in future
                $envisagee = $faker->dateTimeBetween('-1 year', '+6 months')->format('Y-m-d');

                // If status is "Effectuée", set completion date between envisagee and now
                $effectuee = null;
                if ($etat === 'Effectuée') {
                    // Convert envisagee to DateTime object for comparison
                    $envisageeDate = Carbon::parse($envisagee);

                    // If envisagee is in the past, effectuee date can be between envisagee and now
                    if ($envisageeDate->lt($currentDate)) {
                        $effectuee = $faker->dateTimeBetween($envisagee, 'now')->format('Y-m-d');
                    } else {
                        // If envisagee is in the future, set state to "Programmée" instead
                        $etat = 'Programmée';
                    }
                }

                Visit::create([
                    'employee_id' => $employee->id,
                    'type' => $faker->randomElement($visitTypes),
                    'etat' => $etat,
                    'envisagee' => $envisagee,
                    'effectuee' => $effectuee,
                    'suivi' => $faker->randomElement($suiviTypes),
                    'apte' => $faker->boolean(80), // 80% chance of being fit for work
                    'observations' => $faker->optional(0.7)->paragraph(), // 70% chance of having observations
                ]);
            }
        }
    }
}