@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Provinces</h1>
        <a href="{{ route('provinces.create') }}" class="btn btn-primary">Create Province</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="provincesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Timezone 1</th>
                <th>Timezone 2</th>
                <th>Tax Value</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($provinces as $province)
                <tr>
                    <td>{{ $province->name }}</td>
                    <td>{{ $province->slug }}</td>
                    <td>{{ $province->timezone_1 }}</td>
                    <td>{{ $province->timezone_2 }}</td>
                    <td>{{ $province->tax_value }}</td>
                    <td>{{ $province->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('provinces.edit', $province) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('provinces.destroy', $province) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@push('scripts')

<script>
    $(document).ready(function() {
        $('#provincesTable').DataTable();
        
        // Initialize delete confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit the form if confirmed
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
