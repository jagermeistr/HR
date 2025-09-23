<?php

namespace App\Livewire\Admin\Employees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use Livewire\WithoutUrlPagination;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;
    public function delete($id): void
    {
        Employee::find($id)->delete();
        Session()->flash('success', 'Employee deleted successfully.');
    }
    public function render()
    {
        return view('livewire.admin.employees.index');
    }
}
