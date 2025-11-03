<?php

namespace App\Livewire\Admin\Contracts;

use App\Models\Contract;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public $contract;
    public $search = '';
    public $department_id = '';

    public function rules(): array
    {
        return [
            'contract.designation_id' => 'required', // Fixed: designations_id to designation_id
            'contract.employee_id' => 'required',
            'contract.start_date' => 'required|date',
            'contract.end_date' => 'required|date|after:contract.start_date',
            'contract.rate_type' => 'required',
            'contract.rate' => 'required|numeric',
        ];
    }

    public function mount(): void
    {
        $this->contract = new Contract();
    }
    
    public function selectEmployee($id): void
    {
        $employee = Employee::find($id);
        
        if ($employee) {
            $this->contract->employee_id = $id;
            $this->search = $employee->name;
            
            // Auto-fill department and designation if available
            if ($employee->designation) {
                $this->contract->designation_id = $employee->designation->id;
                $this->department_id = $employee->designation->department_id ?? '';
            }
        }
    }
    
    public function save(): mixed
    {
        $this->validate();

        if($this->contract->employee->getActiveContract($this->contract->start_date, $this->contract->end_date)) {
            throw ValidationException::withMessages([
                'contract' => 'The selected employee already has an active contract during the specified period.',
            ]);
        }
        // Ensure department_id is set on the contract if needed
        if ($this->department_id) {
            $this->contract->department_id = $this->department_id;
        }
        
        $this->contract->save();
        session()->flash('success', 'Contract created successfully.');
        return $this->redirect(route('contracts.index'), navigate: true);
    }

    public function updatedDepartmentId(): void
    {
        // Reset designation when department changes
        $this->contract->designation_id = '';

        
    }

    public function render()
    {
        // Fix: Only load employees when searching, and use proper search scope
        $employees = $this->search 
            ? Employee::with(['designation.department'])
                ->inCompany()
                ->where('name', 'like', '%' . $this->search . '%')
                ->limit(10)
                ->get()
            : collect();

        $departments = Department::inCompany()->get();
        $designations = $this->department_id ? Department::find(id: $this->department_id)?->designations: collect();

        return view('livewire.admin.contracts.create', [
            'employees' => $employees,
            'departments' => $departments,
            'designations' => $designations,
        ]);
    }
}