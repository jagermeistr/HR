<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionRecord;
use App\Services\ProductionPredictionService;

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
        // Load historical data for both charts
        $productions = ProductionRecord::select(
                DB::raw('DATE(production_date) as date'),
                DB::raw('SUM(total_kgs) as total')
            )
            ->where('production_date', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Use full date format to avoid duplicates
        $this->dates = $productions->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('M d, Y'); // Add year to avoid duplicates
        })->toArray();
        
        $this->totals = $productions->pluck('total')->toArray();

        // Load predictions if we have enough data
        if (count($productions) >= 7) {
            $this->loadPredictions($productions);
        }

        $this->calculateMetrics();
    }

    public function loadPredictions($historicalData)
    {
        $predictionService = new ProductionPredictionService();
        
        $this->predictions = $predictionService->generatePredictions($historicalData, $this->predictionPeriod);
        
        $this->predictionDates = collect($this->predictions)->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('M d, Y');
        })->toArray();
        
        $this->predictionValues = collect($this->predictions)->pluck('predicted_kgs')->toArray();
        $this->confidenceScores = collect($this->predictions)->pluck('confidence')->toArray();
    }

    /**
     * Calculate metrics for the forecast insights
     */
    public function calculateMetrics()
    {
        // Calculate average prediction
        if (!empty($this->predictionValues)) {
            $this->averagePrediction = array_sum($this->predictionValues) / count($this->predictionValues);
        } else {
            $this->averagePrediction = 0;
        }

        // Calculate prediction accuracy (average confidence)
        if (!empty($this->confidenceScores)) {
            $this->predictionAccuracy = (array_sum($this->confidenceScores) / count($this->confidenceScores)) * 100;
        } else {
            $this->predictionAccuracy = 0;
        }
    }

    public function updatePredictionPeriod($period)
    {
        $this->predictionPeriod = $period;
        $this->loadChartData();
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