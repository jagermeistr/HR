<?php

namespace App\Livewire\Admin\Designations;

use Livewire\Component;
use App\Models\Designation;

class Edit extends Component
{
    public $designation;
    public function rules(): array
    {
        return [
            'designation.name' => 'required|string|max:255',
            'designation.department_id' => 'required|exists:departments,id'
        ];
    }
    public function mount($id): void
    {
        $this->designation = Designation::find($id);
    }

    public function save(): mixed
    {
        $this->validate();
        $this->designation->save();
        session()->flash('success', 'Designation updated successfully.');
        return $this->redirectRoute('designations.index');
    }

    public function render()
    {
        return view('livewire.admin.designations.edit');
    }
}
