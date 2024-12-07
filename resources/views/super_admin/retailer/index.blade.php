@extends('layouts.app')

@section('content')
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-2">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Distributor Management</h3>
        <a href="{{ route('retailer.create') }}" class="btn btn-primary">Create Distributor </a>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Distributor</h5>
        </div>
        <div class="card-body">
            <table id="retailersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Organization Name</th>
                        <th> Supplier Organization Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retailers as $retailer)
                        <tr>
                            <td>{{ $retailer->first_name }} {{ $retailer->last_name }}</td>
                            <td>{{ $retailer->dba ?? '-' }}</td>
                            <td>{{ $retailer->lp->dba ?? '-' }}</td>
                            <td>{{ $retailer->phone }}</td>
                            <td>{{ $retailer->email }}</td>
                            <td>{{ $retailer->type ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('retailer.show', $retailer->id) }}" class="icon-action text-decoration-none " data-bs-toggle="tooltip" data-bs-placement="top" title="View Distributor ">
                                    <i style="color: black" class="fas fa-eye "></i>
                                </a>
                                <a href="{{ route('retailer.edit', $retailer->id) }}" class="icon-action text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Distributor ">
                                    <i style="color: black" class="fas fa-edit "></i>
                                </a>
                                <form action="{{ route('retailer.destroy', $retailer->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Distributor " style="color: inherit; text-decoration: none;">
                                        <i style="color: black" class="fas fa-trash "></i>
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
@endsection

@push('scripts')

<script>
  $(document).ready(function() {
    $("#loader").fadeOut("slow");
    $('#retailersTable').DataTable({
        scrollX: true,
        autoWidth: false,
        "initComplete": function() {
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Initialize delete confirmation with event delegation
    $('#retailersTable').on('submit', '.delete-form', function(e) {
        e.preventDefault();

        const form = this; // Reference to the form that triggered the event

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

    // Display Toastr messages if available
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
});

</script>
@endpush
