<?php

namespace App\Livewire\Admin\CollectionCenters;

use Livewire\Component;
use App\Models\CollectionCenter;

class Create extends Component
{
    public $name, $location, $manager_name, $contact;

    public function render()
    {
        return view('livewire.admin.collectioncenters.create');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        CollectionCenter::create([
            'company_id' => session('company_id'),
            'name' => $this->name,
            'location' => $this->location,
            'manager_name' => $this->manager_name,
            'contact' => $this->contact,
        ]);

        session()->flash('success', 'Collection Center added successfully.');
        $this->reset();
    }
}

