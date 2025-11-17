<div class="space-y-6">
    <!-- Original Production Chart -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
            Production Records Over Time
        </h3>

        <div class="h-64">
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
                    Historical data with {{ $predictionPeriod }}-day forecast
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button wire:click="updatePredictionPeriod(7)"
                        class="px-3 py-1 text-sm rounded-md {{ $predictionPeriod == 7 ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}">7D</button>
                    <button wire:click="updatePredictionPeriod(30)"
                        class="px-3 py-1 text-sm rounded-md {{ $predictionPeriod == 30 ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}">30D</button>
                    <button wire:click="updatePredictionPeriod(90)"
                        class="px-3 py-1 text-sm rounded-md {{ $predictionPeriod == 90 ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300' }}">90D</button>
                </div>

                <button wire:click="togglePredictions"
                    class="px-3 py-1 text-sm rounded-md {{ $showPredictions ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    {{ $showPredictions ? 'Hide Forecast' : 'Show Forecast' }}
                </button>
            </div>
        </div>

        <div class="h-80">
            <canvas id="productionForecastChart"
                wire:ignore
                data-dates='@json($dates)'
                data-totals='@json($totals)'
                data-prediction-dates='@json($predictionDates)'
                data-prediction-values='@json($predictionValues)'
                data-confidence-scores='@json($confidenceScores)'
                data-show-predictions='{{ $showPredictions ? 'true' : 'false' }}'
                data-prediction-period='{{ $predictionPeriod }}'
            ></canvas>
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
                    <div class="bg-green-100 dark:bg-green-800 p-2 rounded-full">🎯</div>
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
                    <div class="bg-purple-100 dark:bg-purple-800 p-2 rounded-full">📅</div>
                </div>
            </div>
        </div>
        @endif

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

<!-- Chart.js v4 CDN (you said you have v4.5.1) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // hold chart instances globally so we can destroy and re-create on updates
    let forecastChart = null;
    let originalChart = null;

    // helper to format ISO date strings for display
    function formatDisplayDate(iso) {
        const d = new Date(iso + 'T00:00:00'); // ensure local midnight
        // e.g. "Oct 19, 2025"
        return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    // Create original (historical-only) chart
    function initOriginalChart() {
        const originalCanvas = document.getElementById('productionChart');
        if (!originalCanvas) return;

        const dates = JSON.parse(originalCanvas.dataset.dates || '[]');
        const totals = JSON.parse(originalCanvas.dataset.totals || '[]');

        if (!dates.length || !totals.length) {
            originalCanvas.style.display = 'none';
            const parent = originalCanvas.parentElement;
            parent.innerHTML += '<p class="text-center text-gray-500 py-8">No production data available</p>';
            return;
        }

        const ctx = originalCanvas.getContext('2d');
        const labels = dates.map(formatDisplayDate);

        originalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Total KGs Produced',
                    data: totals,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.12)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Total KGs' },
                        ticks: {
                            callback: function (v) {
                                return new Intl.NumberFormat('en-US').format(v) + ' kg';
                            }
                        }
                    },
                    x: {
                        title: { display: true, text: 'Date' }
                    }
                }
            }
        });
    }

    // Create forecast chart (historical + prediction merged)
    function initForecastChart() {
        const forecastCanvas = document.getElementById('productionForecastChart');
        if (!forecastCanvas) return;

        const forecastDates = JSON.parse(forecastCanvas.dataset.dates || '[]'); // historical ISO
        const forecastTotals = JSON.parse(forecastCanvas.dataset.totals || '[]');
        const predictionDates = JSON.parse(forecastCanvas.dataset.predictionDates || '[]'); // ISO
        const predictionValues = JSON.parse(forecastCanvas.dataset.predictionValues || '[]');
        const confidenceScores = JSON.parse(forecastCanvas.dataset.confidenceScores || '[]');
        const showPredictions = (forecastCanvas.dataset.showPredictions === 'true');

        // Compose combined timeline (ISO strings)
        let combinedDates = [...forecastDates];
        let combinedLabels = combinedDates.map(formatDisplayDate);
        let datasets = [];

        // Historical dataset
        datasets.push({
            label: 'Historical Production',
            data: forecastTotals,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.08)',
            tension: 0.35,
            fill: false,
            borderWidth: 3,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4
        });

        if (showPredictions && predictionValues.length > 0) {
            // Append prediction dates to combinedDates
            combinedDates = [...combinedDates, ...predictionDates];
            combinedLabels = combinedDates.map(formatDisplayDate);

            // For the chart we need an array the same length as combinedDates where historical indices have values, forecast indices have values
            const nullsForHistorical = Array(forecastDates.length).fill(null);
            const predictionDataSeries = [...nullsForHistorical, ...predictionValues];

            datasets.push({
                label: 'Production Forecast',
                data: predictionDataSeries,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                borderDash: [6, 4],
                tension: 0.35,
                fill: false,
                borderWidth: 3,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 3
            });

            // Confidence band (upper and lower) - we create two datasets and fill between them
            if (confidenceScores.length === predictionValues.length) {
                const upper = [...nullsForHistorical];
                const lower = [...nullsForHistorical];

                for (let i = 0; i < predictionValues.length; i++) {
                    const v = predictionValues[i];
                    const conf = confidenceScores[i] ?? 0.5;
                    // symmetric band based on confidence (simple approach)
                    const halfWidth = v * ((1 - conf) / 2);
                    upper.push(v + halfWidth);
                    lower.push(v - halfWidth);
                }

                datasets.push({
                    label: 'Confidence Upper',
                    data: upper,
                    borderColor: 'rgba(16,185,129,0.15)',
                    backgroundColor: 'rgba(16,185,129,0.04)',
                    borderWidth: 0,
                    pointRadius: 0,
                    fill: '+1', // fill to previous dataset (forecast)
                    tension: 0.35
                });

                datasets.push({
                    label: 'Confidence Lower',
                    data: lower,
                    borderColor: 'rgba(16,185,129,0.15)',
                    backgroundColor: 'rgba(16,185,129,0.04)',
                    borderWidth: 0,
                    pointRadius: 0,
                    fill: '-1', // fill to the dataset above (upper)
                    tension: 0.35
                });
            }
        }

        const ctx = forecastCanvas.getContext('2d');

        // destroy existing instance if present
        if (forecastChart) {
            forecastChart.destroy();
            forecastChart = null;
        }

        forecastChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: combinedLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { usePointStyle: true, padding: 16 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function (ctx) {
                                return ctx[0] ? ctx[0].label : '';
                            },
                            label: function (context) {
                                const datasetIndex = context.datasetIndex;
                                const value = context.parsed.y;
                                let label = context.dataset.label || '';
                                if (value === null || typeof value === 'undefined') {
                                    return null;
                                }
                                // Format number
                                const formatted = new Intl.NumberFormat('en-US').format(value);
                                label += ': ' + formatted + ' kg';

                                // If the dataset is the forecast dataset (we placed it as index 1 when present), show confidence
                                // To be robust, detect label 'Production Forecast'
                                if (label.startsWith('Production Forecast')) {
                                    // find index in combinedLabels
                                    const dataIndex = context.dataIndex;
                                    // compute predictionIndex relative to historical length
                                    const histLen = forecastDates.length;
                                    const predIndex = dataIndex - histLen;
                                    if (predIndex >= 0 && predIndex < confidenceScores.length) {
                                        const conf = (confidenceScores[predIndex] || 0) * 100;
                                        label += ` (${conf.toFixed(1)}% confidence)`;
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
                        title: { display: true, text: 'Total KGs', color: '#6B7280' },
                        ticks: {
                            callback: function (v) {
                                return new Intl.NumberFormat('en-US').format(v) + ' kg';
                            }
                        },
                        grid: { color: 'rgba(107, 114, 128, 0.08)' }
                    },
                    x: {
                        title: { display: true, text: 'Date', color: '#6B7280' },
                        grid: { color: 'rgba(107, 114, 128, 0.06)' }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    point: { hoverRadius: 8 }
                }
            }
        });
    }

    // Initialize both charts
    initOriginalChart();
    initForecastChart();

    // Re-initialize forecast chart when Livewire dispatches the event
    Livewire.dispatch('chart-updated', (data) => {
        initForecastChart();
        // also update original chart if needed
        if (originalChart) {
            originalChart.update();
        }
    });

    // Also handle Livewire's specific event bus in case you prefer emitting from Livewire:
    if (typeof Livewire !== 'undefined') {
        Livewire.on('chartUpdated', () => {
            initForecastChart();
            if (originalChart) originalChart.update();
        });
    }
});
</script>
