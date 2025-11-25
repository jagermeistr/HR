<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a default user (admin) to satisfy foreign key constraints
        // \App\Models\User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        // ]);

        $this->call([
            CompaniesSeeder::class,
            DepartmentsSeeder::class,
            EmployeesSeeder::class,
            ContractsSeeder::class,
            CompanySettingSeeder::class,
            FarmerSeeder::class,
            CollectionCenterSeeder::class,
            ProductionRecordSeeder::class,
            BurnoutTestAttendanceSeeder::class,
            LeaveTypeSeeder::class,
        ]);
    }
}
