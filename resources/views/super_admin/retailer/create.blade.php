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
                    <div class="text-danger">{{ $message }}</div>
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
            </div>

            <!-- Email and Phone Fields -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email" value="{{ old('email', $retailer->email ?? '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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
   <!-- Role Selection Radio Button Group -->
   <div class="mb-3">
    <label class="form-label">Select Type <span class="text-danger">*</span></label>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="type" id="distributor" value="Distributor" {{ old('type', $retailer->type ?? '') == 'Distributor' ? 'checked' : '' }}>
        <label class="form-check-label" for="distributor">
            Distributor
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="type" id="shop" value="Shop" {{ old('type', $retailer->type ?? '') == 'Shop' ? 'checked' : '' }}>
        <label class="form-check-label" for="shop">
            Shop
        </label>
    </div>
    @error('type')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-paper-plane"></i> {{ isset($retailer) ? 'Update Distributor' : 'Create Distributor' }}
            </button>
        </form>
    </div> <!-- End of white background div -->
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to remove 'is-invalid' class and error message when user interacts
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

        if (input.type === 'radio' || input.tagName === 'SELECT') {
            input.addEventListener('change', function () {
                const name = input.name;
                const radios = document.querySelectorAll(`input[name="${name}"]`);
                radios.forEach(function (radio) {
                    radio.classList.remove('is-invalid');
                });
                const errorDiv = input.closest('.form-check, .form-group')?.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.style.display = 'none'; // Hide the error message
                }
            });
        }
    }

    // Add event listeners to all form inputs and selects to remove validation errors on interaction
    const formInputs = document.querySelectorAll(
        '#retailerForm input[type="text"], #retailerForm input[type="email"], #retailerForm input[type="number"], #retailerForm select, #retailerForm input[type="radio"]'
    );
    formInputs.forEach(function (input) {
        removeValidationErrors(input);
    });

    // Form submit validation
    document.querySelector('#retailerForm').addEventListener('submit', function (event) {
        let isValid = true;
        let radioErrorShown = false; // Flag to check if error for radio buttons is already shown

        // Check all required inputs
        formInputs.forEach(function (input) {
            if (
                (input.type === 'radio' && !document.querySelector(`input[name="${input.name}"]:checked`)) ||
                (input.type !== 'radio' && input.value.trim() === '')
            ) {
                isValid = false;
                input.classList.add('is-invalid');
                let errorDiv = input.nextElementSibling;

                // Handle error for radio buttons and dropdowns
                if (input.type === 'radio' || input.tagName === 'SELECT') {
                    const parent = input.closest('.form-check, .form-group');
                    if (parent) {
                        errorDiv = parent.querySelector('.invalid-feedback');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.classList.add('invalid-feedback');
                            parent.appendChild(errorDiv);
                        }
                        errorDiv.textContent = 'This field is required.';
                        errorDiv.style.display = 'block';

                        // Only show the error for the last radio button in the group
                        if (!radioErrorShown) {
                            radioErrorShown = true;
                        } else {
                            errorDiv.style.display = 'none'; // Hide if it's not the last one
                        }
                    }
                } else {
                    // Handle other input types
                    if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv = document.createElement('div');
                        errorDiv.classList.add('invalid-feedback');
                        input.parentNode.insertBefore(errorDiv, input.nextSibling);
                    }
                    errorDiv.textContent = 'This field is required.';
                    errorDiv.style.display = 'block';
                }
            }
        });

        // Prevent form submission if any validation errors exist
        if (!isValid) {
            event.preventDefault();
        }
    });
});

</script>
@endpush
