@extends('layouts.admin')

@section('content')
<div class="container p-3">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Edit Supplier</h3>
        <a href="{{ route('lp.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back </a>
    </div>

    <form action="{{ route('lp.update', $lp->id) }}" method="POST" id="lpForm">
        @csrf
        @method('PUT')

        <!-- LP Information Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Supplier Information</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="name"><i class="fas fa-user"></i> Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $lp->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dba" class="form-label">
                            <i class="fas fa-tag"></i> Organization Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" placeholder="Enter Organization Name" value="{{ old('dba', $lp->dba ?? '') }}" readonly>
                        @error('dba')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                 
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="primary_contact"><i class="fas fa-phone"></i> Primary Contact <span class="text-danger">*</span></label>
                        <input type="text" name="primary_contact" id="primary_contact" class="form-control @error('primary_contact') is-invalid @enderror" value="{{ old('primary_contact', $lp->primary_contact_phone) }}">
                        @error('primary_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_email"><i class="fas fa-envelope"></i> Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control @error('primary_contact_email') is-invalid @enderror" value="{{ old('primary_contact_email', $lp->primary_contact_email) }}" readonly>
                        @error('primary_contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                  
                </div>
                <div class="col-md-6 form-group">
                    <label for="primary_contact_position"><i class="fas fa-briefcase"></i> Contact Position</label>
                    <input type="text" name="primary_contact_position" id="primary_contact_position" class="form-control @error('primary_contact_position') is-invalid @enderror" value="{{ old('primary_contact_position', $lp->primary_contact_position) }}">
                    @error('primary_contact_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Address Information Card (If addresses are available) -->
       <!-- Address Information Card (If addresses are available) -->
@if($lpAddresses->isNotEmpty())
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title">Address Information</h5>
    </div>
    <div class="card-body">
        @foreach ($lpAddresses as $index => $address)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title">Address {{ $index + 1 }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <!-- Address Field -->
                        <div class="col-md-6 form-group">
                            <label for="address_{{ $index }}">
                                <i class="fas fa-map-marker-alt"></i> Address 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="address[{{ $index }}][address]" 
                                   id="address_{{ $index }}" 
                                   class="form-control @error('address.' . $index . '.address') is-invalid @enderror" 
                                   value="{{ old('address.' . $index . '.address', $address->address) }}">
                            @error('address.' . $index . '.address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                                <div class="col-md-6 form-group">
                                    <label for="address_{{ $index }}"><i class="fas fa-map-marker-alt"></i> Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address[{{ $index }}][address]" id="address_{{ $index }}" class="form-control @error('address.' . $index . '.address') is-invalid @enderror" value="{{ old('address.' . $index . '.address', $address->address) }}">
                                    @error('address.' . $index . '.address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                        <!-- Province Dropdown -->
                         <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label for="province_{{ $index }}">
                                        <i class="fas fa-map-marker-alt"></i> Province 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="address[{{ $index }}][province_id]" 
                                            id="province_{{ $index }}" 
                                            class="form-control @error('address.' . $index . '.province_id') is-invalid @enderror">
                                        <option value="">Select Province</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" 
                                                    {{ old('address.' . $index . '.province_id', $address->province_id) == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('address.' . $index . '.province_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        
                        <!-- City Field -->
                        <div class="col-md-6 form-group">
                            <label for="city_{{ $index }}">
                                <i class="fas fa-city"></i> City 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="address[{{ $index }}][city]" 
                                   id="city_{{ $index }}" 
                                   class="form-control @error('address.' . $index . '.city') is-invalid @enderror" 
                                   value="{{ old('address.' . $index . '.city', $address->city) }}">
                            @error('address.' . $index . '.city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>

                    <div class="form-row">

                        <!-- Postal Code Field -->
                        <div class="col-md-6 form-group">
                            <label for="postal_code_{{ $index }}">
                                <i class="fas fa-mail-bulk"></i> Postal Code 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="address[{{ $index }}][postal_code]" 
                                   id="postal_code_{{ $index }}" 
                                   class="form-control @error('address.' . $index . '.postal_code') is-invalid @enderror" 
                                   value="{{ old('address.' . $index . '.postal_code', $address->postal_code) }}">
                            @error('address.' . $index . '.postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif


        <!-- Submit Button -->
        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Supplier</button>
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

        document.querySelectorAll('#lpForm input[type="text"], #lpForm input[type="email"], #lpForm input[type="number"]').forEach(input => {
            removeValidationErrors(input);
        });
    });
</script>
@endpush
@endsection
