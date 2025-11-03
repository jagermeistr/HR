<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl">Departments</flux:heading>
        <flux:subheading size="lg" class="mb-6"> Update Departments for {{ getCompany()->name}}</flux:subheading>
        <flux:separator/>
    </div>

    <form wire:submit="save" class="my-6 w-full space-y-6">
        <flux:input label="Department Name" wire:model.live="department.name" :invalid="$errors->has('department.name')" type="text"/>
        <flux:button variant="primary" type="submit">Save</flux:button>

    </form>

</div>
