@extends('layouts.app')

@section('content')
<div class="container">
    <h1>LP Management</h1>
    
    <div class="col text-end mb-3">
        <a href="{{ route('lp.create') }}" class="btn btn-primary">Create LP</a>
    </div>
    
    @if(session('toast_success'))
        <div class="alert alert-success">{{ session('toast_success') }}</div>
    @endif

    <table id="lpTable" class="table table-striped table-bordered mt-3">
        <thead>
            <tr>
                <th>LP Name</th>
                <th>DBA</th>
                <th>Primary Contact Email</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lps as $lp)
                <tr>
                    <td>{{ $lp->name }}</td>
                    <td>{{ $lp->dba }}</td>
                    <td>{{ $lp->primary_contact_email }}</td>
                    <td class="text-center">
                        <a href="{{ route('lp.show', $lp->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="View LP">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('lp.edit', $lp) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit LP">Edit</a>
                        <form action="{{ route('lp.destroy', $lp) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete LP">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#lpTable').DataTable();
        
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

        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush
@endsection
