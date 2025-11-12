<?php

namespace App\Livewire\Admin\CollectionCenters;

use Livewire\Component;
use App\Models\CollectionCenter;

class Index extends Component
{
    public function render()
    {
        $collectionCenters = CollectionCenter::latest()->get();

        return view('livewire.admin.collectioncenters.index', [
            'collectionCenters' => $collectionCenters
        ]);
    }
}