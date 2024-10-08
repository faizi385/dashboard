@extends('layouts.app')

@section('content')
<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Retailer Management</h3>
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
                    <td>{{ $retailer->dba ?? '-' }}</td>
                    <td>{{ $retailer->phone }}</td>
                    <td>{{ $retailer->email }}</td>
                    <td class="text-center">
                        <!-- View Icon -->
                        <a href="{{ route('retailer.show', $retailer->id) }}" class="icon-action text-decoration-none " data-bs-toggle="tooltip" data-bs-placement="top" title="View Retailer">
                            <i style="color: black" class="fas fa-eye "></i>
                        </a>
        
                        <!-- Edit Icon -->
                        <a href="{{ route('retailer.edit', $retailer->id) }}" class="icon-action text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Retailer">
                            <i style="color: black" class="fas fa-edit "></i>
                        </a>
        
                        <!-- Delete Icon -->
                        <form action="{{ route('retailer.destroy', $retailer->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Retailer" style="color: inherit; text-decoration: none;">
                                <i style="color: black" class="fas fa-trash "></i>
                            </button>
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
