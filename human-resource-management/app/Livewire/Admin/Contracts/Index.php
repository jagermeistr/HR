<?php

namespace App\Livewire\Admin\Contracts;

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Contract;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';

    public function delete($id): void
    {
        // Implement the delete logic here
        Contract::find($id)->delete();
        session()->flash('success', 'Contract deleted successfully.');
    }
    public function render()
    {
        return view('livewire.admin.contracts.index', [
            'contracts' => Contract::inCompany()->searchByEmployee($this->search)->paginate(perPage: 10),
        ]);
    }
}
