<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Faker\Factory as Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker =Factory::create();
        $companies = Company::all();
        foreach ($companies as $key => $company) {
            foreach ($company->departments as $department) {
                foreach ($department->designations as $key => $designation) {
                    for ($i = 0; $i < 3; $i++) {
                        Employee::create([
                            'company_id' => $company->id, // ADD THIS LINE
                            'designation_id' => $designation->id,
                            'name' => $faker->firstName . ' ' . $faker->lastName . ' (Kenyan)',
                            'email' => $faker->unique()->safeEmail(),
                            'phone' => $faker->numerify('07########'), // Kenyan mobile format
                            'address' => $faker->city() . ', Kenya',
                        ]);
                    }
                }
            }
        }
    }
}
