<?php

namespace App\Livewire\Admin\Designations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Designation;

class Index extends Component
{
    use WithPagination;
    public function delete($id): void
    {
        Designation::find($id)->delete();
        session()->flash('success', 'Designation deleted successfully.');
    }
    public function render()
    {
        return view('livewire.admin.designations.index');
    }
}
