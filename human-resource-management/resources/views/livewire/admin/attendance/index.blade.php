<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Attendance Management -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                <!-- Header -->
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Attendance Management</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                        Track employee attendance and monitor burnout risks
                    </p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date
                        </label>
                        <input
                            type="date"
                            wire:model.live="selectedDate"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search Employee
                        </label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search employees..."
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                    </div>

                    <!-- Burnout Filter
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model.live="burnoutFilter"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Show Burnout Risks Only
                            </span>
                        </label>
                    </div> -->

                    <!-- Settings Info -->
                    <div class="flex items-end">
                        <div class="bg-blue-100 dark:bg-blue-900/30 px-3 py-2 rounded-md w-full">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                Late After:
                                @if($settings && $settings->late_threshold)
                                {{ \Carbon\Carbon::parse($settings->late_threshold)->format('h:i A') }}
                                @else
                                09:00 AM
                                @endif
                            </p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">
                                OT After: {{ $settings->regular_hours ?? 8 }}h •
                                Burnout: {{ $settings->burnout_threshold ?? 48 }}h/wk
                            </p>
                        </div>
                    </div>

                    <!-- Burnout Count & Settings -->
                    <!-- <div class="flex items-end space-x-2">
                        <div class="bg-red-100 dark:bg-red-900/30 px-3 py-2 rounded-md">
                            <p class="text-sm text-red-800 dark:text-red-300">
                                Burnout Risks: {{ $burnoutEmployees ?? 0 }}
                            </p>
                        </div>
                        <button
                            wire:click="$set('showSettings', true)"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </button>
                    </div>--> 
                </div>

                <!-- Attendance Table -->
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="shadow border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Employee
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Check In
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Check Out
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Hours Worked
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($employees as $employee)
                                    @php
                                    $attendance = $employee->attendances->first();
                                    $isBurnoutRisk = $attendance ? ($attendance->is_burnout_risk ?? false) : false;
                                    $today = \Carbon\Carbon::parse($selectedDate)->isToday();
                                    @endphp
                                    <tr class="{{ $isBurnoutRisk ? 'bg-red-50 dark:bg-red-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors">

                                        <!-- Employee Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">
                                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                                        {{ $employee->name }}
                                                        @if($isBurnoutRisk)
                                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                            ⚠️ Burnout Risk
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $employee->designation->name ?? 'No Designation' }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                                        {{ $employee->designation->department->name ?? 'No Department' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Check In Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $attendance && $attendance->check_in ? $attendance->check_in->format('h:i A') : '--:--' }}
                                            </div>
                                            @if($attendance && $attendance->check_in && $attendance->is_late)
                                            <div class="text-xs text-red-600 dark:text-red-400 font-medium">
                                                Late Arrival
                                            </div>
                                            @endif
                                        </td>

                                        <!-- Check Out Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $attendance && $attendance->check_out ? $attendance->check_out->format('h:i A') : '--:--' }}
                                            </div>
                                        </td>

                                        <!-- Hours Worked Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $attendance ? number_format($attendance->hours_worked, 1) : '0.0' }} hrs
                                            </div>
                                            @if($attendance && $attendance->overtime)
                                            <div class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                                +{{ number_format($attendance->overtime_hours, 1) }} OT
                                            </div>
                                            @endif
                                        </td>

                                        <!-- Status Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                            $status = $attendance->status ?? 'absent';
                                            $statusColors = [
                                            'present' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'absent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'late' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'half_day' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                            ];
                                            $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if((!$attendance || !$attendance->check_in) && $today)
                                                <button
                                                    wire:click="checkIn({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Check In
                                                </button>
                                                @endif

                                                @if($attendance && $attendance->check_in && !$attendance->check_out && $today)
                                                <button
                                                    wire:click="checkOut({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Check Out
                                                </button>
                                                @endif

                                                @if((!$attendance || $attendance->status !== 'absent') && $today)
                                                <button
                                                    wire:click="markAbsent({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Mark Absent
                                                </button>
                                                @endif

                                                @if($attendance && $attendance->check_in && $attendance->check_out)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                                    Completed
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No employees found matching your criteria.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            @if($employees->hasPages())
                            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                                {{ $employees->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Burnout Risk Alert -->
                @if(($burnoutEmployees ?? 0) > 0)
                <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">
                                Burnout Risk Alert
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                                <p>
                                    {{ $burnoutEmployees }} employee(s) have worked more than {{ $settings->burnout_threshold ?? 48 }} hours this week and are at risk of burnout.
                                    Consider adjusting workloads or schedules.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Weekly Summary -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    @php
                    $presentCount = $employees->where('attendances.0.status', 'present')->count() + $employees->where('attendances.0.status', 'late')->count();
                    $absentCount = $employees->where('attendances.0.status', 'absent')->count();
                    $overtimeCount = $employees->where('attendances.0.overtime', true)->count();
                    $totalCount = $employees->total();

                    $attendanceRate = $totalCount > 0 ? ($presentCount / $totalCount) * 100 : 0;
                    $absentRate = $totalCount > 0 ? ($absentCount / $totalCount) * 100 : 0;
                    $overtimeRate = $totalCount > 0 ? ($overtimeCount / $totalCount) * 100 : 0;
                    @endphp

                    <!-- Total Employees -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-800 dark:text-blue-300">Total Employees</div>
                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $totalCount }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">Active Employees</div>
                    </div>

                    <!-- Present Today -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-800 dark:text-green-300">Present Today</div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $presentCount }}</div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                            {{ number_format($attendanceRate, 1) }}% attendance
                        </div>
                    </div>

                    <!-- Absent Today -->
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-red-800 dark:text-red-300">Absent Today</div>
                        <div class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $absentCount }}</div>
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                            {{ number_format($absentRate, 1) }}% absent
                        </div>
                    </div>

                    <!-- Overtime Today -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-orange-800 dark:text-orange-300">Overtime Today</div>
                        <div class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $overtimeCount }}</div>
                        <div class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                            {{ number_format($overtimeRate, 1) }}% overtime
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
@if($showSettings ?? false)
<div class="fixed inset-0 overflow-y-auto z-50">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background Overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Attendance Settings
                </h3>

                <div class="space-y-4">
                    <!-- Late Arrival Threshold -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Late Arrival Threshold
                        </label>
                        <input
                            type="time"
                            wire:model="lateThreshold"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Employees will be marked late if they check in after this time
                        </p>
                    </div>

                    <!-- Regular Working Hours -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Regular Working Hours
                        </label>
                        <input
                            type="number"
                            step="0.5"
                            min="1"
                            max="24"
                            wire:model="regularHours"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Overtime starts after these many hours
                        </p>
                    </div>

                    <!-- Burnout Threshold -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Burnout Threshold (Weekly)
                        </label>
                        <input
                            type="number"
                            min="1"
                            max="168"
                            wire:model="burnoutThreshold"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Employees working more than this many hours per week are at burnout risk
                        </p>
                    </div>

                    <!-- Work Hours -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Work Start Time
                            </label>
                            <input
                                type="time"
                                wire:model="workStartTime"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Work End Time
                            </label>
                            <input
                                type="time"
                                wire:model="workEndTime"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button
                    wire:click="saveSettings"
                    type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                    Save Settings
                </button>
                <button
                    wire:click="$set('showSettings', false)"
                    type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Flash Messages -->
@if (session()->has('success'))
<div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
    <div class="max-w-sm w-full bg-green-100 dark:bg-green-900/30 shadow-lg rounded-lg pointer-events-auto ring-1 ring-green-500 dark:ring-green-400 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if (session()->has('error'))
<div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
    <div class="max-w-sm w-full bg-red-100 dark:bg-red-900/30 shadow-lg rounded-lg pointer-events-auto ring-1 ring-red-500 dark:ring-red-400 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif