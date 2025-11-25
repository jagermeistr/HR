<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanySetting;
use App\Models\Company;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->error('No companies found. Please create companies first.');
            return;
        }

        foreach ($companies as $company) {
            // Create company settings for each company if they don't exist
            if (CompanySetting::where('company_id', $company->id)->count() === 0) {
                CompanySetting::create([
                    'company_id' => $company->id,
                    'late_threshold' => '09:15:00',
                    'regular_hours' => 8.00,
                    'burnout_threshold' => 48,
                    'work_start_time' => '09:00:00',
                    'work_end_time' => '17:00:00',
                ]);
                
                $this->command->info("✓ Company settings created for {$company->name}");
            } else {
                $this->command->info("✓ Company settings already exist for {$company->name}");
            }
        }

        $this->command->info('Company settings created for all companies!');
    }
}