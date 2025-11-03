<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $departments = $company->departments()->createMany([
                ['name' => 'Engineering'],
                ['name' => 'Human Resource'],
                ['name' => 'Finance'],
                ['name' => 'Marketing'],
                //['name' => 'Sorting'],
            ]);


            foreach ($departments as $department) {
                switch ($department->name) {
                    case 'Engineering':
                        $designations = [
                            'Software Engineer',
                            'Senior Software Engineer',
                            'Engineering Manager',
                            'Director of Engineering',
                        ];
                        break;
                    case 'Human Resource':
                        $designations = [
                            'HR Assistant',
                            'HR Manager',
                            'Recruiter',
                            'Director of HR',
                        ];
                        break;
                    case 'Finance':
                        $designations = [
                            'Accountant',
                            'Finance Manager',
                            'Financial Analyst',
                            'Director of Finance',
                        ];
                        break;
                    case 'Marketing':
                        $designations = [
                            'Marketing Coordinator',
                            'Marketing Manager',
                            'Brand Strategist',
                            'Director of Marketing',
                        ];
                        break;

                    default:
                        $designations = [];
                        break;
                }
                foreach ($designations as $designation) {
                    $department->designations()->create(
                        [
                            'name' => $designation,
                        ]
                    );
                }
            }
        }
    }
}
