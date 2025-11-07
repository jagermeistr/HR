<?php

namespace App\Livewire\Admin\Contracts;

use App\Models\Contract;
use Livewire\Component;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Designation;
use Illuminate\Validation\ValidationException;

class Edit extends Component
{
    public $contract;
    public $search = '';
    public $department_id;
    public $isLoading = false;

    public function rules(): array
    {
        return [
            'contract.designation_id' => 'required|exists:designations,id',
            'contract.employee_id' => 'required|exists:employees,id',
            'contract.start_date' => 'required|date',
            'contract.end_date' => 'required|date|after:contract.start_date',
            'contract.rate_type' => 'required|in:hourly,daily,monthly',
            'contract.rate' => 'required|numeric|min:0',
        ];
    }

    public function mount($id): void
    {
        $this->contract = Contract::with(['employee', 'designation.department'])
            ->findOrFail($id);

        $this->search = $this->contract->employee->name;
        $this->department_id = $this->contract->designation->department_id;
    }
    
    public function selectEmployee($id): void
    {
        try {
            $employee = Employee::findOrFail($id);
            $this->contract->employee_id = $employee->id;
            $this->search = $employee->name;
            $this->isLoading = false;
            
            // Clear any previous employee validation errors
            $this->resetErrorBag('contract.employee_id');
        } catch (\Exception $e) {
            $this->addError('contract.employee_id', 'Employee not found.');
        }
    }
    
    public function save()
    {
        $this->validate();

        try {
            // For admin users, we'll allow saving even if there are overlapping contracts
            // but we'll show a warning message instead of preventing the save
            
            $existingContract = $this->contract->employee
                ->getActiveContract($this->contract->start_date, $this->contract->end_date, $this->contract->id);

            if ($existingContract) {
                // Instead of throwing a validation exception, show a warning but allow save
                session()->flash('warning', 
                    "Warning: This employee already has an active contract during this period. " .
                    "Contract #{$existingContract->id} ({$existingContract->start_date} to {$existingContract->end_date}) " .
                    "may overlap with this contract."
                );
            }

            $this->contract->save();
            
            session()->flash('success', 'Contract updated successfully.');
            return $this->redirect(route('contracts.index'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update contract. Please try again.');
            logger()->error('Contract update failed: ' . $e->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->isLoading = $this->search !== '' && 
            $this->search !== $this->contract->employee->name;
            
        // Clear employee selection if search is changed
        if ($this->search !== $this->contract->employee->name) {
            $this->contract->employee_id = null;
        }
    }

    public function updatedDepartmentId($value): void
    {
        $this->contract->designation_id = null;
        
        // Clear designation validation errors when department changes
        $this->resetErrorBag('contract.designation_id');
    }

    public function render()
    {
        $employees = [];
        
        if ($this->search) {
            $employees = Employee::searchByName($this->search)
                ->limit(10)
                ->get();
        }

        $departments = Department::all();
        $designations = $this->department_id 
            ? Designation::where('department_id', $this->department_id)->get()
            : collect();

        return view('livewire.admin.contracts.edit', [
            'employees' => collect($employees),
            'departments' => $departments,
            'designations' => $designations,
        ]);
    }
}