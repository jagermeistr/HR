<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Attendance History -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <!-- Header -->
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Attendance History & Burnout Analysis</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">View detailed attendance records and burnout risk reports</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee</label>
                        <select
                            wire:model.live="employeeId"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->name }} - {{ $employee->designation->name ?? 'No Designation' }}
                            </option>
                            @endforeach
                        </select>
                        @error('employeeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input
                            type="date"
                            wire:model.live="startDate"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        @error('startDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input
                            type="date"
                            wire:model.live="endDate"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        @error('endDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                        </select>
                        @error('statusFilter') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-end space-x-4">
                        <button
                            wire:click="applyFilters"
                            class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            Apply
                        </button>
                        <button
                            wire:click="clearFilters"
                            class="w-full bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                            Clear
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button
                            wire:click="exportToCsv"
                            wire:loading.attr="disabled"
                            class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors disabled:opacity-50">
                            <span wire:loading.remove>Export CSV</span>
                            <span wire:loading>Exporting...</span>
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-2 md:grid-cols-7 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-800 dark:text-blue-300">Total Records</div>
                        <div class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ $summary['total_records'] ?? 0 }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-800 dark:text-green-300">Present</div>
                        <div class="text-xl font-bold text-green-900 dark:text-green-100">{{ $summary['present_days'] ?? 0 }}</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-red-800 dark:text-red-300">Absent</div>
                        <div class="text-xl font-bold text-red-900 dark:text-red-100">{{ $summary['absent_days'] ?? 0 }}</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Late</div>
                        <div class="text-xl font-bold text-yellow-900 dark:text-yellow-100">{{ $summary['late_days'] ?? 0 }}</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-purple-800 dark:text-purple-300">Total Hours</div>
                        <div class="text-xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($summary['total_hours'] ?? 0, 1) }}</div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-orange-800 dark:text-orange-300">Overtime Hours</div>
                        <div class="text-xl font-bold text-orange-900 dark:text-orange-100">{{ number_format($summary['overtime_hours'] ?? 0, 1) }}</div>
                    </div>
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-indigo-800 dark:text-indigo-300">Avg Hours/Day</div>
                        <div class="text-xl font-bold text-indigo-900 dark:text-indigo-100">{{ number_format($summary['average_hours'] ?? 0, 1) }}</div>
                    </div>
                </div>

                <!-- Burnout Analysis Section -->
                <div class="mb-6 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-300 mb-4">Burnout Risk Analysis</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-orange-700 dark:text-orange-400 mb-2">Weekly Hours Distribution</h4>
                            <div class="space-y-2">
                                @php
                                $settings = app(\App\Models\CompanySetting::class)->first();
                                $burnoutThreshold = $settings->burnout_threshold ?? 48;

                                // Get unique employees with their weekly hours
                                $employeeWeeklyHours = [];
                                $employeeIds = $attendanceHistory->pluck('employee_id')->unique();

                                foreach($employeeIds as $employeeId) {
                                    $record = $attendanceHistory->where('employee_id', $employeeId)->first();
                                    if ($record) {
                                        $weeklyHours = $record->weekly_hours ?? $record->getWeeklyHours();
                                        $employeeWeeklyHours[$employeeId] = [
                                            'name' => $record->employee->name,
                                            'weekly_hours' => $weeklyHours,
                                            'is_risk' => $weeklyHours > $burnoutThreshold
                                        ];
                                    }
                                }

                                // Sort by hours (highest first)
                                uasort($employeeWeeklyHours, fn($a, $b) => $b['weekly_hours'] <=> $a['weekly_hours']);
                                $atRiskCount = count(array_filter($employeeWeeklyHours, fn($emp) => $emp['is_risk']));
                                @endphp

                                @foreach(array_slice($employeeWeeklyHours, 0, 5) as $employee)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[120px]" title="{{ $employee['name'] }}">
                                        {{ $employee['name'] }}
                                    </span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full @if($employee['is_risk']) bg-red-500 @else bg-green-500 @endif"
                                                @style(['width' => min(100, ($employee['weekly_hours'] / 80) * 100) . '%'])>
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium @if($employee['is_risk']) text-red-600 dark:text-red-400 @else text-green-600 dark:text-green-400 @endif">
                                            {{ number_format($employee['weekly_hours'], 1) }}h
                                        </span>
                                    </div>
                                </div>
                                @endforeach

                                @if(count($employeeWeeklyHours) > 5)
                                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                    +{{ count($employeeWeeklyHours) - 5 }} more employees
                                </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-orange-700 dark:text-orange-400 mb-2">Risk Summary</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Employees at Risk</span>
                                    <span class="text-lg font-bold @if($atRiskCount > 0) text-red-600 dark:text-red-400 @else text-green-600 dark:text-green-400 @endif">
                                        {{ $atRiskCount }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Safe Employees</span>
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                        {{ count($employeeWeeklyHours) - $atRiskCount }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Risk Threshold</span>
                                    <span class="text-sm font-medium text-orange-600 dark:text-orange-400">{{ $burnoutThreshold }} hours/week</span>
                                </div>
                            </div>

                            @if($atRiskCount > 0)
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-red-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <div class="text-sm text-red-700 dark:text-red-300">
                                        <strong>{{ $atRiskCount }} employee(s)</strong> are working beyond the recommended weekly hours ({{ $burnoutThreshold }}h). Consider workload adjustments.
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Attendance History Table -->
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="shadow border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check In</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check Out</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Overtime</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Burnout Risk</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($attendanceHistory as $record)
                                    @php
                                    $settings = app(\App\Models\CompanySetting::class)->first();
                                    $burnoutThreshold = $settings->burnout_threshold ?? 48;
                                    $weeklyHours = $record->weekly_hours ?? $record->getWeeklyHours();
                                    $isBurnoutRisk = $record->is_burnout_risk ?? ($weeklyHours > $burnoutThreshold);
                                    @endphp
                                    <tr class="@if($isBurnoutRisk) bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 @else hover:bg-gray-50 dark:hover:bg-gray-700 @endif transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $record->date->format('M d, Y') }}
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $record->date->format('l') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $record->employee->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $record->employee->designation->name ?? 'No Designation' }}
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $record->employee->designation->department->name ?? 'No Department' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($record->check_in)
                                            <div class="font-medium">{{ $record->check_in->format('h:i A') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                @if($record->is_late)
                                                <span class="text-red-600 dark:text-red-400">Late</span>
                                                @else
                                                On Time
                                                @endif
                                            </div>
                                            @else
                                            <span class="text-gray-400">--:--</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($record->check_out)
                                            <div class="font-medium">{{ $record->check_out->format('h:i A') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Checked Out</div>
                                            @else
                                            <span class="text-gray-400">--:--</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div class="font-medium">{{ number_format($record->hours_worked, 1) }} hrs</div>
                                            @if($record->overtime)
                                            <div class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                                +{{ number_format($record->overtime_hours, 1) }} OT
                                            </div>
                                            @endif
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Weekly: {{ number_format($weeklyHours, 1) }}h
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                            $statusColors = [
                                            'present' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'absent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'late' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'half_day' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                            ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($record->overtime)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                Yes
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                                No
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($isBurnoutRisk)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                ⚠️ At Risk
                                            </span>
                                            <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                                                +{{ number_format($weeklyHours - $burnoutThreshold, 1) }}h over
                                            </div>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                ✅ Safe
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No attendance records found for the selected criteria.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            @if($attendanceHistory->hasPages())
                            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                                {{ $attendanceHistory->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                @if($attendanceHistory->count() === 0)
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No attendance records found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4 max-w-md mx-auto">
                        @if($employeeId || $statusFilter || $startDate != now()->subDays(30)->format('Y-m-d') || $endDate != now()->format('Y-m-d'))
                        Try adjusting your filters or select a different date range.
                        @else
                        No attendance data available for the selected period.
                        @endif
                    </p>
                    <div class="space-x-3">
                        <button wire:click="clearFilters" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            Clear All Filters
                        </button>
                        <button onclick="location.reload()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                            Refresh Page
                        </button>
                    </div>
                </div>
                @endif

                <!-- Loading State -->
                <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="text-gray-700 dark:text-gray-300">Loading attendance data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>