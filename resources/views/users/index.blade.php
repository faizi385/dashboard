@extends('layouts.app')

@section('content')
<h1>Users</h1>

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container bg-light">

    <div class="row mb-4">
        <div class="col text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">Add User</a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="example" class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->roles->pluck('original_name')->implode(', ') }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm btn-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit User">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete User">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom styles for action buttons */
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
<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#example').DataTable({
            "initComplete": function() {
           
             
            }
        });

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
                        form.submit();
                    }
                });
            });
        });

        // Display Toastr messages
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush

@endsection
