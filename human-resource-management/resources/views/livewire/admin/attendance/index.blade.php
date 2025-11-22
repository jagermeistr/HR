<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Attendance Management -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Attendance Management</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Track employee attendance and monitor burnout risks for {{ getCompany()->name ?? 'your company' }}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <input 
                            type="date" 
                            wire:model.live="selectedDate" 
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Employee</label>
                        <input 
                            type="text" 
                            wire:model.live="search" 
                            placeholder="Search employees..." 
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model.live="burnoutFilter" 
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show Burnout Risks Only</span>
                        </label>
                    </div>
                    <div class="flex items-end">
                        <div class="bg-red-100 dark:bg-red-900/30 px-3 py-2 rounded-md">
                            <p class="text-sm text-red-800 dark:text-red-300">
                                Burnout Risks: {{ $burnoutEmployees }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="shadow border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check In</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check Out</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours Worked</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($employees as $employee)
                                    @php
                                        $attendance = $employee->attendances->first();
                                        $isBurnoutRisk = $employee->is_burnout_risk;
                                        $today = \Carbon\Carbon::parse($selectedDate)->isToday();
                                    @endphp
                                    <tr class="@if($isBurnoutRisk) bg-red-50 dark:bg-red-900/20 @else hover:bg-gray-50 dark:hover:bg-gray-700 @endif transition-colors">
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
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->designation->name }}</div>
                                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ $employee->designation->department->name ?? 'No Department' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $attendance && $attendance->check_in ? $attendance->check_in->format('h:i A') : '--:--' }}
                                            </div>
                                            @if($attendance && $attendance->check_in)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attendance->check_in->format('M d, Y') }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $attendance && $attendance->check_out ? $attendance->check_out->format('h:i A') : '--:--' }}
                                            </div>
                                            @if($attendance && $attendance->check_out)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attendance->check_out->format('M d, Y') }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $attendance ? number_format($attendance->hours_worked, 1) : '0.0' }} hrs
                                            </div>
                                            @if($attendance && $attendance->overtime)
                                            <div class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                                +{{ number_format($attendance->hours_worked - 8, 1) }} OT
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $status = $attendance->status ?? 'absent';
                                                $statusColors = [
                                                    'present' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    'absent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                    'late' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                    'half_day' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$status] }}">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if((!$attendance || !$attendance->check_in) && $today)
                                                <button 
                                                    wire:click="checkIn({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Check In
                                                </button>
                                                @endif
                                                
                                                @if($attendance && $attendance->check_in && !$attendance->check_out && $today)
                                                <button 
                                                    wire:click="checkOut({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Check Out
                                                </button>
                                                @endif
                                                
                                                @if((!$attendance || $attendance->status !== 'absent') && $today)
                                                <button 
                                                    wire:click="markAbsent({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                                {{ $employees->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Burnout Risk Alert -->
                @if($burnoutEmployees > 0)
                <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">
                                Burnout Risk Alert for {{ getCompany()->name ?? 'Your Company' }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                                <p>
                                    {{ $burnoutEmployees }} employee(s) in your company have worked more than 40 hours this week and are at risk of burnout.
                                    Consider adjusting workloads or schedules.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Weekly Summary -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-800 dark:text-blue-300">Total Employees</div>
                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $employees->total() }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">In {{ getCompany()->name ?? 'Company' }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-800 dark:text-green-300">Present Today</div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                            {{ $employees->where('attendances.0.status', 'present')->count() }}
                        </div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                            @php
                                $presentCount = $employees->where('attendances.0.status', 'present')->count();
                                $totalCount = $employees->total();
                                $attendanceRate = $totalCount > 0 ? ($presentCount / $totalCount) * 100 : 0;
                            @endphp
                            {{ number_format($attendanceRate, 1) }}% attendance
                        </div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-red-800 dark:text-red-300">Absent Today</div>
                        <div class="text-2xl font-bold text-red-900 dark:text-red-100">
                            {{ $employees->where('attendances.0.status', 'absent')->count() }}
                        </div>
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                            @php
                                $absentCount = $employees->where('attendances.0.status', 'absent')->count();
                                $totalCount = $employees->total();
                                $absentRate = $totalCount > 0 ? ($absentCount / $totalCount) * 100 : 0;
                            @endphp
                            {{ number_format($absentRate, 1) }}% absent
                        </div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="text-sm font-medium text-orange-800 dark:text-orange-300">Overtime Today</div>
                        <div class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                            {{ $employees->where('attendances.0.overtime', true)->count() }}
                        </div>
                        <div class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                            @php
                                $overtimeCount = $employees->where('attendances.0.overtime', true)->count();
                                $totalCount = $employees->total();
                                $overtimeRate = $totalCount > 0 ? ($overtimeCount / $totalCount) * 100 : 0;
                            @endphp
                            {{ number_format($overtimeRate, 1) }}% overtime
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if (session()->has('success'))
<div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
    <div class="max-w-sm w-full bg-green-100 dark:bg-green-900/30 shadow-lg rounded-lg pointer-events-auto ring-1 ring-green-500 dark:ring-green-400 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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