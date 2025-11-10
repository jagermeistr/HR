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
                    
                    <!-- Departments Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 flex flex-col items-center hover:shadow-md transition-shadow">
                        <span class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $departmentsCount ?? 0 }}</span>
                        <span class="mt-2 text-gray-800 dark:text-gray-200 font-semibold text-lg">Departments</span>
                    </div>
                    
                    <!-- Companies Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 flex flex-col items-center hover:shadow-md transition-shadow">
                        <span class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $companiesCount ?? 0 }}</span>
                        <span class="mt-2 text-gray-800 dark:text-gray-200 font-semibold text-lg">Companies</span>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 gap-6 mt-8">
                    <!-- Bar Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                        <span class="text-gray-800 dark:text-gray-200 font-semibold text-lg mb-4 block">Production Growth</span>
                        <div class="w-full h-64 flex items-end justify-center px-4">
                            <div class="flex items-end w-full h-48 space-x-4">
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-400 dark:bg-blue-500 rounded-t shadow-lg transition-all hover:bg-blue-500 dark:hover:bg-blue-400" style="height: 60%"></div>
                                    <span class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Jan</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-500 dark:bg-blue-600 rounded-t shadow-lg transition-all hover:bg-blue-600 dark:hover:bg-blue-500" style="height: 75%"></div>
                                    <span class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Feb</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-600 dark:bg-blue-700 rounded-t shadow-lg transition-all hover:bg-blue-700 dark:hover:bg-blue-600" style="height: 90%"></div>
                                    <span class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Mar</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-500 dark:bg-blue-600 rounded-t shadow-lg transition-all hover:bg-blue-600 dark:hover:bg-blue-500" style="height: 70%"></div>
                                    <span class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Apr</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-400 dark:bg-blue-500 rounded-t shadow-lg transition-all hover:bg-blue-500 dark:hover:bg-blue-400" style="height: 80%"></div>
                                    <span class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">May</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                        <span class="text-gray-800 dark:text-gray-200 font-semibold text-lg mb-4 block">Production Trend</span>
                        <div class="w-full h-96 flex items-center justify-center">
                            <canvas id="productionLineChart" class="w-full h-full"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('productionLineChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                    label: 'Production Growth',
                    data: [60, 75, 90, 70, 80],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: false 
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                        },
                        ticks: {
                            color: 'rgb(107, 114, 128)',
                            stepSize: 20
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                        },
                        ticks: {
                            color: 'rgb(107, 114, 128)',
                        }
                    }
                }
            }
        });
    });
</script>