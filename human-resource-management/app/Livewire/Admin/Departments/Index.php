<?php

namespace App\Livewire\Admin\Departments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;
use Livewire\WithoutUrlPagination;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;
    public function delete($id): void
    {
        Department::find($id)->delete();
        session()->flash('success', 'Department deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.departments.index', [
            'departments' => Department::inCompany()->paginate(perPage: 5),
        ]);
    }
}
