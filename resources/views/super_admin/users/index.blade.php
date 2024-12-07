@extends('layouts.app')

@section('content')
<h1 class="text-white" id="text">Users</h1>

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-2">

    <div class="row mb-4">
        <div class="col text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">Add User</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">User</h5>
        </div>
        <div class="card-body">
            <table id="example" class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th >Action</th> <!-- Centered Action header -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->roles->pluck('original_name')->implode(', ') }}</td>
                            <td class="text-center"> <!-- Center align the action column -->
                                <!-- Edit Icon -->
                                <a style="text-decoration: none" href="{{ route('users.edit', $user) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit User" class="icon-action">
                                    <i style="color: black" class="fas fa-edit "></i>
                                </a>

                                <!-- Delete Icon -->
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete User" style="color: inherit; text-decoration: none;">
                                        <i class="fas fa-trash "></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
  $(document).ready(function() {
    $("#loader").fadeOut("slow");

    // Initialize DataTable
    var table = $('#example').DataTable({
        "initComplete": function() {
            // Custom init code if needed
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Function to handle delete confirmation with SweetAlert
    function initializeDeleteConfirmation() {
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
    }

    // Call the function to initialize delete confirmation on page load
    initializeDeleteConfirmation();

    // Reinitialize the SweetAlert delete confirmation when DataTable is redrawn (pagination, search, etc.)
    table.on('draw', function() {
        initializeDeleteConfirmation();
    });

    // Display Toastr messages
    @if(session('toast_success'))
        toastr.success("{{ session('toast_success') }}");
    @endif
});

</script>
@endpush

@endsection
