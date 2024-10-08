@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white"><i class="fas fa-plus-circle text-white"></i> Add Single Offer</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('offers.store') }}" method="POST">
            @csrf
            <input type="hidden" name="source" value="2"> 
            <div class="row">
                <!-- LP -->
                <div class="mb-3 col-md-6">
                    <label for="lp_id" class="form-label">LP</label>
                    @if(request('lp_id') && $lp)
                    <!-- If lp_id is present in the request and $lp is defined, show the LP name and a hidden input -->
                    <input type="hidden" name="lp_id" value="{{ request('lp_id') }}">
                    <p><strong>{{ $lp->name }}</strong></p>
                @else
                    <!-- If lp_id is not present, show the dropdown -->
                    <select name="lp_id" id="lp_id" class="form-control @error('lp_id') is-invalid @enderror">
                        <option value="">Select LP</option>
                        @foreach($lps as $lp)
                            <option value="{{ $lp->id }}">{{ $lp->name }}</option>
                        @endforeach
                    </select>
                    @error('lp_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                @endif
                    @error('lp_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Product Name -->
                <div class="mb-3 col-md-6">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" name="product_name" id="product_name" class="form-control @error('product_name') is-invalid @enderror" >
                    @error('product_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            
            <div class="row">
                <!-- Provincial SKU -->
                <div class="mb-3 col-md-6">
                    <label for="provincial_sku" class="form-label">Provincial SKU</label>
                    <input type="text" name="provincial_sku" id="provincial_sku" class="form-control @error('provincial_sku') is-invalid @enderror" >
                    @error('provincial_sku')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- GTIN -->
                <div class="mb-3 col-md-6">
                    <label for="gtin" class="form-label">GTIN</label>
                    <input type="text" name="gtin" id="gtin" class="form-control @error('gtin') is-invalid @enderror" >
                    @error('gtin')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Province -->
                <div class="mb-3 col-md-6">
                    <label for="province" class="form-label">Province</label>
                    <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" >
                    @error('province')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Data Fee -->
                <div class="mb-3 col-md-6">
                    <label for="general_data_fee" class="form-label">General Data Fee (%)</label>
                    <input type="number" name="general_data_fee" id="general_data_fee" class="form-control @error('general_data_fee') is-invalid @enderror" >
                    @error('general_data_fee')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Unit Cost -->
                <div class="mb-3 col-md-6">
                    <label for="unit_cost" class="form-label">Unit Cost (excl. HST)</label>
                    <input type="number" name="unit_cost" id="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror" >
                    @error('unit_cost')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-3 col-md-6">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" >
                    @error('category')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Brand -->
                <div class="mb-3 col-md-6">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" >
                    @error('brand')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Case Quantity -->
                <div class="mb-3 col-md-6">
                    <label for="case_quantity" class="form-label">Case Quantity (Units per case)</label>
                    <input type="number" name="case_quantity" id="case_quantity" class="form-control @error('case_quantity') is-invalid @enderror" >
                    @error('case_quantity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Offer Start -->
                <div class="mb-3 col-md-6">
                    <label for="offer_start" class="form-label">Offer Start</label>
                    <input type="date" name="offer_start" id="offer_start" class="form-control @error('offer_start') is-invalid @enderror" onchange="updateEndDate()">
                    @error('offer_start')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Offer End -->
                <div class="mb-3 col-md-6">
                    <label for="offer_end" class="form-label">Offer End</label>
                    <input type="date" name="offer_end" id="offer_end" class="form-control @error('offer_end') is-invalid @enderror" >
                    @error('offer_end')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            <div class="row">
                <!-- Product Size -->
                <div class="mb-3 col-md-6">
                    <label for="product_size" class="form-label">Product Size (g)</label>
                    <input type="number" name="product_size" id="product_size" class="form-control @error('product_size') is-invalid @enderror" >
                    @error('product_size')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- THC Range -->
                <div class="mb-3 col-md-6">
                    <label for="thc_range" class="form-label">THC % Range</label>
                    <input type="text" name="thc_range" id="thc_range" class="form-control @error('thc_range') is-invalid @enderror" >
                    @error('thc_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- CBD Range -->
                <div class="mb-3 col-md-6">
                    <label for="cbd_range" class="form-label">CBD % Range</label>
                    <input type="text" name="cbd_range" id="cbd_range" class="form-control @error('cbd_range') is-invalid @enderror" >
                    @error('cbd_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Product Link -->
                <div class="mb-3 col-md-6">
                    <label for="product_link" class="form-label">Link to Product and Brand Info</label>
                    <input type="url" name="product_link" id="product_link" class="form-control @error('product_link') is-invalid @enderror" >
                    @error('product_link')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Comment -->
                <div class="mb-3 col-md-6">
                    <label for="comment" class="form-label">Comment</label>
                    <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" ></textarea>
                    @error('comment')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Offer Date -->
                {{-- <div class="mb-3 col-md-6">
                    <label for="offer_date" class="form-label">Offer Date</label>
                    <input type="date" name="offer_date" id="offer_date" class="form-control @error('offer_date') is-invalid @enderror" >
                    @error('offer_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div> --}}
            </div>
<!-- First Checkbox: Add An Exclusive Offer -->
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="addExclusiveOfferCheckbox" name="exclusive_offer" value="1">
    <label class="form-check-label" for="addExclusiveOfferCheckbox">
        Add An Exclusive Offer
    </label>
</div>

<!-- Fields for 'Add An Exclusive Offer' -->
<div id="addExclusiveOfferFields" style="display: none;">
    <div class="row">
        <!-- Exclusive Data Fee -->
        <div class="mb-3 col-md-6">
            <label for="exclusive_data_fee" class="form-label">Exclusive Data Fee (%)</label>
            <input type="number" name="exclusive_data_fee" id="exclusive_data_fee" class="form-control @error('exclusive_data_fee') is-invalid @enderror">
            @error('exclusive_data_fee')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Retailers -->
        <div class="mb-3 col-md-6">
            <label for="retailer_ids" class="form-label">Select Retailers</label>
            <select name="retailer_ids[]" id="retailer_ids" class="form-control select2 @error('retailer_ids') is-invalid @enderror" multiple>
                @foreach($retailers as $retailer)
                    <option value="{{ $retailer->id }}">{{ $retailer->dba }}</option>
                @endforeach
            </select>
            @error('retailer_ids')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<!-- Second Checkbox: Make It Exclusive to Specific Retailers -->
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="makeExclusiveOfferCheckbox" name="makeExclusiveOfferCheckbox" value="1" onclick="toggleExclusiveFields()">
    <label class="form-check-label" for="makeExclusiveOfferCheckbox">
        Make It an Exclusive Offer for Specific Retailers
    </label>
</div>

<!-- Fields for 'Make It an Exclusive Offer for Retailers' -->
<div id="makeExclusiveOfferFields" style="display: none;">
    <div class="row">
        <!-- Retailer Names for Second Checkbox -->
        <div class="mb-3 col-md-6">
            <label for="exclusive_retailer_ids" class="form-label">Select Retailers</label>
            <select name="exclusive_retailer_ids[]" id="exclusive_retailer_ids" class="form-control select2 @error('exclusive_retailer_ids') is-invalid @enderror" multiple>
                @foreach($retailers as $retailer)
                    <option value="{{ $retailer->id }}">{{ $retailer->dba }}</option>
                @endforeach
            </select>
            @error('exclusive_retailer_ids')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success">Add Offer</button>
        </form>
    </div>
</div>

<!-- JavaScript -->

<script>
    // Select2 Initialization
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select Retailers',
            tags: true // Allows adding new tags if needed
        });
    });

    // Function to update the end date to one month after the start date
    function updateEndDate() {
        const startDate = document.getElementById('offer_start').value;
        const endDateInput = document.getElementById('offer_end');

        if (startDate) {
            const start = new Date(startDate);
            start.setMonth(start.getMonth() + 1); // Add one month
            const year = start.getFullYear();
            const month = String(start.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
            const day = String(start.getDate()).padStart(2, '0');
            endDateInput.value = `${year}-${month}-${day}`; // Set the end date value
        } else {
            endDateInput.value = ''; // Clear end date if start date is empty
        }
    }

    // Toggle 'Add An Exclusive Offer' Fields
    document.getElementById('addExclusiveOfferCheckbox').addEventListener('change', function () {
        var addExclusiveFields = document.getElementById('addExclusiveOfferFields');
        addExclusiveFields.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle 'Make It an Exclusive Offer' Fields
    function toggleExclusiveFields() {
        const checkbox = document.getElementById('makeExclusiveOfferCheckbox');
        const exclusiveFields = document.getElementById('makeExclusiveOfferFields');
        exclusiveFields.style.display = checkbox.checked ? 'block' : 'none';
    }
</script>
@endsection
