@extends('layouts.app')

@section('content')
<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Retailer Management</h3>
        <a href="{{ route('retailer.create') }}" class="btn btn-success">Create Retailer</a>
    </div>

    <table id="retailersTable" class="table table-striped">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>DBA</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($retailers as $retailer)
                <tr>
                    <td>{{ $retailer->first_name }} {{ $retailer->last_name }}</td>
                    <td>{{ $retailer->dba }}</td>
                    <td>{{ $retailer->phone }}</td>
                    <td>{{ $retailer->email }}</td>
                    <td>
                        <a href="{{ route('retailer.show', $retailer->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View Retailer">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('retailer.edit', $retailer->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Retailer">Edit</a>
                        <form action="{{ route('retailer.destroy', $retailer->id) }}" method="POST" style="display:inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Retailer">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Loader CSS -->
<style>
    /* Full-screen loader */
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
        z-index: 9999;
    }

    .loader {
        border: 8px solid #f3f3f3;
        border-radius: 50%;
        border-top: 8px solid #3498db;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Hide loader initially */
    .hidden {
        display: none;
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
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables with loader
        $('#retailersTable').DataTable({
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
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
                    confirmButtonText: 'Yes, Delete It!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit the form if confirmed
                    }
                });
            });
        });

        // Display Toastr messages if available
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush
