<?php

namespace App\Livewire\Admin\Employees;

use App\Models\Designation;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Department;

class Create extends Component
{
    public $employee;
    public $department_id;

    public function rules(): array
    {
        return [
            'employee.name' => 'required|string|max:255',
            'employee.email' => 'required|email|max:255',
            'employee.phone' => 'required|string|max:20',
            'employee.address' => 'required|string|max:255',
            'employee.designation_id' => 'required|exists:designations,id',
        ];
    }

    public function mount(): void
    {
        $this->employee = new Employee();
    }

    public function save(): mixed
    {
        $this->validate();
        $this->employee->save();
        session()->flash('success', 'Employee created successfully.');
        return $this->redirectIntended('employees.index');
    }
    public function render()
    {
        $designations = Designation::inCompany()->where('department_id', $this->department_id)->get();
        return view('livewire.admin.employees.create', [
            'designations' => $designations,
            'departments' => Department::inCompany()->get()
        ]);

    }
}
