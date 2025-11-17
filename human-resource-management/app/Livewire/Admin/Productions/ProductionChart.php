<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionRecord;
use App\Services\ProductionPredictionService;
use Carbon\Carbon;

class ProductionChart extends Component
{
    public $dates = [];
    public $totals = [];
    public $predictions = [];
    public $predictionDates = [];
    public $predictionValues = [];
    public $confidenceScores = [];
    public $predictionPeriod = 30;
    public $showPredictions = true;
    public $averagePrediction = 0;
    public $predictionAccuracy = 0;

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        // Load historical data (raw ISO dates)
        $productions = ProductionRecord::select(
                DB::raw('DATE(production_date) as date'),
                DB::raw('SUM(total_kgs) as total')
            )
            ->where('production_date', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Keep ISO date strings (Y-m-d) only — formatting will be done in JS
        $this->dates = $productions->pluck('date')->map(function ($d) {
            return Carbon::parse($d)->toDateString();
        })->toArray();

        $this->totals = $productions->pluck('total')->map(function ($v) {
            return (float) $v;
        })->toArray();

        // Load predictions if we have enough data
        if (count($productions) >= 1) {
            // call predictions even if <7 so fallback predictions are returned;
            // UI will show the data-quality warning if <7
            $this->loadPredictions($productions);
        } else {
            $this->predictions = [];
            $this->predictionDates = [];
            $this->predictionValues = [];
            $this->confidenceScores = [];
        }

        $this->calculateMetrics();
    }

    public function loadPredictions($historicalData)
    {
        $predictionService = new ProductionPredictionService();
        $this->predictions = $predictionService->generatePredictions($historicalData, (int)$this->predictionPeriod);

        $this->predictionDates = collect($this->predictions)
            ->pluck('date')
            ->map(function ($d) {
                return Carbon::parse($d)->toDateString();
            })->toArray();

        $this->predictionValues = collect($this->predictions)
            ->pluck('predicted_kgs')
            ->map(function ($v) {
                return (float) $v;
            })->toArray();

        $this->confidenceScores = collect($this->predictions)
            ->pluck('confidence')
            ->map(function ($v) {
                return (float) $v;
            })->toArray();
    }

    public function calculateMetrics()
    {
        $this->averagePrediction = !empty($this->predictionValues)
            ? round(array_sum($this->predictionValues) / count($this->predictionValues), 1)
            : 0;

        $this->predictionAccuracy = !empty($this->confidenceScores)
            ? round((array_sum($this->confidenceScores) / count($this->confidenceScores)) * 100, 1)
            : 0;
    }

    public function updatePredictionPeriod($period)
    {
        $this->predictionPeriod = (int) $period;
        $this->loadChartData();
        // Emit event to the frontend to update chart without reloading page
        $this->dispatch('chartUpdated');
    }

    public function togglePredictions()
    {
        $this->showPredictions = !$this->showPredictions;
        $this->dispatch('chartUpdated');
    }

    public function render()
    {
        return view('livewire.admin.productions.production-chart');
    }
}
