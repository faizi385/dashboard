@extends('layouts.admin')

@section('content')
<div class="container p-3">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Add Location</h3>
        <a href="{{ route('retailer.show', $retailer->id) }}" class="btn btn-primary"> <i class="fas fa-arrow-left"></i> Back</a>
    </div>
   <form action="{{ route('retailer.address.store', $retailer->id) }}" method="POST">
        @csrf
        <div id="address-forms">
            <div class="card address-form">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title">Address Information</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-address ml-auto" style="display:none;">
                        <i class="fas fa-trash-alt mr-2"></i> Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="street_no"><i class="fas fa-home"></i> Street No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('addresses.0.street_no') is-invalid @enderror" name="addresses[0][street_no]">
                            @error('addresses.0.street_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="street_name"><i class="fas fa-road"></i> Street Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('addresses.0.street_name') is-invalid @enderror" name="addresses[0][street_name]">
                            @error('addresses.0.street_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="province"><i class="fas fa-map-marker-alt"></i> Province <span class="text-danger">*</span></label>
                            <select class="form-control @error('addresses.0.province') is-invalid @enderror" name="addresses[0][province]">
                                <option value="" disabled selected>Select a province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('addresses.0.province')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="city-dropdown"><i class="fas fa-city"></i> City <span class="text-danger">*</span></label>
                            <select class="form-control @error('addresses.0.city') is-invalid @enderror city-dropdown" name="addresses[0][city]">
                                <option value="" disabled selected>Select a city</option>
                                <option value="Calgary">Calgary</option>
                                <option value="Vancouver">Vancouver</option>
                                <option value="Toronto">Toronto</option>
                                <option value="Winnipeg">Winnipeg</option>
                                <option value="Regina">Regina</option>
                                <option value="other">Other (Specify)</option>
                            </select>
                            @error('addresses.0.city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group custom-city-input" style="display:none;">
                            <label for="custom_city"><i class="fas fa-city"></i> Enter City Name </label>
                            <input type="text" class="form-control" name="addresses[0][custom_city]" placeholder="Enter your city name">
                            @error('addresses.0.custom_city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="location"><i class="fas fa-map-pin"></i> Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('addresses.0.location') is-invalid @enderror" name="addresses[0][location]">
                            @error('addresses.0.location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="contact_person_name"><i class="fas fa-user"></i> Contact Person Name</label>
                            <input type="text" class="form-control" name="addresses[0][contact_person_name]">
                            @error('addresses.0.contact_person_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="contact_person_phone"><i class="fas fa-phone"></i> Contact Person Phone</label>
                            <input type="text" class="form-control" name="addresses[0][contact_person_phone]">
                            @error('addresses.0.contact_person_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="add-address" class="btn btn-primary mt-3"><i class="fas fa-plus"></i> Add Another Address</button>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Save Locations</button>
    </form>
    <script>
let addressCount = 1;

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
const formInputs = document.querySelectorAll('#address-forms input[type="text"], #address-forms input[type="email"], #address-forms select');
formInputs.forEach(function (input) {
    removeValidationErrors(input);
});

// Function to display validation errors dynamically
function displayValidationErrors(input) {
    if (input.value.trim() === '') {
        input.classList.add('is-invalid');
        let errorDiv = input.nextElementSibling;

        // If error div does not exist, create it
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }

        errorDiv.style.display = 'block'; // Show the error message
        errorDiv.textContent = 'This field is required.'; // Custom message
    }
}

// Add new address form functionality
document.getElementById('add-address').addEventListener('click', function () {
    let newAddressForm = document.querySelector('.address-form').cloneNode(true);

    // Reset input values and attributes in the cloned form
    newAddressForm.querySelectorAll('input, select').forEach(function (input) {
        let newName = input.name.replace(/\d+/, addressCount); // Increment name index
        input.name = newName;
        input.id = `${newName.replace(/\[|\]/g, '_')}`; // Add unique ID for better validation
        input.value = '';
        input.classList.remove('is-invalid');

        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.style.display = 'none';
        }

        removeValidationErrors(input);
    });

    // Adjust labels' `for` attributes to match new IDs
    newAddressForm.querySelectorAll('label').forEach(function (label) {
        let targetInput = label.htmlFor.replace(/\d+/, addressCount);
        label.htmlFor = targetInput;
    });

    newAddressForm.querySelector('.remove-address').style.display = 'inline-block';
    document.getElementById('address-forms').appendChild(newAddressForm);
    addressCount++;

    handleCityDropdown(newAddressForm);
    validateClonedInputs(newAddressForm);
});

// Prevent form submission if any required field is empty
document.querySelector('form').addEventListener('submit', function (event) {
    let isValid = true;

    document.querySelectorAll('.address-form').forEach(function (form) {
        form.querySelectorAll('input[required], select[required]').forEach(function (input) {
            if (input.value.trim() === '') {
                displayValidationErrors(input);
                isValid = false;
            }
        });
    });

    if (!isValid) {
        event.preventDefault();
    }
});


// Validate each required field in a form on blur
function validateClonedInputs(form) {
    form.querySelectorAll('input[required], select[required]').forEach(function (input) {
        input.addEventListener('blur', function () {
            if (input.value.trim() === '') {
                displayValidationErrors(input);
            }
        });
    });
}

// Initial setup for existing forms
document.querySelectorAll('.address-form').forEach(function (form) {
    handleCityDropdown(form);
    validateClonedInputs(form); // Apply validation to existing form fields
});

// Handle city dropdown toggle
function handleCityDropdown(form) {
    form.querySelector('.city-dropdown').addEventListener('change', function () {
        const customCityInput = form.querySelector('.custom-city-input');
        if (this.value === 'other') {
            customCityInput.style.display = 'block';
        } else {
            customCityInput.style.display = 'none';
        }
    });
}

// Remove address form functionality with SweetAlert2 confirmation
document.addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-address')) {
        if (document.querySelectorAll('.address-form').length > 1) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This address will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('.address-form').remove();
                    Swal.fire('Removed!', 'The address has been removed.', 'success');
                }
            });
        } else {
            Swal.fire('Warning!', 'At least one address is required.', 'error');
        }
    }
});
</script>
</div>
@endsection
