@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white my-4">{{ isset($lp) ? 'Edit LP' : 'Create LP' }}</h1>
        <a href="{{ route('lp.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form id="createLpForm" action="{{ isset($lp) ? route('lp.update', $lp) : route('lp.store') }}" method="POST">
            @csrf
            @if(isset($lp))
                @method('PUT')
            @endif

            <div class="row mb-3">
                <!-- LP Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-building"></i> LP Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter LP Name" value="{{ old('name', $lp->name ?? '') }}" >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- DBA -->
                <div class="col-md-6 mb-3">
                    <label for="dba" class="form-label">
                        <i class="fas fa-tag"></i> DBA <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" placeholder="Enter DBA" value="{{ old('dba', $lp->dba ?? '') }}" >
                    @error('dba')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- Primary Contact Email -->
                <div class="col-md-6 mb-3">
                    <label for="primary_contact_email" class="form-label">
                        <i class="fas fa-envelope"></i> Primary Contact Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control @error('primary_contact_email') is-invalid @enderror" placeholder="Enter Primary Contact Email" value="{{ old('primary_contact_email', $lp->primary_contact_email ?? '') }}" >
                    @error('primary_contact_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Primary Contact Phone -->
                <div class="col-md-6 mb-3">
                    <label for="primary_contact_phone" class="form-label">
                        <i class="fas fa-phone"></i> Primary Contact Phone <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="primary_contact_phone" id="primary_contact_phone" class="form-control @error('primary_contact_phone') is-invalid @enderror" placeholder="Enter Primary Contact Phone" value="{{ old('primary_contact_phone', $lp->primary_contact_phone ?? '') }}" minlength="9" maxlength="11">
                    @error('primary_contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- Primary Contact Position -->
                <div class="col-md-6 mb-3">
                    <label for="primary_contact_position" class="form-label">
                        <i class="fas fa-user-tie"></i> Primary Contact Position <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="primary_contact_position" id="primary_contact_position" class="form-control @error('primary_contact_position') is-invalid @enderror" placeholder="Enter Primary Contact Position" value="{{ old('primary_contact_position', $lp->primary_contact_position ?? '') }}" >
                    @error('primary_contact_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ isset($lp) ? 'Update LP' : 'Create LP' }}
            </button>
        </form>
    </div> <!-- End of white background div -->
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to remove 'is-invalid' class and error message when user starts typing
        function removeValidationErrors(input) {
            input.addEventListener('input', function () {
                if (input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
                    const errorDiv = input.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv.style.display = 'none'; // Hide the error message
                    }
                }
            });
        }

        // Add event listeners to all form inputs to remove validation errors when typing
        const formInputs = document.querySelectorAll('#createLpForm input[type="text"], #createLpForm input[type="email"]');
        formInputs.forEach(function (input) {
            removeValidationErrors(input);
        });
    });
</script>
@endpush
