<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
        Production Records Over Time
    </h3>
    
    <!-- Fixed height container -->
    <div class="h-64"> <!-- Fixed height here -->
        <canvas id="productionChart" 
                wire:ignore
                data-dates='@json($dates)'
                data-totals='@json($totals)'></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('productionChart');
    
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const dates = JSON.parse(canvas.dataset.dates || '[]');
    const totals = JSON.parse(canvas.dataset.totals || '[]');
    
    if (dates.length === 0 || totals.length === 0) {
        canvas.style.display = 'none';
        const parent = canvas.parentElement;
        parent.innerHTML += '<p class="text-center text-gray-500 py-8">No production data available</p>';
        return;
    }

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Total KGs Produced',
                data: totals,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3,
                fill: true,
                borderWidth: 2,
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // This is okay when container has fixed height
            plugins: {
                legend: { 
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    title: { 
                        display: true, 
                        text: 'Total KGs' 
                    }
                },
                x: { 
                    title: { 
                        display: true, 
                        text: 'Date' 
                    }
                }
            }
        }
    });
});
</script>