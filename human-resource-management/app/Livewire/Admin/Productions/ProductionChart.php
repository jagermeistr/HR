<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionRecord;

class ProductionChart extends Component
{
    public $dates = [];
    public $totals = [];

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        $productions = ProductionRecord::select(
                DB::raw('DATE(production_date) as date'),
                DB::raw('SUM(total_kgs) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $this->dates = $productions->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('M d');
        })->toArray();
        
        $this->totals = $productions->pluck('total')->toArray();
    }

    public function render()
    {
        return view('livewire.admin.productions.production-chart');
    }
}