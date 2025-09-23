<?php

namespace App\Livewire\Admin\Contracts;

use App\Models\Contract;
use Livewire\Component;
use App\Models\Department;
use App\Models\Employee;

class Edit extends Component
{
    public $contract;
    public $search = '';
    public $department_id;
    public function rules(): array
    {
        return [
            'contract.designations_id' => 'required',
            'contract.employee_id' => 'required',
            'contract.start_date' => 'required|date',
            'contract.end_date' => 'required|date|after:contract.start_date',
            'contract.rate_type' => 'required',
            'contract.rate' => 'required|numeric',
        ];
    }

    public function mount($id): void
    {
        $this->contract =  Contract::find($id);
        $this->search = $this->contract->employee->name;
        $this->department_id = $this->contract->designation->department_id;

    }
    
    public function selectEmployee($id): void
    {
        $this->contract->employee_id = $id;
        $this->search = $this->contract->employee->name;
    }
    
    public function save(): mixed
    {
        $this->validate();
        $this->contract->save();
        session()->flash('success', 'Contract Updated successfully.');
        return $this->redirectIntended('contracts.index');
    }

    public function render()
    {
        $employees = Employee::inCompany()->searchByName($this->search)->get();
        $departments = Department::inCompany()->get();
        $designations = $this->department_id ? Department::find($this->department_id)->designations : collect();
        return view('livewire.admin.contracts.edit', [
            'employees' => $employees,
            'departments' => $departments,
            'designations' => $designations,
        ]);
    }
}
