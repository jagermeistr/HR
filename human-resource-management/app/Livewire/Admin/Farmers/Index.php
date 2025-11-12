<?php

namespace App\Livewire\Admin\Farmers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Farmer;
use Livewire\WithoutUrlPagination;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;
    public function delete($id): void
    {
        Farmer::find($id)->delete();
        Session()->flash('success', 'farmer deleted successfully.');
    }
    public function render()
    {
        return view('livewire.admin.farmers.index', [
      'farmers' => Farmer::paginate(5)

        ]);
    }
}
