<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Dashboard -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Dashboard</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Welcome {{ auth()->user()->name }}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <!-- Employees Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 flex flex-col items-center hover:shadow-md transition-shadow">
                        <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Employee::count() }}</span>
                        <span class="mt-2 text-gray-800 dark:text-gray-200 font-semibold text-lg">Employees</span>
                    </div>

                    <!-- Production Card -->
                    <!-- Today's Production Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Today's Production</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                                    {{ number_format(\App\Models\ProductionRecord::whereDate('production_date', today())->sum('total_kgs'), 2) }} kg
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ now()->format('F j, Y') }}</p>
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Companies Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 flex flex-col items-center hover:shadow-md transition-shadow">
                        <span class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $companiesCount ?? 0 }}</span>
                        <span class="mt-2 text-gray-800 dark:text-gray-200 font-semibold text-lg">Companies</span>
                    </div>
                </div>

                <!-- Charts Section -->
                @livewire('admin.productions.production-chart')

            </div>
        </div>
    </div>
</div>