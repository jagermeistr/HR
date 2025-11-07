<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use App\Models\ProductionRecord;

class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.productions.index', [
            'production_records' => ProductionRecord::with('collection_center')->latest()->get(),
        ]);
    }
}
