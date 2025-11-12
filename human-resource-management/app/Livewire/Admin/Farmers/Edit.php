<?php

namespace App\Livewire\Admin\Farmers;

use Livewire\Component;
use App\Models\Farmer;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public $farmer;

    public function rules(): array
    {
        return [
            'farmer.name' => 'required|string|max:255',
            'farmer.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('farmers', 'email')->ignore($this->farmer->id)
            ],
            'farmer.phone' => 'required|string|max:20',
            'farmer.address' => 'required|string|max:255',
            // Removed designation_id
        ];
    }

    public function mount($id): void
    {
        $this->farmer = Farmer::findOrFail($id); // Removed with('designation.department')
    }

    public function save()
    {
        $this->validate();
        $this->farmer->save();
        session()->flash('success', 'farmer updated successfully.');
        return $this->redirect(route('farmers.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.farmers.edit');
    }
}