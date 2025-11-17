<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductionRecord;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'production_date'; // Changed from 'id' to 'production_date'
    public $sortDirection = 'desc'; // Keep as 'desc' for newest first
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'production_date'], // Updated
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'desc'; // Default to desc when changing fields
        }
        
        $this->sortBy = $field;
    }

    public function render()
    {
        $query = ProductionRecord::with('collection_center');

        // Search functionality
        if ($this->search) {
            $query->where(function($q) {
                $q->where('total_kgs', 'like', '%'.$this->search.'%')
                  ->orWhereHas('collection_center', function($q) {
                      $q->where('name', 'like', '%'.$this->search.'%');
                  });
            });
        }

        // Sorting - now defaults to production_date desc
        $query->orderBy($this->sortBy, $this->sortDirection);

        $production_records = $query->paginate($this->perPage);

        return view('livewire.admin.productions.index', compact('production_records'));
    }
}