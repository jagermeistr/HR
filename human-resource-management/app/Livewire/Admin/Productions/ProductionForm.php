<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use App\Models\Farmer;
use App\Models\CollectionCenter;
use App\Models\ProductionRecord;

class ProductionForm extends Component
{
    public $date;
    public $collection_center_id;
    public $farmer_weights = []; // [farmer_id => weight]

    public function submit()
    {
        $this->validate([
            'date' => 'required|date',
            'collection_center_id' => 'required|exists:collection_centers,id',
            'farmer_weights.*' => 'required|numeric|min:0',
        ]);

        $production = ProductionRecord::create([
            'date' => $this->date,
            'collection_center_id' => $this->collection_center_id,
            'total_weight' => array_sum($this->farmer_weights),
            'company_id' => session('company_id'),
        ]);

        foreach($this->farmer_weights as $farmer_id => $weight){
            $production->details()->create([
                'farmer_id' => $farmer_id,
                'weight_supplied' => $weight,
            ]);
        }

        session()->flash('success', 'Production recorded successfully.');
        return redirect()->route('productions.index');
    }

    public function render()
    {
        $farmers = Farmer::inCompany()->get();
        $collectionCenters = CollectionCenter::inCompany()->get();

        return view('livewire.admin.productions.production-form', compact('farmers','collectionCenters'));
    }
}

