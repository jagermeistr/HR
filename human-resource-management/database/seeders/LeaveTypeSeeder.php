<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'days_per_year' => 21,
                'description' => 'Paid time off work granted by employers to employees',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'days_per_year' => 14,
                'description' => 'Leave for personal illness or medical appointments',
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'days_per_year' => 84,
                'description' => 'Leave for expecting mothers before and after childbirth',
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'days_per_year' => 7,
                'description' => 'Leave for new fathers around the time of childbirth',
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Leave',
                'days_per_year' => 5,
                'description' => 'Leave for unexpected personal or family emergencies',
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'days_per_year' => 0,
                'description' => 'Leave without pay for personal reasons',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }
    }
}