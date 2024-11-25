@extends('layouts.admin')

@section('content')
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Supplier Details</h3>
        <div>
            <a href="{{ route('lp.index') }}" class="btn btn-primary ">Back </a>
            
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addOfferModal">
                Add Deal
            </button>
            
            <a href="{{ route('offers.index', ['lp_id' => $lp->id, 'from_lp_show' => 1]) }}" 
                class="btn btn-primary">
                View Deal
             </a>
             
             
             <button class="btn btn-primary" onclick="window.location.href='{{ route('carveouts.index', ['lp_id' => $lp->id]) }}'">
                View Carveouts
            </button>


            <button class="btn btn-primary" onclick="window.location.href='{{ route('lp.products.by.id', ['lp_id' => $lp->id]) }}'">
                View Products
            </button>

            <!-- View LP Statement Button -->
            <button class="btn btn-primary" onclick="window.location.href='{{ route('lp.statement.view', ['lp_id' => $lp->id]) }}'">
                View Supplier Statement
            </button>
            

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Supplier Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">General Information</h6>
            <p><strong>Supplier Name:</strong> {{ $lp->name }}</p>
            <p><strong>Organization Name:</strong> {{ $lp->dba }}</p>
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

<div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOfferModalLabel">Add Deals</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <!-- Bulk Offer Upload Option -->
                    <div>
                        <form action="{{ route('offers.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="lp_id" value="{{ $lp->id }}"> <!-- Hidden LP ID -->
                            <input type="hidden" name="source" value="1"> <!-- Source for bulk upload -->

                            <!-- Display LP Name instead of Dropdown -->
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <p><strong>{{ $lp->name }} ({{ $lp->dba }})</strong></p>
                            </div>

                            <!-- Radio buttons for Current Month and Previous Month -->
                         

                            <!-- File Upload Section -->
                            <div class="mb-3">
                                <label for="offerExcel" class="form-label">Upload Bulk Deals (Excel)</label>
                                <input type="file" class="form-control" id="offerExcel" name="offerExcel" accept=".xlsx, .xls, .csv">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Month</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="month" id="currentMonth" value="current" checked>
                                        <label class="form-check-label" for="currentMonth">
                                            Current Month
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="month" id="nextMonth" value="next">
                                        <label class="form-check-label" for="nextMonth">
                                            Next Month
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Excel
                            </button>
                        </form>
                    </div>

                    <!-- Single Offer Add Option -->
                    <div>
                        <a href="{{ route('offers.create', ['lp_id' => $lp->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Single Deal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />


<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .container {
        margin-top: 20px;
    }

   
    .container {
        padding-bottom: 100px; /* Adjust this value based on footer height */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        $("#loader").fadeOut("slow");

// Show the loader on form submission
$('form[action="{{ route('offers.import') }}"]').on('submit', function(e) {
    $("#loader").fadeIn("slow");
});   const modalForm = document.querySelector('form[action="{{ route('offers.import') }}"]');
        const offerExcel = modalForm.querySelector('#offerExcel');

        modalForm.addEventListener('submit', function (e) {
            // Clear previous errors
            const errorContainer = offerExcel.nextElementSibling;
            if (errorContainer) {
                errorContainer.remove();
            }

            let isValid = true;

            // Validate the file input
            if (!offerExcel.value.trim()) {
                isValid = false;

                // Add error message
                const errorMessage = document.createElement('div');
                errorMessage.classList.add('text-danger', 'mt-1');
                errorMessage.textContent = 'Please upload a valid file.';
                offerExcel.parentNode.appendChild(errorMessage);
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Remove error dynamically when user interacts
        offerExcel.addEventListener('change', function () {
            const errorContainer = offerExcel.nextElementSibling;
            if (errorContainer) {
                errorContainer.remove();
            }
        });
    });
    @if(session('toast_success'))
        toastr.success("{{ session('toast_success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>


@endsection
