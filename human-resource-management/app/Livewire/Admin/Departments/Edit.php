<?php

namespace App\Livewire\Admin\Departments;

use Livewire\Component;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;

class Edit extends Component
{
    public $department;
    public $department_name;

    public function rules(): array
    {
        return [
            'department.name' => 'required|string|max:255',
        ];
    }

    public function mount($id): void
    {
        $this->department = Department::find($id);

    }

    public function save(): mixed
    {
        $this->validate();
        $this->department->save();
        session()->flash('success', 'Department updated successfully.');
        return $this->redirectRoute('departments.index');
    }

    public function render()
    {
        return view('livewire.admin.departments.edit');
    }
}
