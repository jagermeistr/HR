<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'Kericho Tea Factory',
                'email' => 'info@kerichoteafactory.co.ke',
                'website' => 'https://kerichoteafactory.co.ke',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nandi Hills Tea Factory',
                'email' => 'contact@nandihillsteafactory.co.ke',
                'website' => 'https://nandihillsteafactory.co.ke',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kisii Tea Factory',
                'email' => 'admin@kisiiteafactory.co.ke',
                'website' => 'https://kisiiteafactory.co.ke',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Limuru Tea Factory',
                'email' => 'support@limuruteafactory.co.ke',
                'website' => 'https://limuruteafactory.co.ke',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        foreach(Company::all() as $key => $company) {
            $company->users()->attach(ids: 1); // Attach the first user (admin)
        };
    }
}
