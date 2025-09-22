<?php

namespace App\Livewire\Admin\Companies;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Index extends Component
{
    public function delete($id): void
    {
        $company= Company::find($id);
        if ($company->logo){
            Storage::disk(name: 'public')->delete($company->logo);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.companies.index');
    }
}
