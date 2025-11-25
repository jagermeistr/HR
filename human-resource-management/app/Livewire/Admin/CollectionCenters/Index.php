<?php

namespace App\Livewire\Admin\CollectionCenters;

use Livewire\Component;
use App\Models\CollectionCenter;

class Index extends Component
{
    public $deleteId = null;
    public $showDeleteModal = false;

    // Delete confirmation modal
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    // Cancel delete
    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    // Perform delete
    public function deleteCenter()
    {
        try {
            $center = CollectionCenter::findOrFail($this->deleteId);
            $center->delete();

            $this->showDeleteModal = false;
            $this->deleteId = null;

            session()->flash('success', 'Collection center deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting collection center: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $collectionCenters = CollectionCenter::latest()->get();

        return view('livewire.admin.collectioncenters.index', [
            'collectionCenters' => $collectionCenters
        ]);
    }
}