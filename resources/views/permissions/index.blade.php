@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Permissions</h1>
    
    <div class="col text-end">
        <a href="{{ route('permissions.create') }}" class="btn btn-primary mb-3">Create New Permission</a>
    </div>
    
    <table id="permissionsTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->description }}</td>
                    <td class="text-center">
                        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Permission">Edit</a>
                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Permission">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<style>
    /* Button Hover Effect */
    .btn {
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn:hover {
        transform: scale(1.05); /* Slightly increase size */
        background-color: #0056b3; /* Darken the primary button color */
    }

    .btn-warning:hover {
        background-color: #e0a800; /* Darken the warning button color */
    }

    .btn-danger:hover {
        background-color: #c82333; /* Darken the danger button color */
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        $('#permissionsTable').DataTable();

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

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

    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
</script>
@endpush
@endsection
