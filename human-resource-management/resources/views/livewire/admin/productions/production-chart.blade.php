<div class="space-y-6">
    <!-- Original Production Chart -->
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

    <!-- Production Forecast Chart -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    Production Trends & Forecast
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Historical data with {{ $predictionPeriod }}-day SARIMA-based forecast
                </p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <!-- Prediction Period Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button 
                        wire:click="updatePredictionPeriod(7)"
                        class="px-3 py-1 text-sm rounded-md {{ $predictionPeriod == 7 ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}"
                    >
                        7D
                    </button>
                    <button 
                        wire:click="updatePredictionPeriod(30)"
                        class="px-3 py-1 text-sm rounded-md {{ $predictionPeriod == 30 ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}"
                    >
                        30D
                    </button>
                    <button 
                        wire:click="updatePredictionPeriod(90)"
                        class="px-3 py-1 text-sm rounded-md {{ $predictionPeriod == 90 ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}"
                    >
                        90D
                    </button>
                </div>

                <!-- Prediction Toggle -->
                <button 
                    wire:click="togglePredictions"
                    class="px-3 py-1 text-sm rounded-md {{ $showPredictions ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}"
                >
                    {{ $showPredictions ? 'Hide Forecast' : 'Show Forecast' }}
                </button>
            </div>
        </div>
        
        <!-- Chart Container -->
        <div class="h-80">
            <canvas 
                id="productionForecastChart" 
                wire:ignore
                data-dates='@json($dates)'
                data-totals='@json($totals)'
                data-prediction-dates='@json($predictionDates)'
                data-prediction-values='@json($predictionValues)'
                data-confidence-scores='@json($confidenceScores)'
                data-show-predictions='{{ $showPredictions }}'
                data-prediction-period='{{ $predictionPeriod }}'
            ></canvas>
        </div>

        <!-- Loading State -->
        <div wire:loading class="text-center py-4">
            <div class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating forecast...
            </div>
        </div>

        <!-- Forecast Insights -->
        @if($showPredictions && count($predictionValues) > 0)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Avg Forecast</p>
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                            {{ number_format($averagePrediction, 1) }} kg
                        </p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-800 p-2 rounded-full">
                        📈
                    </div>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Model Confidence</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                            {{ number_format($predictionAccuracy, 1) }}%
                        </p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-800 p-2 rounded-full">
                        🎯
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Forecast Period</p>
                        <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                            {{ $predictionPeriod }} days
                        </p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-800 p-2 rounded-full">
                        📅
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Data Quality Warning -->
        @if(count($dates) < 7)
        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    More data needed for accurate predictions. Collect at least 7 days of production data.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize original production chart
    const originalCanvas = document.getElementById('productionChart');
    if (originalCanvas) {
        const dates = JSON.parse(originalCanvas.dataset.dates || '[]');
        const totals = JSON.parse(originalCanvas.dataset.totals || '[]');
        
        if (dates.length === 0 || totals.length === 0) {
            originalCanvas.style.display = 'none';
            const parent = originalCanvas.parentElement;
            parent.innerHTML += '<p class="text-center text-gray-500 py-8">No production data available</p>';
        } else {
            new Chart(originalCanvas, {
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
                    maintainAspectRatio: false,
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
        }
    }

    // Initialize forecast chart
    const forecastCanvas = document.getElementById('productionForecastChart');
    if (!forecastCanvas) {
        console.error('Forecast canvas element not found');
        return;
    }

    const forecastDates = JSON.parse(forecastCanvas.dataset.dates || '[]');
    const forecastTotals = JSON.parse(forecastCanvas.dataset.totals || '[]');
    const predictionDates = JSON.parse(forecastCanvas.dataset.predictionDates || '[]');
    const predictionValues = JSON.parse(forecastCanvas.dataset.predictionValues || '[]');
    const confidenceScores = JSON.parse(forecastCanvas.dataset.confidenceScores || '[]');
    const showPredictions = forecastCanvas.dataset.showPredictions === 'true';
    const predictionPeriod = parseInt(forecastCanvas.dataset.predictionPeriod || '30');

    // Create datasets
    const datasets = [
        {
            label: 'Historical Production',
            data: forecastTotals,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: false,
            borderWidth: 3,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4
        }
    ];

    // Add prediction dataset if enabled
    if (showPredictions && predictionValues.length > 0) {
        // Create a combined array with nulls for historical period
        const predictionData = [...Array(forecastDates.length).fill(null), ...predictionValues];
        const combinedDates = [...forecastDates, ...predictionDates];

        datasets.push({
            label: 'Production Forecast',
            data: predictionData,
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderDash: [5, 5],
            tension: 0.4,
            fill: false,
            borderWidth: 3,
            pointBackgroundColor: 'rgb(16, 185, 129)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 3
        });

        // Add confidence interval
        if (confidenceScores.length > 0) {
            const lowerBounds = predictionValues.map((value, index) => {
                const confidence = confidenceScores[index];
                return value * (1 - (1 - confidence) / 2);
            });
            
            const upperBounds = predictionValues.map((value, index) => {
                const confidence = confidenceScores[index];
                return value * (1 + (1 - confidence) / 2);
            });

            datasets.push({
                label: 'Confidence Range',
                data: [...Array(forecastDates.length).fill(null), ...upperBounds],
                borderColor: 'rgba(16, 185, 129, 0.3)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 0,
                fill: '-1',
                pointRadius: 0,
                tension: 0.4
            });

            datasets.push({
                label: 'Confidence Range',
                data: [...Array(forecastDates.length).fill(null), ...lowerBounds],
                borderColor: 'rgba(16, 185, 129, 0.3)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 0,
                fill: false,
                pointRadius: 0,
                tension: 0.4
            });
        }

        // Update labels for combined timeline
        var chartLabels = combinedDates;
    } else {
        var chartLabels = forecastDates;
    }

    new Chart(forecastCanvas, {
        type: 'line',
        data: {
            labels: chartLabels || forecastDates,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-US').format(context.parsed.y) + ' kg';
                                
                                // Add confidence for predictions
                                if (context.datasetIndex === 1 && confidenceScores[context.dataIndex - forecastDates.length]) {
                                    const confidence = confidenceScores[context.dataIndex - forecastDates.length] * 100;
                                    label += ` (${confidence.toFixed(1)}% confidence)`;
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total KGs',
                        color: '#6B7280'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('en-US').format(value) + ' kg';
                        }
                    },
                    grid: {
                        color: 'rgba(107, 114, 128, 0.1)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        color: '#6B7280'
                    },
                    grid: {
                        color: 'rgba(107, 114, 128, 0.1)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'nearest'
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
});

// Listen for Livewire events to update chart
document.addEventListener('livewire:init', () => {
    Livewire.on('chartUpdated', () => {
        setTimeout(() => {
            window.location.reload();
        }, 100);
    });
});
</script>