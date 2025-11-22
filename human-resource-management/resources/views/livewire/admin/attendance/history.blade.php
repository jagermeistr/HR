<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Attendance History -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <!-- Header -->
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Attendance History</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">View detailed attendance records and reports</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee</label>
                        <select 
                            wire:model.live="employeeId"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }} - {{ $employee->designation->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input 
                            type="date" 
                            wire:model.live="startDate"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input 
                            type="date" 
                            wire:model.live="endDate"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select 
                            wire:model.live="statusFilter"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button 
                            wire:click="exportToCsv"
                            class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors"
                        >
                            Export CSV
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-800 dark:text-blue-300">Total Days</div>
                        <div class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ $summary['total_days'] }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-800 dark:text-green-300">Present</div>
                        <div class="text-xl font-bold text-green-900 dark:text-green-100">{{ $summary['present_days'] }}</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-red-800 dark:text-red-300">Absent</div>
                        <div class="text-xl font-bold text-red-900 dark:text-red-100">{{ $summary['absent_days'] }}</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Late</div>
                        <div class="text-xl font-bold text-yellow-900 dark:text-yellow-100">{{ $summary['late_days'] }}</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-purple-800 dark:text-purple-300">Total Hours</div>
                        <div class="text-xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($summary['total_hours'], 1) }}</div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-orange-800 dark:text-orange-300">Overtime Hours</div>
                        <div class="text-xl font-bold text-orange-900 dark:text-orange-100">{{ number_format($summary['overtime_hours'], 1) }}</div>
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($attendanceHistory as $record)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
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
                                                {{ $record->employee->designation->name }}
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $record->employee->designation->department->name ?? 'No Department' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($record->check_in)
                                                <div class="font-medium">{{ $record->check_in->format('h:i A') }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Checked In</div>
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
                                                +{{ number_format($record->hours_worked - 8, 1) }} OT
                                            </div>
                                            @endif
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
                                            <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full {{ $statusColors[$record->status] }}">
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                                {{ $attendanceHistory->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                @if($attendanceHistory->count() === 0)
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No attendance records found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or date range.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>