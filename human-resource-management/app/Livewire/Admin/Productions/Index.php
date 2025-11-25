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


    public function formatDateSafely($dateString)
    {
        if (empty($dateString)) {
            return 'N/A';
        }

        try {
            // Clean the date string - remove any extra spaces or characters
            $dateString = trim($dateString);

            // Try to parse as YYYY-MM-DD first (your current import format)
            if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $dateString, $matches)) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $dateString)->format('M j, Y');
            }

            // Try to parse as DD/MM/YYYY (your template format)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString)->format('M j, Y');
            }

            // Fallback: try generic parsing
            return \Carbon\Carbon::parse($dateString)->format('M j, Y');
        } catch (\Exception $e) {
            // If all parsing fails, return the raw value
            return substr($dateString, 0, 10); // Return first 10 chars (YYYY-MM-DD portion)
        }
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
            $query->where(function ($q) {
                $q->where('total_kgs', 'like', '%' . $this->search . '%')
                    ->orWhereHas('collection_center', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Sorting - now defaults to production_date desc
        $query->orderBy($this->sortBy, $this->sortDirection);

        $production_records = $query->paginate($this->perPage);

        return view('livewire.admin.productions.index', compact('production_records'));
    }
}
