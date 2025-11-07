<div class="container mt-4">
    <h1>Add Collection Center</h1>

    <form wire:submit.prevent="save">
        <div>
            <label>Name:</label>
            <input type="text" wire:model="name">
        </div>

        <div>
            <label>Location:</label>
            <input type="text" wire:model="location">
        </div>

        <div>
            <label>Manager Name:</label>
            <input type="text" wire:model="manager_name">
        </div>

        <div>
            <label>Contact:</label>
            <input type="text" wire:model="contact">
        </div>

        <button type="submit">Save</button>
    </form>

    @if(session()->has('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif
</div>

