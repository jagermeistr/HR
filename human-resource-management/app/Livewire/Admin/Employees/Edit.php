<?php

namespace App\Livewire\Admin\Employees;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\Department;

class Edit extends Component
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
            'employee.department_id' => 'required|exists:departments,id',
        ];
    }

    public function mount($id): void
    {
        $this->employee =  Employee::find($id);
        $this->department_id = $this->employee->designation->department_id;
    }

    public function save(): mixed
    {
        $this->validate();
        $this->employee->save();
        session()->flash('success', 'Employee updated successfully.');
        return $this->redirectIntended('employees.index');
    }
    public function render()
    {
        $designations = Designation::inCompany()->where('department_id', $this->department_id)->get();
        return view('livewire.admin.employees.edit', [
            'designations' => $designations,
            'departments' => Department::inCompany()->get()
        ]);
    }
}
