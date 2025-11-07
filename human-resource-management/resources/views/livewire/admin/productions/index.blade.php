<div class="container mt-4">
    <h1>Tea Production Records</h1>
    <a href="{{ route('productions.create') }}" class="btn btn-primary mb-3">+ Add New Record</a>

    @if($production_records->isEmpty())
        <p>No production records found.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Collection Center</th>
                    <th>Total Kgs</th>
                    <th>Produced On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($production_records as $production)
                    <tr>
                        <td>{{ $production->id }}</td>
                        <td>{{ $production->collection_center ? $production->collection_center->name : 'N/A' }}</td>
                        <td>{{ $production->total_kgs }}</td>
                        <td>{{ $production->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
