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
