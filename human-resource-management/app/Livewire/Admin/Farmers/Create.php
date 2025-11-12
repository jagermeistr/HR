<?php

namespace App\Livewire\Admin\Farmers;

use Livewire\Component;
use App\Models\Farmer;
use App\Models\ProductionDetail;

class Create extends Component
{
    public $farmer;
    public $kgs_supplied;

    public function rules(): array
    {
        return [
            'farmer.name' => 'required|string|max:255',
            'farmer.email' => 'required|email|max:255|unique:farmers,email',
            'farmer.phone' => 'required|string|max:20',
            'farmer.address' => 'required|string|max:255',
            'kgs_supplied' => 'required|numeric|min:0',
        ];
    }

    public function mount(): void
    {
        $this->farmer = new Farmer();
    }

    public function save(): mixed
    {
        $this->validate();

        // Save farmer
        $this->farmer->save();

        // Save production details directly
        ProductionDetail::create([
            'farmer_id' => $this->farmer->id,
            'kgs_supplied' => $this->kgs_supplied,
            // production_record_id will be null or you can remove it from migration
        ]);

        session()->flash('success', 'Farmer and production details created successfully.');
        return $this->redirect(route('farmers.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.farmers.create');
    }
}