@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>LP Details</h3>
        <div>
            <a href="{{ route('lp.index') }}" class="btn btn-secondary">Back to List</a>
            
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                Add Offer
            </button>
            
            <a href="{{ route('offers.index', ['lp_id' => $lp->id]) }}" class="btn btn-primary">
                View Offers
            </a>

            <button class="btn btn-info" onclick="window.location.href='{{ route('carveouts.index', ['lp_id' => $lp->id]) }}'">
                View Carveouts
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">LP Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">General Information</h6>
            <p><strong>LP Name:</strong> {{ $lp->name }}</p>
            <p><strong>DBA:</strong> {{ $lp->dba }}</p>
            <p><strong>Primary Contact Email:</strong> {{ $lp->primary_contact_email }}</p>
            <p><strong>Primary Contact Phone:</strong> {{ $lp->primary_contact_phone }}</p>
            <p><strong>Primary Contact Position:</strong> {{ $lp->primary_contact_position }}</p>
        </div>
    </div>

    @forelse($lp->address as $index => $address)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Address {{ $index + 1 }} Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Address Details</h6>
            <p><strong>Street No:</strong> {{ $address->street_number }}</p>
            <p><strong>Street Name:</strong> {{ $address->street_name }}</p>
            <p><strong>Postal Code:</strong> {{ $address->postal_code }}</p>
            <p><strong>City:</strong> {{ $address->city }}</p>
        </div>
    </div>
    @empty
    <p class="text-muted mt-4">No address details available.</p>
    @endforelse

</div>

<!-- Add Offer Modal -->
<div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Offers to {{ $lp->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <!-- Bulk Offer Upload Option -->
                    <div>
                        <form action="{{ route('offers.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Hidden LP ID -->
                            <input type="hidden" name="lp_id" value="{{ $lp->id }}">
                            <!-- Display LP Name -->
                            <p><strong>LP:</strong> {{ $lp->name }} ({{ $lp->dba }})</p>

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
                        <a href="{{ route('offers.create', ['lp_id' => $lp->id]) }}" class="btn btn-secondary">
                            <i class="fas fa-plus-circle"></i> Add Single Offer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
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

    .mt-4 {
        margin-top: 1.5rem;
    }

    .container {
        padding-bottom: 100px; /* Adjust this value based on footer height */
    }
</style>

<script>
    // Initialize Toastr notifications
    @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

@endsection
