<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\CompanySetting;
use Carbon\Carbon;

class BurnoutTestSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create company settings
        $settings = CompanySetting::firstOrCreate([], [
            'company_id' => 1,
            'late_threshold' => '09:15:00',
            'regular_hours' => 8.00,
            'burnout_threshold' => 40,
            'work_start_time' => '09:00:00',
            'work_end_time' => '17:00:00',
        ]);

        $employees = Employee::inCompany()->get();
        
        if ($employees->isEmpty()) {
            $this->command->error('No employees found. Please run Employee seeder first.');
            return;
        }

        $this->command->info('Creating burnout test attendance data from Nov 1-24, 2025...');
        
        // Define date range: November 1-24, 2025
        $startDate = Carbon::create(2025, 11, 1); // November 1, 2025
        $endDate = Carbon::create(2025, 11, 24);  // November 24, 2025
        
        // Clear existing attendance for this period
        Attendance::whereBetween('date', [$startDate, $endDate])->delete();

        // Create attendance data for each employee
        foreach ($employees as $index => $employee) {
            $this->createEmployeeAttendance($employee, $index, $startDate, $endDate);
        }

        $this->command->info('Burnout test attendance data created successfully!');
        $this->command->info("Date range: {$startDate->format('M d, Y')} to {$endDate->format('M d, Y')}");
        $this->command->info('Burnout threshold: ' . $settings->burnout_threshold . ' hours per week');
    }

    private function createEmployeeAttendance($employee, $index, $startDate, $endDate)
    {
        // Different work patterns to test various scenarios
        $workPatterns = [
            // Pattern 0: Consistent normal worker
            [
                'check_in' => '08:30',
                'check_out' => '17:00',
                'overtime_factor' => 0, // No overtime
                'absent_days' => 2, // 2 absent days in the period
                'description' => 'Consistent Normal'
            ],
            // Pattern 1: Moderate overtime worker
            [
                'check_in' => '08:00',
                'check_out' => '18:00', 
                'overtime_factor' => 0.3, // 30% chance of extra overtime
                'absent_days' => 1,
                'description' => 'Moderate Overtime'
            ],
            // Pattern 2: High burnout risk
            [
                'check_in' => '07:30',
                'check_out' => '20:00',
                'overtime_factor' => 0.6, // 60% chance of extra overtime
                'absent_days' => 0,
                'description' => 'High Burnout Risk'
            ],
            // Pattern 3: Extreme burnout (critical risk)
            [
                'check_in' => '06:00',
                'check_out' => '21:00',
                'overtime_factor' => 0.8, // 80% chance of extra overtime
                'absent_days' => 0,
                'description' => 'Extreme Burnout'
            ],
            // Pattern 4: Part-time worker
            [
                'check_in' => '09:00',
                'check_out' => '14:00',
                'overtime_factor' => 0,
                'absent_days' => 3,
                'description' => 'Part-time'
            ],
            // Pattern 5: Late starter with overtime
            [
                'check_in' => '10:00',
                'check_out' => '19:00',
                'overtime_factor' => 0.4,
                'absent_days' => 1,
                'description' => 'Late Starter Overtime'
            ],
            // Pattern 6: Early bird consistent
            [
                'check_in' => '06:30',
                'check_out' => '15:30',
                'overtime_factor' => 0.1,
                'absent_days' => 2,
                'description' => 'Early Bird'
            ],
            // Pattern 7: Variable schedule
            [
                'check_in' => '08:00',
                'check_out' => '17:30',
                'overtime_factor' => 0.5,
                'absent_days' => 2,
                'description' => 'Variable Schedule'
            ],
        ];

        // Use modulo to cycle through patterns
        $patternIndex = $index % count($workPatterns);
        $pattern = $workPatterns[$patternIndex];

        $currentDate = $startDate->copy();
        $totalDays = 0;
        $workDays = 0;
        $totalHours = 0;

        // Select random absent days
        $absentDays = [];
        $allDays = [];
        while ($currentDate <= $endDate) {
            $allDays[] = $currentDate->copy();
            $currentDate->addDay();
        }
        shuffle($allDays);
        $absentDays = array_slice($allDays, 0, $pattern['absent_days']);

        // Reset current date
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== Carbon::SATURDAY && $currentDate->dayOfWeek !== Carbon::SUNDAY) {
                
                // Check if this is an absent day
                $isAbsent = false;
                foreach ($absentDays as $absentDay) {
                    if ($absentDay->isSameDay($currentDate)) {
                        $isAbsent = true;
                        break;
                    }
                }

                if (!$isAbsent) {
                    $checkIn = $pattern['check_in'];
                    $checkOut = $pattern['check_out'];
                    
                    // Apply overtime variation
                    if ($pattern['overtime_factor'] > 0 && rand(1, 100) <= ($pattern['overtime_factor'] * 100)) {
                        // Add random overtime (1-3 hours)
                        $overtimeHours = rand(1, 3);
                        $checkOutTime = Carbon::createFromTimeString($checkOut);
                        $checkOutTime->addHours($overtimeHours);
                        $checkOut = $checkOutTime->format('H:i');
                    }

                    // Random late arrival (10% chance)
                    if (rand(1, 100) <= 10) {
                        $checkInTime = Carbon::createFromTimeString($checkIn);
                        $checkInTime->addMinutes(rand(15, 90)); // 15-90 minutes late
                        $checkIn = $checkInTime->format('H:i');
                    }

                    $attendance = Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $currentDate,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => 'present',
                    ]);

                    // Calculate hours worked
                    $attendance->calculateHoursWorked();
                    $totalHours += $attendance->hours_worked;
                    $workDays++;
                } else {
                    // Create absent record
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $currentDate,
                        'status' => 'absent',
                        'hours_worked' => 0,
                        'overtime' => false,
                        'overtime_hours' => 0,
                        'is_late' => false,
                    ]);
                }
                
                $totalDays++;
            }

            $currentDate->addDay();
        }

        $avgDailyHours = $workDays > 0 ? round($totalHours / $workDays, 1) : 0;
        $this->command->info("✓ {$employee->name}: {$pattern['description']} - {$workDays} work days, {$totalHours} total hours ({$avgDailyHours} avg/day)");
    }
}