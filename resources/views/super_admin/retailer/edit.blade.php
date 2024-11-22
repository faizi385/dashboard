@extends('layouts.admin')

@section('content')
<div class="container p-3">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Edit Distributor </h3>
        <a href="{{ route('retailer.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back </a>
    </div>

    <form action="{{ route('retailer.update', $retailer->id) }}" method="POST" id="retailerForm">
        @csrf
        @method('PUT')

        <!-- Retailer Information Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Distributor  Information</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="first_name"><i class="fas fa-user"></i> First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $retailer->first_name) }}">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="last_name"><i class="fas fa-user"></i> Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $retailer->last_name) }}">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="corporate_name"><i class="fas fa-building"></i> Corporate Name</label>
                        <input type="text" name="corporate_name" id="corporate_name" class="form-control @error('corporate_name') is-invalid @enderror" value="{{ old('corporate_name', $retailer->corporate_name) }}">
                        @error('corporate_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="dba"><i class="fas fa-store"></i> DBA</label>
                        <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" value="{{ old('dba', $retailer->dba) }}">
                        @error('dba')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $retailer->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $retailer->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

      <!-- Address Information Card (If addresses are available) -->
@if($retailer->address->isNotEmpty())
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title">Address Information</h5>
    </div>
    <div class="card-body">
        @foreach ($retailer->address as $index => $address)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title">Address {{ $index + 1 }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="street_no_{{ $index }}"><i class="fas fa-home"></i> Street No <span class="text-danger">*</span></label>
                            <input type="text" name="address[{{ $index }}][street_no]" id="street_no_{{ $index }}" class="form-control @error('address.' . $index . '.street_no') is-invalid @enderror" value="{{ old('address.' . $index . '.street_no', $address->street_no) }}">
                            @error('address.' . $index . '.street_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="street_name_{{ $index }}"><i class="fas fa-road"></i> Street Name <span class="text-danger">*</span></label>
                            <input type="text" name="address[{{ $index }}][street_name]" id="street_name_{{ $index }}" class="form-control @error('address.' . $index . '.street_name') is-invalid @enderror" value="{{ old('address.' . $index . '.street_name', $address->street_name) }}">
                            @error('address.' . $index . '.street_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="province_{{ $index }}"><i class="fas fa-map-marker-alt"></i> Province <span class="text-danger">*</span></label>
                            <select name="address[{{ $index }}][province]" id="province_{{ $index }}"
                                    class="form-control @error('address.' . $index . '.province') is-invalid @enderror">
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}"
                                            {{ old('address.' . $index . '.province', $address->province_id) == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('address.' . $index . '.province')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label for="city_{{ $index }}"><i class="fas fa-city"></i> City <span class="text-danger">*</span></label>
                            <input type="text" name="address[{{ $index }}][city]" id="city_{{ $index }}" class="form-control @error('address.' . $index . '.city') is-invalid @enderror" value="{{ old('address.' . $index . '.city', $address->city) }}">
                            @error('address.' . $index . '.city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Additional address fields here with error handling as needed -->
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

        <!-- Submit Button -->
        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Distributor </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const removeValidationErrors = (input) => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
                const errorDiv = input.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.style.display = 'none';
                }
            });
        };

        document.querySelectorAll('#retailerForm input[type="text"], #retailerForm input[type="email"], #retailerForm input[type="number"]').forEach(input => {
            removeValidationErrors(input);
        });
    });
</script>
@endpush
@endsection
