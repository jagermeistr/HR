<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Burnout Deep Dive -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                
                <!-- Header -->
                <div class="relative mb-6 w-full">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Burnout Risk Deep Dive Analysis</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                        Comprehensive analysis of employee workload patterns and burnout risks
                    </p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <!-- Time Range Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Analysis Period</label>
                    <div class="flex space-x-4">
                        <button 
                            wire:click="$set('timeRange', 'current_week')"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $timeRange === 'current_week' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                            Current Week
                        </button>
                        <button 
                            wire:click="$set('timeRange', 'last_week')"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $timeRange === 'last_week' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                            Last Week
                        </button>
                        <button 
                            wire:click="$set('timeRange', 'last_month')"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $timeRange === 'last_month' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                            Last Month
                        </button>
                    </div>
                </div>

                <!-- Executive Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600 dark:text-red-400">Critical Risk</p>
                                <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $criticalCount ?? 0 }}</p>
                                <p class="text-xs text-red-500 dark:text-red-400 mt-1">>60 hours/week</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-orange-600 dark:text-orange-400">High Risk</p>
                                <p class="text-2xl font-bold text-orange-700 dark:text-orange-300">{{ $highRiskCount ?? 0 }}</p>
                                <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">48-60 hours/week</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-50 to-green-50 dark:from-yellow-900/20 dark:to-green-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Moderate Risk</p>
                                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $moderateRiskCount ?? 0 }}</p>
                                <p class="text-xs text-yellow-500 dark:text-yellow-400 mt-1">40-48 hours/week</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600 dark:text-green-400">Safe Zone</p>
                                <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $safeCount ?? 0 }}</p>
                                <p class="text-xs text-green-500 dark:text-green-400 mt-1"><40 hours/week</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Distribution Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Risk Distribution -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Risk Distribution</h3>
                        <div class="space-y-4">
                            @foreach($riskDistribution ?? [] as $risk)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3 {{ $risk['color'] ?? 'bg-gray-300' }}"></div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $risk['level'] ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $risk['color'] ?? 'bg-gray-300' }}" style="width: '{{ $risk['percentage'] ?? 0 }}%'"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-12 text-right">
                                        {{ $risk['count'] ?? 0 }} ({{ $risk['percentage'] ?? 0 }}%)
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Department Breakdown -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Department Analysis</h3>
                        <div class="space-y-4">
                            @forelse($departmentRisks ?? [] as $dept)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $dept['department'] ?? 'Unknown' }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $dept['risk_color'] ?? 'bg-gray-100 text-gray-800' }}">{{ $dept['risk_level'] ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                                    <span>Avg: {{ $dept['avg_hours'] ?? 0 }}h/week</span>
                                    <span>At Risk: {{ $dept['at_risk_count'] ?? 0 }}/{{ $dept['total_employees'] ?? 0 }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No department data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- High Risk Employees Table -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">High Risk Employees - Immediate Attention Required</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-red-50 dark:bg-red-900/20">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 dark:text-red-300 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 dark:text-red-300 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 dark:text-red-300 uppercase tracking-wider">Weekly Hours</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 dark:text-red-300 uppercase tracking-wider">Overtime</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 dark:text-red-300 uppercase tracking-wider">Risk Level</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 dark:text-red-300 uppercase tracking-wider">Recommendation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($highRiskEmployees ?? [] as $employee)
                                <tr class="hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $employee['name'] ?? 'Unknown' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee['designation'] ?? 'No Designation' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $employee['department'] ?? 'No Department' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-lg font-bold {{ $employee['hours_color'] ?? 'text-gray-600' }}">{{ $employee['weekly_hours'] ?? 0 }}h</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $employee['daily_avg'] ?? 0 }}/day</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                            +{{ $employee['overtime_hours'] ?? 0 }}h OT
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee['risk_badge_color'] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $employee['risk_level'] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $employee['recommendation'] ?? 'No recommendation available' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No high-risk employees found for the selected period.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Weekly Pattern Analysis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Weekly Trends -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Weekly Work Pattern Analysis</h3>
                        <div class="space-y-4">
                            @forelse($weeklyPatterns ?? [] as $pattern)
                            <div class="border-l-4 {{ $pattern['border_color'] ?? 'border-gray-300' }} pl-4 py-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $pattern['title'] ?? 'Unknown Pattern' }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $pattern['description'] ?? 'No description' }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $pattern['badge_color'] ?? 'bg-gray-100 text-gray-800' }}">{{ $pattern['employee_count'] ?? 0 }} employees</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No pattern data available</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Intervention Recommendations -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4">Recommended Interventions</h3>
                        <div class="space-y-3">
                            @forelse($interventions ?? [] as $intervention)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mt-0.5">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ $loop->iteration }}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-blue-900 dark:text-blue-200">{{ $intervention['title'] ?? 'Unknown Intervention' }}</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ $intervention['description'] ?? 'No description' }}</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1"><strong>Impact:</strong> {{ $intervention['impact'] ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-blue-700 dark:text-blue-300">No intervention recommendations available</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Action Plan -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/10 dark:to-indigo-900/10 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-300 mb-4">30-Day Action Plan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @forelse($actionPlan ?? [] as $action)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                            <div class="flex items-center mb-3">
                                <div class="p-2 {{ $action['icon_bg'] ?? 'bg-gray-100' }} rounded-lg">
                                    <svg class="w-5 h-5 {{ $action['icon_color'] ?? 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon_path'] ?? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                    </svg>
                                </div>
                                <span class="ml-3 text-sm font-medium {{ $action['text_color'] ?? 'text-gray-700' }}">{{ $action['timeframe'] ?? 'Unknown' }}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $action['title'] ?? 'Unknown Title' }}</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                @foreach($action['tasks'] ?? [] as $task)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $task }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @empty
                        <div class="col-span-3 text-center py-4 text-gray-500 dark:text-gray-400">
                            No action plan data available
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>