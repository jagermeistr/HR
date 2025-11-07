<div class="container mt-4">
    <h1>Collection Centers</h1>
    <a href="{{ route('collectioncenters.create') }}" class="btn btn-primary mb-3">+ Add Center</a>

    @if($collectionCenters->isEmpty())
        <p>No collection centers found.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Manager</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collectionCenters as $center)
                    <tr>
                        <td>{{ $center->name }}</td>
                        <td>{{ $center->location ?? 'N/A' }}</td>
                        <td>{{ $center->manager_name ?? 'N/A' }}</td>
                        <td>{{ $center->contact ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

