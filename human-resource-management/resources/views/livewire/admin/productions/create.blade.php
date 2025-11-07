<div class="container mt-4">
    <h1>Add Tea Production</h1>

    <form wire:submit.prevent="save">
        <div>
            <label>Collection Center:</label>
            <select wire:model="collection_center_id">
                <option value="">-- Select Center --</option>
                @foreach($collectioncenters as $center)
                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Total Kilograms Produced:</label>
            <input type="number" wire:model="total_kgs" min="0">
        </div>

       

        <button type="submit">Save Record</button>
    </form>

    @if (session()->has('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif
</div>
