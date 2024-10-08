@extends('layouts.app')

@section('content')

<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <h1 class="text-white" id="text">Roles</h1>
    
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
                        <!-- Edit Icon -->
                        <a style="text-decoration: none" href="{{ route('roles.edit', $role) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Role">
                            <i style="color: black"  class="fas fa-edit "></i>
                        </a>
                        
                        <!-- Delete Icon -->
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 " data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Role" style="color: inherit; text-decoration: none;">
                                <i style="color: black"  class="fas fa-trash "></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<style>
    /* Custom styles for action buttons */
    .btn {
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-primary {
        background-color:  #B8AC92;
        border-color: #B8AC92;
        font-family: 'Ivy Mode'!important;
        font-size: 16px !important;
        font-weight: 400 !important;
        line-height: 24px !important;
        text-align: left !important;
    }
    
    /* Centered icons with equal size */
   
    /* Hover effects for icons */



    /* h2, h3, h4, h5, h6, p, th, td {
        font-family: 'Outfit' !important;
        font-size: 16px !important;
        font-weight: 400 !important;
        line-height: 24px !important;
        text-align: left !important;
    }

 
    table thead th, table tbody td {
        font-family: 'Outfit' !important;
        font-size: 16px !important;
        font-weight: 400 !important;
        line-height: 24px !important;
        text-align: left !important;
    } */

    /* Styling for DataTables pagination, search, etc. */
    #text {
        font-family: 'Outfit';
        font-size: 38.44px;
        font-weight: 600;
        line-height: 48px;
        text-align: left;
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
