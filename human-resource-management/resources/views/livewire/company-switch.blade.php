<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\RedirectResponse;

new class extends Component {
    public $company;
    public function mount($company): void
    {
        $this->company = $company;
    }
    public function selectCompany($id): mixed
    {
        session(['company_id' => $this->company->id]);
        return $this->redirectIntended(URL::previous(), navigate: true);
    }
};
?>

<div>
    <flux:menu.item wire:click="selectCompany({{ $company->id }})">{{ $company->name }}</flux:menu.item>
</div>
