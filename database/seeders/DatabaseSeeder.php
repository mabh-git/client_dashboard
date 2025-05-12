<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SPSTSeeder::class,
            ClientSeeder::class,
            EmployeeSeeder::class,
            VisitSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}