@extends('layouts.app')

@section('content')
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>
<div class="container">
    <h1 class="text-white">Permissions</h1>
    
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
                    <!-- Permission Name -->
                    <td>{{ ucwords($permission->name) }}</td>
        
                    <!-- Permission Description -->
                    <td>{{ ucwords($permission->description) }}</td>
        
                    <!-- Action Icons for Edit and Delete -->
                    <td class="text-center">
                        <!-- Edit Icon -->
                        <a  href="{{ route('permissions.edit', $permission->id) }}" class="icon-action text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Permission">
                            <i style="color: black" class="fas fa-edit "></i>
                        </a>
        
                        <!-- Delete Icon -->
                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Permission" style="color: inherit; text-decoration: none;">
                                <i style="color: black"  class="fas fa-trash "></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
        
    </table>
</div>


<style>
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{
        color: white  !important;}
</style>
@push('scripts')
<!-- DataTables JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $("#loader").fadeOut("slow");
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
