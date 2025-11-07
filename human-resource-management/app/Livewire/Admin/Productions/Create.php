<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use App\Models\ProductionRecord;
use App\Models\CollectionCenter;
use App\Models\Farmer;

class Create extends Component
{
    public $collection_center_id;
    public $total_kgs;
    public $farmers;

    public function render()
    {
        return view('livewire.admin.productions.create', [
            'farmers' => Farmer::where('company_id', session('company_id'))->get(),
            'collectioncenters' => CollectionCenter::where('company_id', session('company_id'))->get(),
        ]);
    }

    public function save()
    {
        $this->validate([
            'collection_center_id' => 'required',
            'total_kgs' => 'required|numeric|min:1',
        ]);

        ProductionRecord::create([
            'company_id' => session('company_id'),
            'collection_center_id' => $this->collection_center_id,
            'total_kgs' => $this->total_kgs,
            'production_date' => now(),
        ]);

        session()->flash('success', 'Production record added successfully.');
        $this->reset();
    }
}
