@extends('layouts.admin')


@section('content')

<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white"><i class="fas fa-plus-circle text-white"></i> Add Single Deal</h1>
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white  p-4 rounded shadow-sm mb-4">
        <form action="{{ route('offers.store') }}" method="POST" id="offerForm">
            @csrf
            <input type="hidden" name="source" value="2"> 
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="lp_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                    @if(request('lp_id') && $lp)
                        <input type="hidden" name="lp_id" value="{{ request('lp_id') }}">
                        <input type="text" class="form-control" value="{{ $lp->name }}" disabled>
                    @else
                        <select name="lp_id" id="lp_id" class="form-control select2 @error('lp_id') is-invalid @enderror">
                            <option value="">Select Supplier</option>
                            @foreach($lps as $lp)
                                <option value="{{ $lp->id }}" {{ old('lp_id') == $lp->id ? 'selected' : '' }}>
                                    {{ $lp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('lp_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif
                </div>
                
                <div class="mb-3 col-md-6">
                    <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="product_name" id="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Enter Product Name" value="{{ old('product_name') }}" >
                    @error('product_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="provincial_sku" class="form-label">Provincial SKU <span class="text-danger">*</span></label>
                    <input type="text" name="provincial_sku" id="provincial_sku" class="form-control @error('provincial_sku') is-invalid @enderror" placeholder="Enter Provincial Sku" value="{{ old('provincial_sku') }}">
                    @error('provincial_sku')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label for="gtin" class="form-label">GTIN <span class="text-danger">*</span></label>
                    <input type="text" name="gtin" id="gtin" class="form-control @error('gtin') is-invalid @enderror" placeholder="Enter Gtin" value="{{ old('gtin') }}" >
                    @error('gtin')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                    <select name="province_id" id="province" class="form-control select2 @error('province') is-invalid @enderror">
                        <option value="">Select Province</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ old('province') == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('province')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="mb-3 col-md-6">
                    <label for="general_data_fee" class="form-label">General Data Fee (%) <span class="text-danger">*</span></label>
                    <input type="number" name="general_data_fee" id="general_data_fee" class="form-control @error('general_data_fee') is-invalid @enderror" placeholder="Enter General Data Fee Name" value="{{ old('general_data_fee') }}">
                    @error('general_data_fee')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="unit_cost" class="form-label">Unit Cost (excl. HST) <span class="text-danger">*</span></label>
                    <input type="number" name="unit_cost" id="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror" placeholder="Enter Unit Cost" value="{{ old('unit_cost') }}" >
                    @error('unit_cost')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" placeholder="Enter Category" value="{{ old('category') }}" >
                    @error('category')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
                    <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" placeholder="Enter Brand" value="{{ old('brand') }}" >
                    @error('brand')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label for="case_quantity" class="form-label">Case Quantity (Units per case) <span class="text-danger">*</span></label>
                    <input type="number" name="case_quantity" id="case_quantity" class="form-control @error('case_quantity') is-invalid @enderror" placeholder="Enter Case Quantity" value="{{ old('case_quantity') }}" >
                    @error('case_quantity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="offer_start" class="form-label">Offer Start <span class="text-danger">*</span></label>
                    <input type="date" name="offer_start" id="offer_start" class="form-control @error('offer_start') is-invalid @enderror" placeholder="Enter Offer Start" onchange="updateEndDate()" value="{{ old('offer_start') }}" >
                    @error('offer_start')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label for="offer_end" class="form-label">Offer End <span class="text-danger">*</span></label>
                    <input type="date" name="offer_end" id="offer_end" class="form-control @error('offer_end') is-invalid @enderror" placeholder="Enter Offer End" value="{{ old('offer_end') }}"  >
                    @error('offer_end')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>


            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="product_size" class="form-label">Product Size (g) <span class="text-danger">*</span></label>
                    <input type="number" name="product_size" id="product_size" class="form-control @error('product_size') is-invalid @enderror" placeholder="Enter Product Size" value="{{ old('product_size') }}" >
                    @error('product_size')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label for="thc_range" class="form-label">THC % Range <span class="text-danger">*</span></label>
                    <input type="text" name="thc_range" id="thc_range" class="form-control @error('thc_range') is-invalid @enderror" placeholder="Enter Thc Range" value="{{ old('thc_range') }}" >
                    @error('thc_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="cbd_range" class="form-label">CBD % Range <span class="text-danger">*</span></label>
                    <input type="text" name="cbd_range" id="cbd_range" class="form-control @error('cbd_range') is-invalid @enderror" placeholder="Enter Cbd Range" value="{{ old('cbd_range') }}"  >
                    @error('cbd_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label for="product_link" class="form-label">Link to Product and Brand Info </label>
                    <input type="url" name="product_link" id="product_link" class="form-control @error('product_link') is-invalid @enderror" placeholder="Enter Product Link" value="{{ old('product_link') }}"  >
                    @error('product_link')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="comment" class="form-label">Comment </label>
                    <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" placeholder="Enter  Comment" value="{{ old('comment') }}" ></textarea>
                    @error('comment')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

             
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="addExclusiveOfferCheckbox">
                <label class="form-check-label" for="addExclusiveOfferCheckbox">
                    Add An Exclusive Deal
                </label>
            </div>
            
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
                        <label for="retailer_ids" class="form-label">Select Distributor </label>
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
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="makeExclusiveOfferCheckbox" onclick="toggleExclusiveFields()">
                <label class="form-check-label" for="makeExclusiveOfferCheckbox">
                    Make It an Exclusive Deal for Specific Distributor 
                </label>
            </div>
            
            <div id="makeExclusiveOfferFields" style="display: none;">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="exclusive_retailer_ids" class="form-label">Select Distributor </label>
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
            
            <button type="submit" class="btn btn-primary">Add Deal</button>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('addExclusiveOfferCheckbox');
            const exclusiveFields = document.getElementById('addExclusiveOfferFields');
            const exclusiveDataFee = document.getElementById('exclusive_data_fee');
            const retailerIds = document.getElementById('retailer_ids');

            checkbox.addEventListener('change', function() {
                if (checkbox.checked) {
                    exclusiveFields.style.display = 'block';
                } else {
                    exclusiveFields.style.display = 'none';
                    exclusiveDataFee.value = '';
                    if (retailerIds) {
                        $(retailerIds).val(null).trigger('change');
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('makeExclusiveOfferCheckbox');
            const exclusiveFields = document.getElementById('makeExclusiveOfferFields');
            const retailerIds = document.getElementById('exclusive_retailer_ids');

            checkbox.addEventListener('change', function() {
                if (checkbox.checked) {
                    exclusiveFields.style.display = 'block';
                } else {
                    exclusiveFields.style.display = 'none';
                    if (retailerIds) {
                        $(retailerIds).val(null).trigger('change');
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function removeValidationErrors(input) {
                input.addEventListener('input', function () {
                    if (input.classList.contains('is-invalid')) {
                        input.classList.remove('is-invalid');
                        const errorDiv = input.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                            errorDiv.style.display = 'none'; 
                        }
                    }
                });
            }
            const formInputs = document.querySelectorAll('#offerForm input, #offerForm select, #offerForm textarea');
            formInputs.forEach(function (input) {
                removeValidationErrors(input);
            });
        });
        function toggleCheckbox(checkboxId, otherCheckboxId, fieldsId) {
            const checkbox = document.getElementById(checkboxId);
            const otherCheckbox = document.getElementById(otherCheckboxId);
            const fields = document.getElementById(fieldsId);
            fields.style.display = checkbox.checked ? 'block' : 'none';
            otherCheckbox.disabled = checkbox.checked;
        }
        document.getElementById('addExclusiveOfferCheckbox').addEventListener('change', function () {
            toggleCheckbox('addExclusiveOfferCheckbox', 'makeExclusiveOfferCheckbox', 'addExclusiveOfferFields');
        });

        document.getElementById('makeExclusiveOfferCheckbox').addEventListener('change', function () {
            toggleCheckbox('makeExclusiveOfferCheckbox', 'addExclusiveOfferCheckbox', 'makeExclusiveOfferFields');
        });
    function updateEndDate() {
        const startDateInput = document.getElementById('offer_start');
        const endDateInput = document.getElementById('offer_end');
        
        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(startDate.setMonth(startDate.getMonth() + 1));
            const year = endDate.getFullYear();
            const month = String(endDate.getMonth() + 1).padStart(2, '0');
            const day = String(endDate.getDate()).padStart(2, '0');
            endDateInput.value = `${year}-${month}-${day}`;
        }
    }
    </script>

@endsection
