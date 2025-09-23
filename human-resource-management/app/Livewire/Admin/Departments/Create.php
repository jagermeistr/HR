<?php

namespace App\Livewire\Admin\Departments;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;

class Create extends Component
{
    public $department;
    public function rules(): array
    {
        return [
            'department.name' => 'required|string|max:255|',
        ];
    }
    public function mount(): void
    {
        $this->department = new Department();
    }
    public function save(): mixed
    {
        $this->validate();
        $this->department->company_id = session('company_id');
        $this->department->save();

        session()->flash('success', 'Department created successfully.');
        return$this->redirectIntended('departments.index');
    }

    public function render()
    {
        return view('livewire.admin.departments.create');
    }
}
