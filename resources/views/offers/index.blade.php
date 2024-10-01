@extends('layouts.admin')

@section('content')

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-2">
    <div class="d-flex justify-content-between mb-4">
        <h3>Offers List</h3>
        <div>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                Add Offer
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            @if(isset($lp)) <!-- Check if $lp is set -->
            <div class="d-flex justify-content-between">
                <h5 class="card-title">Offers for LP: {{ $lp->name }} ({{ $lp->dba }})</h5>
                <button class="btn btn-info" onclick="window.location.href='{{ route('carveouts.index', ['lp_id' => $lp->id]) }}'">
                    View Carveouts
                </button>
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
                            @if($offer->retailer_id) <!-- Check if retailer_id exists -->
                                {{ $offer->retailer->first_name . ' ' . $offer->retailer->last_name }} <!-- Concatenate first and last name -->
                            @else
                                N/A
                            @endif
                        </td>
                        
                        <td>
                            <a href="{{ route('offers.edit', $offer->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Offer">Edit</a>
                            <form action="{{ route('offers.destroy', $offer->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this offer?');" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Offer">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
               
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
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

        // Show success toast if session exists
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>

<style>
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999; /* Ensure the loader is on top of other content */
    }

    .loader {
        border: 8px solid #f3f3f3; /* Light gray */
        border-top: 8px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .hidden {
        display: none;
    }

    .container {
        margin-top: 20px;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #2c3e50;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
    }

    .card-body {
        padding: 15px;
        background-color: #f9f9f9;
    }

    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap; /* Prevent wrapping */
        overflow: hidden;    /* Hide overflow */
        text-overflow: ellipsis; /* Add ellipsis for overflow */
    }

    .table th {
        font-size: 0.85rem; /* Adjust header font size to be smaller */
        padding: 0.75rem; /* Optional: Adjust padding to reduce height */
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }
</style>
@endsection
