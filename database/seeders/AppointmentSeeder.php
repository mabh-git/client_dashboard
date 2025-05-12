<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $employees = Employee::all();
        $currentDate = Carbon::now();

        foreach ($employees as $employee) {
            // Create between 0 and 3 appointments per employee
            $appointmentCount = $faker->numberBetween(0, 3);

            for ($i = 0; $i < $appointmentCount; $i++) {
                // Set date to be between 3 months ago and 3 months in the future
                $date = $faker->dateTimeBetween('-3 months', '+3 months');
                
                // Ensure envoi date is before appointment date
                $envoi = $faker->dateTimeBetween('-4 months', $date);
                
                // Determine if appointment is in the past
                $isPast = Carbon::parse($date)->lt($currentDate);
                
                // For past appointments, set honore, reporte, etc. based on real values
                // For future appointments, these will all be false or null
                $accepte = $isPast ? $faker->boolean(80) : $faker->boolean(20);
                $honore = $isPast && $accepte ? $faker->boolean(90) : false;
                $reporte = $isPast && !$honore ? $faker->boolean(60) : false;
                $excusable = $isPast && !$honore ? $faker->boolean(50) : false;

                Appointment::create([
                    'employee_id' => $employee->id,
                    'date' => $date->format('Y-m-d H:i:s'),
                    'envoi' => $envoi->format('Y-m-d'),
                    'ar' => $faker->boolean(60), // 60% chance of acknowledgement receipt
                    'ordonnance' => $faker->boolean(40), // 40% chance of having a prescription
                    'accepte' => $accepte,
                    'excusable' => $excusable,
                    'reporte' => $reporte,
                    'honore' => $honore,
                    'motif' => $faker->optional(0.7)->sentence(), // 70% chance of having a reason
                    'commentaire' => $faker->optional(0.5)->paragraph(), // 50% chance of having comments
                ]);
            }
        }
    }
}