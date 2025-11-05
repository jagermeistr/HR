<?php

namespace App\Livewire\Admin\Companies;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Company;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    use WithFileUploads;
    public $company;
    public $logo;
    public function rules(): array
    {
        return [
            'company.name' => 'required|string|max:255|',
            'company.email' => 'required|email|max:255|',
            'company.website' => 'nullable|url|max:255|',
            'logo' => 'nullable|image|max:1024|mimes:jpg,png,jpeg,jpg|max:2048',
        ];
    }
    public function mount($id): void
    {
        $this->company = Company::find($id);
    }
    public function save(): mixed
    {
        $this->validate();

        if ($this->logo) {
            Storage::disk('public')->delete($this->company->logo);
            $this->company->logo = $this->logo->store('logos', 'public');
        }

        $this->company->save();
        session()->flash('success', 'Company updated successfully.');
        return $this->redirectIntended(route('companies.index'), navigate: true);
    }
    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasCompany($this->company->id)) {
            abort(403, 'Unauthorized action.');
        }
        return view('livewire.admin.companies.edit');
    }
}
