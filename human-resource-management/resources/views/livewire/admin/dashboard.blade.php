<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl">
            Dashboard
        </flux:heading>
        <flux:subheading size="lg" class="mb-6">
            Welcome {{ auth()->user()->name }}
        </flux:subheading>
        <flux:separator/>
    </div>
     
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-blue-600">{{ \App\Models\Employee::count() }}</span>
            <span class="mt-2 text-black font-semibold" style="font-size: 1.1rem;">Employees</span>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-green-600">{{ $departmentsCount ?? 0 }}</span>
            <span class="mt-2 text-black font-semibold" style="font-size: 1.1rem;">Departments</span>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-3xl font-bold text-yellow-600">{{ $leavesCount ?? 0 }}</span>
            <span class="mt-2 text-black font-semibold" style="font-size: 1.1rem;">Companies</span>
        </div>
        <div class="col-span-1 md:col-span-3 flex justify-center">
            <div class="bg-white rounded-lg shadow p-12 flex flex-col items-center w-full max-w-5xl">
            <span class="text-black font-semibold mb-2" style="font-size: 1.3rem;">Production Growth</span>
            <div class="w-full h-[64rem] flex items-end">
                <div class="flex-1 mx-4 bg-blue-400 rounded-t shadow-lg" style="height: 60%; min-height: 8rem;"></div>
                <div class="flex-1 mx-4 bg-blue-500 rounded-t shadow-lg" style="height: 75%; min-height: 8rem;"></div>
                <div class="flex-1 mx-4 bg-blue-600 rounded-t shadow-lg" style="height: 90%; min-height: 8rem;"></div>
                <div class="flex-1 mx-4 bg-blue-500 rounded-t shadow-lg" style="height: 70%; min-height: 8rem;"></div>
                <div class="flex-1 mx-4 bg-blue-300 rounded-t shadow-lg" style="height: 80%; min-height: 8rem;"></div>
            </div>
            <div class="flex w-full justify-between mt-4 text-base text-gray-700 font-semibold">
                <span>Jan</span>
                <span>Feb</span>
                <span>Mar</span>
                <span>Apr</span>
                <span>May</span>
            </div>
            </div>
        </div>
        <div class="col-span-1 md:col-span-3 flex justify-center">
            <div class="bg-white rounded-lg shadow p-12 flex flex-col items-center w-full max-w-5xl">
            <span class="text-black font-semibold mb-2" style="font-size: 1.3rem;">Production Growth</span>
            <div class="w-full h-96 flex items-center justify-center">
                <canvas id="productionLineChart" class="w-full h-full"></canvas>
            </div>
            <div class="flex w-full justify-between mt-4 text-base text-gray-700 font-semibold">
                <span>Jan</span>
                <span>Feb</span>
                <span>Mar</span>
                <span>Apr</span>
                <span>May</span>
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
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 6,
                }]
                },
                options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                    }
                }
                }
            });
            });
        </script>


    </div>
</div>
