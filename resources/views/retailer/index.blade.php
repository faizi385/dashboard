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
                <th>Action</th>
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


@push('scripts')


<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#retailersTable').DataTable({
            "initComplete": function() {
                // Hide the loader once the table is initialized
             
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
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
    });
</script>
@endpush
