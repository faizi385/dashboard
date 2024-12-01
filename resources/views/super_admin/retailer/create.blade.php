@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-user-plus"></i>
            {{ isset($retailer) ? 'Edit Distributor ' : 'Create Distributor ' }}
        </h1>
        <a href="{{ route('retailer.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ isset($retailer) ? route('retailers.update', $retailer) : route('retailers.store') }}" method="POST" id="retailerForm">
            @csrf
            @if(isset($retailer))
                @method('PUT')
            @endif
            @if(auth()->user()->hasRole('Super Admin'))
            <div class="mb-3 col-lg-6">
                <label for="lp_id" class="form-label">Select Supplier <span class="text-danger">*</span></label>
                <select name="lp_id" id="lp_id" class="form-select @error('lp_id') is-invalid @enderror">
                    <option value="">-- Select Supplier --</option>
                    @foreach($lps as $lp)
                        <option value="{{ $lp->id }}" {{ old('lp_id', $retailer->lp_id ?? '') == $lp->id ? 'selected' : '' }}>
                            {{ $lp->name }}
                        </option>
                    @endforeach
                </select>
                @error('lp_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            <!-- First Name and Last Name Fields -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">
                        <i class="fas fa-user"></i> First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter First Name" value="{{ old('first_name', $retailer->first_name ?? '') }}">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">
                        <i class="fas fa-user"></i> Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter Last Name" value="{{ old('last_name', $retailer->last_name ?? '') }}">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dba" class="form-label">
                        <i class="fas fa-tag"></i> Organization Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" placeholder="Enter Organization Name" value="{{ old('last_name', $retailer->dba ?? '') }}" >
                    @error('dba')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
           
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email" value="{{ old('email', $retailer->email ?? '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 </div>

            <!-- Email and Phone Fields -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone"></i> Phone Number <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter Phone Number" value="{{ old('phone', $retailer->phone ?? '') }}" minlength="9" maxlength="11">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-paper-plane"></i> {{ isset($retailer) ? 'Update Distributor' : 'Create Distributor' }}
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('retailerForm');

    // Validation function
    function validateForm(event) {
        let isValid = true;

        // Clear all previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(feedback => feedback.remove());

        // Validate text fields
        const textFields = ['first_name', 'last_name', 'email', 'phone'];
        textFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                showValidationError(input, `${capitalize(field.replace('_', ' '))} is required.`);
                isValid = false;
            }
        });

        // Validate dropdown
        const lpId = document.getElementById('lp_id');
        if (lpId && lpId.value === '') {
            showValidationError(lpId, 'Supplier selection is required.');
            isValid = false;
        }

        // Validate radio buttons
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const isTypeSelected = Array.from(typeRadios).some(radio => radio.checked);
        if (!isTypeSelected) {
            const typeContainer = typeRadios[0].closest('.mb-3');
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback', 'd-block');
            errorDiv.textContent = 'Select a type.';
            typeContainer.appendChild(errorDiv);
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    }

    // Show validation error
    function showValidationError(input, message) {
        input.classList.add('is-invalid');
        let errorDiv = input.closest('.mb-3')?.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            input.closest('.mb-3').appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    // Remove validation error dynamically
    function removeValidationError(event) {
        const input = event.target;
        if (input.classList.contains('is-invalid')) {
            input.classList.remove('is-invalid');
        }

        const errorDiv = input.closest('.mb-3')?.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Capitalize helper function
    function capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Attach the validation function to form submission
    form.addEventListener('submit', validateForm);

    // Attach dynamic error removal on input
    const formInputs = document.querySelectorAll('#retailerForm input, #retailerForm select');
    formInputs.forEach(input => {
        input.addEventListener('input', removeValidationError);
        if (input.type === 'radio') {
            input.addEventListener('change', removeValidationError);
        }
    });
});
</script>
@endpush

