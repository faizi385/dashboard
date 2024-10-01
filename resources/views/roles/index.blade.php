@extends('layouts.app')

@section('content')

<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <h1>Roles</h1>
    
    <div class="col text-end mb-3">
        <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="rolesTable" class="table table-bordered mt-3 custom-table">
        <thead>
            <tr>
                <th>Original Name</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td class="left-aligned">{{ $role->original_name }}</td>
                    <td class="text-center">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Role">Edit</a>
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Role">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<style>
    .custom-table {
        border-collapse: collapse; 
    }
    
    .custom-table td, .custom-table th {
        border: 1px solid #dee2e6; 
        vertical-align: middle; 
    }

    .custom-table td.left-aligned {
        text-align: left; 
    }

    .custom-table td.text-center {
        text-align: center; 
    }

    /* Loader Styles */
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999; /* Make sure it's above other elements */
    }

    .loader {
        border: 5px solid #f3f3f3; /* Light grey */
        border-top: 5px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 50px; /* Size of the loader */
        height: 50px; /* Size of the loader */
        animation: spin 1s linear infinite; /* Spin animation */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Bootstrap Bundle JS (for tooltips) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Show loader when the page is loading
        $("#loader").fadeOut("slow");

        // Initialize DataTables
        $('#rolesTable').DataTable();

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

        // Display Toastr messages
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush

@endsection
