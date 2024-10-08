@extends('layouts.app')

@section('content')
<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <h1>LP Management</h1>

    <div class="col text-end mb-3">
        <a href="{{ route('lp.create') }}" class="btn btn-primary">Create LP</a>
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
            Add Offer
        </button>
    </div>  

  
    <table id="lpTable" class="table table-striped table-bordered mt-3">
        <thead>
            <tr>
                <th>LP Name</th>
                <th>DBA</th>
                <th>Primary Contact Email</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lps as $lp)
                <tr>
                    <td>{{ $lp->name }}</td>
                    <td>{{ $lp->dba }}</td>
                    <td>{{ $lp->primary_contact_email }}</td>
                    <td class="text-center">
                        <a href="{{ route('lp.show', $lp->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="View ">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('lp.edit', $lp) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit ">Edit</a>
                        <form action="{{ route('lp.destroy', $lp) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete ">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Offer Modal -->
<!-- Add Offer Modal -->
<div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOfferModalLabel">Add Offers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <!-- Bulk Offer Upload Option -->
                    <div>
                        <form action="{{ route('offers.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="lpSelect" class="form-label">Select LP</label>
                                <select class="form-select" id="lpSelect" name="lp_id" required>
                                    <option value="" selected disabled>Select LP</option>
                                    @foreach($lps as $lp)
                                        <option value="{{ $lp->id }}">{{ $lp->name }} ({{ $lp->dba }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="offerExcel" class="form-label">Upload Bulk Offers (Excel)</label>
                                <input type="file" class="form-control" id="offerExcel" name="offerExcel" accept=".xlsx, .xls, .csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Excel
                            </button>
                        </form>
                    </div>

                    <!-- Single Offer Add Option -->
                    <div>
                        <a href="{{ route('offers.create') }}" class="btn btn-secondary">
                            <i class="fas fa-plus-circle"></i> Add Single Offer
                        </a>
                        {{-- <a href="{{ route('offers.export') }}" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Export Offers
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@push('scripts')


<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#lpTable').DataTable({
            "initComplete": function() {
        
            }
        });
        
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Delete confirmation
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

        // Toastr messages
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush
@endsection
