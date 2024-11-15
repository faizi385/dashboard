@extends('layouts.admin')

@section('content')
<div class="container p-3">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Edit LP</h3>
        <a href="{{ route('lp.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back </a>
    </div>

    <form action="{{ route('lp.update', $lp->id) }}" method="POST" id="lpForm">
        @csrf
        @method('PUT')

        <!-- LP Information Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">LP Information</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="name"><i class="fas fa-user"></i> LP Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $lp->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="primary_contact"><i class="fas fa-phone"></i> Primary Contact <span class="text-danger">*</span></label>
                        <input type="text" name="primary_contact" id="primary_contact" class="form-control @error('primary_contact') is-invalid @enderror" value="{{ old('primary_contact', $lp->primary_contact) }}">
                        @error('primary_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_email"><i class="fas fa-envelope"></i> Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control @error('primary_contact_email') is-invalid @enderror" value="{{ old('primary_contact_email', $lp->primary_contact_email) }}">
                        @error('primary_contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Address Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="address_line_1"><i class="fas fa-map-marker-alt"></i> Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" name="address_line_1" id="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" value="{{ old('address_line_1', $lp->address_line_1) }}">
                        @error('address_line_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
        
                    <div class="col-md-6 form-group">
                        <label for="address_line_2"><i class="fas fa-map-marker-alt"></i> Address Line 2</label>
                        <input type="text" name="address_line_2" id="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror" value="{{ old('address_line_2', $lp->address_line_2) }}">
                        @error('address_line_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
        
                <div class="form-row">
                    <div class="col-md-4 form-group">
                        <label for="city"><i class="fas fa-city"></i> City <span class="text-danger">*</span></label>
                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $lp->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
        
                    <div class="col-md-4 form-group">
                        <label for="province"><i class="fas fa-map"></i> Province <span class="text-danger">*</span></label>
                        <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $lp->province) }}">
                        @error('province')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
        
                    <div class="col-md-4 form-group">
                        <label for="postal_code"><i class="fas fa-mail-bulk"></i> Postal Code <span class="text-danger">*</span></label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $lp->postal_code) }}">
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update LP</button>
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
