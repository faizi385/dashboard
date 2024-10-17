@extends('layouts.admin')

@section('content')

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-2">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Offers List</h3>
        {{-- <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addOfferModal">
            Add Offer
        </button> --}}
        {{-- <div>
            @if(isset($lp)) <!-- Check if $lp is set -->
                <a href="{{ url()->previous() }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            @endif
        </div> --}}

    </div>

    <div class="card">
        <div class="card-header">
            @if(isset($lp)) <!-- Check if $lp is set -->
            <div class="d-flex justify-content-between">
                <h5 class="card-title">Offers for LP: {{ $lp->name }} ({{ $lp->dba }})</h5>
            </div>
            @else
                <h5 class="card-title">Offers for All LPs</h5> <!-- Fallback when $lp is not available -->
            @endif
        </div>
        
        <div class="card-body">
            <table id="offersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Provincial SKU</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Date</th>
                        <th>GTIN</th>
                        <th>Data Fee (%)</th>
                        <th>Cost ($)</th>
                        <th>Exclusive</th> <!-- New column for exclusive retailer -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                    <tr>
                        <td>{{ $offer->province }}</td>
                        <td>{{ $offer->product_name }}</td>
                        <td>{{ $offer->category }}</td>
                        <td>{{ $offer->brand }}</td>
                        <td>{{ $offer->provincial_sku }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->offer_start)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->offer_end)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->offer_date)->format('Y-m-d') }}</td>
                        <td>{{ $offer->gtin }}</td>
                        <td>{{ $offer->data_fee }}</td>
                        <td>{{ $offer->unit_cost }}</td>
                        <td>
                            @if($offer->retailer) <!-- Check if retailer relationship exists -->
                                {{ $offer->retailer->first_name . ' ' . $offer->retailer->last_name }} <!-- Concatenate first and last name -->
                            @else
                                ALL
                            @endif
                        </td>
                        {{-- <td>
                            @if($offer->retailer_id) <!-- Check if retailer_id exists -->
                                {{ $offer->retailer->first_name . ' ' . $offer->retailer->last_name }} <!-- Concatenate first and last name -->
                            @else
                                ALL
                            @endif
                        </td> --}}
                        
                        <td class="text-center">
                            <!-- Edit Offer Icon -->
                            <a href="{{ route('offers.edit', $offer->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Offer">
                                <i style="color: black" class="fas fa-edit "></i> <!-- Edit Icon -->
                            </a>
                            
                            <!-- Delete Offer Icon -->
                            <form action="{{ route('offers.destroy', $offer->id) }}" method="POST" style="display:inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class=" btn btn-link p-0 delete-offer" data-id="{{ $offer->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Offer">
                                    <i style="color: black" class="fas fa-trash "></i> <!-- Delete Icon -->
                                </button>
                            </form>
                        </td>
                        
                    </tr>
                    @empty
                    {{-- <tr>
                        <td colspan="13" class="text-center">No offers found.</td>
                    </tr> --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Offer Modal -->
<div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Offers to {{ $lp->name ?? null }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <!-- Bulk Offer Upload Option -->
                    <div>
                        <form action="{{ route('offers.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Hidden LP ID -->
                            <input type="hidden" name="lp_id" value="{{ $lp->id ?? null}}">
                            <!-- Display LP Name -->
                            <p><strong>LP:</strong> {{ $lp->name ?? null }} ({{ $lp->dba ?? null }})</p>

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
                        <a href="{{ route('offers.create', ['lp_id' => $lp->id ?? null]) }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Single Offer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include DataTables and SweetAlert -->

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        $('#offersTable').DataTable({
            responsive: true,
            "scrollX": true, // Enable horizontal scrolling
            "language": {
                "emptyTable": "No offers found." // Custom message for no data
            },
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
            }
        });

        // SweetAlert for delete confirmation
        $('.delete-offer').on('click', function() {
            const deleteForm = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteForm.submit(); // Submit the form to delete the offer
                }
            });
        });

        // Show success toast if session exists
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>

<style>
    .container {
        margin-top: 20px;
    }

  
    .dataTables_wrapper .dataTables_filter label{
        color: black
    }
</style>
@endsection
