<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Company;
use Carbon\Carbon;

class BurnoutTestAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->error('No companies found.');
            return;
        }

        $this->command->info('--- Creating burnout test attendance data ---');

        $startDate = Carbon::parse('2025-11-01');
        $endDate   = Carbon::parse('2025-11-24');

        // Clear existing data for the period
        Attendance::whereBetween('date', [$startDate, $endDate])->delete();

        foreach ($companies as $company) {
            $this->command->info("Processing company: {$company->name}");

            $employees = Employee::whereHas('designation.department', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->get();

            if ($employees->isEmpty()) {
                $this->command->warn("No employees in {$company->name}");
                continue;
            }

            foreach ($employees as $i => $employee) {
                $this->createEmployeeAttendance(
                    employee: $employee,
                    index: $i,
                    startDate: $startDate->copy(),
                    endDate: $endDate->copy()
                );
            }
        }

        $this->command->info('✓ Burnout test data created for all companies.');
    }

    private function createEmployeeAttendance($employee, $index, $startDate, $endDate)
    {
        $patterns = [
            ['label'=>'Normal','in'=>'08:30','out'=>'17:00','overtime'=>0.0,'absent'=>2, 'base_hours'=>8.5],
            ['label'=>'Moderate OT','in'=>'08:00','out'=>'18:00','overtime'=>0.3,'absent'=>1, 'base_hours'=>10],
            ['label'=>'High Burnout','in'=>'07:30','out'=>'20:00','overtime'=>0.6,'absent'=>0, 'base_hours'=>12.5],
            ['label'=>'Extreme Burnout','in'=>'06:00','out'=>'21:00','overtime'=>0.8,'absent'=>0, 'base_hours'=>15],
            ['label'=>'Part-time','in'=>'09:00','out'=>'14:00','overtime'=>0.0,'absent'=>3, 'base_hours'=>5],
            ['label'=>'Late Overtime','in'=>'10:00','out'=>'19:00','overtime'=>0.4,'absent'=>1, 'base_hours'=>9],
            ['label'=>'Early Bird','in'=>'06:30','out'=>'15:30','overtime'=>0.1,'absent'=>2, 'base_hours'=>9],
            ['label'=>'Variable','in'=>'08:00','out'=>'17:30','overtime'=>0.5,'absent'=>2, 'base_hours'=>9.5],
        ];

        $pattern = $patterns[$index % count($patterns)];

        $allDates = [];
        $loopDate = $startDate->copy();

        while ($loopDate <= $endDate) {
            if (!$loopDate->isWeekend()) {
                $allDates[] = $loopDate->copy();
            }
            $loopDate->addDay();
        }

        shuffle($allDates);
        $absentDates = array_slice($allDates, 0, $pattern['absent']);

        $totalHours = 0;
        $workDays = 0;

        foreach ($allDates as $date) {
            $isAbsent = collect($absentDates)->contains(fn($d) => $d->isSameDay($date));

            if ($isAbsent) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'status' => 'absent',
                    'hours_worked' => 0,
                    'overtime' => false,
                    'overtime_hours' => 0,
                    'is_late' => false,
                ]);
                continue;
            }

            // Calculate hours for this day
            $baseHours = $pattern['base_hours'];
            
            // Add overtime based on pattern
            $finalHours = $baseHours;
            if (rand(1, 100) <= ($pattern['overtime'] * 100)) {
                $finalHours += rand(1, 4); // Add 1-4 hours overtime
            }

            // Ensure hours are positive and realistic
            $finalHours = max(0, min(24, $finalHours));

            // Create check-in/check-out times based on hours
            $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $pattern['in']);
            $checkOut = $checkIn->copy()->addHours($finalHours);

            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'present',
                'hours_worked' => $finalHours,
                'overtime_hours' => max(0, $finalHours - 8),
                'overtime' => $finalHours > 8,
                'is_late' => false, // You can add late logic here
            ]);

            $totalHours += $finalHours;
            $workDays++;
        }

        $avg = $workDays ? round($totalHours / $workDays, 1) : 0;

        // Calculate weekly hours for burnout status
        $lastAttendance = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->first();

        $burnoutStatus = $lastAttendance ? $lastAttendance->burnoutStatus() : ['hours' => 0, 'level' => 'Unknown'];

        $this->command->info(
            "✓ {$employee->name} → {$pattern['label']} | {$workDays} days | {$totalHours} hrs | avg {$avg}/day"
        );

        $this->command->warn("   ⇒ Weekly Burnout Status: {$burnoutStatus['level']} ({$burnoutStatus['hours']} hrs)");
    }
}