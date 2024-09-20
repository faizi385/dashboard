<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Details</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        .btn-primary {
            border-radius: 0.25rem;
        }
        .address-field {
            position: relative;
            padding: 20px; /* Adjust padding as needed */
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 20px; /* Space between address fields */
        }
        .remove-address-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 0.875rem;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add shadow for better visibility */
        }
        .remove-address-btn:hover {
            background: #c82333;
        }
    </style>
    
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4 text-center">Retailer Information Form</h3>
    
        <form action="{{ route('retailer.submitForm') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="retailer_id" value="{{ $retailer->id }}">

            <!-- Personal Information -->
            <div class="row mb-4">
                <!-- First Name -->
                <div class="col-md-4 mb-3">
                    <label for="first_name" class="form-label"><i class="fas fa-user"></i> First Name</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $retailer->first_name }}" required>
                    <div class="invalid-feedback">Please provide a valid first name.</div>
                </div>

                <!-- Last Name -->
                <div class="col-md-4 mb-3">
                    <label for="last_name" class="form-label"><i class="fas fa-user"></i> Last Name</label>
                    <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $retailer->last_name }}" required>
                    <div class="invalid-feedback">Please provide a valid last name.</div>
                </div>

                <!-- Email -->
                <div class="col-md-4 mb-3">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ $retailer->email }}" required>
                    <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>
            </div>
    
            <div class="row mb-4">
                <!-- Phone Number -->
                <div class="col-md-4 mb-3">
                    <label for="phone" class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ $retailer->phone }}" required>
                    <div class="invalid-feedback">Please provide a valid phone number.</div>
                </div>
    
                <!-- Corporate Name -->
                <div class="col-md-4 mb-3">
                    <label for="corporate_name" class="form-label"><i class="fas fa-building"></i> Corporate Name</label>
                    <input type="text" class="form-control" name="corporate_name" id="corporate_name" value="{{ $retailer->corporate_name }}">
                </div>
    
                <!-- DBA -->
                <div class="col-md-4 mb-3">
                    <label for="dba" class="form-label"><i class="fas fa-tag"></i> DBA (Doing Business As)</label>
                    <input type="text" class="form-control" name="dba" id="dba" value="{{ $retailer->dba }}">
                </div>
            </div>
    
            <div class="row mb-4">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
    
                <!-- Confirm Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                </div>
            </div>
    
            <!-- Address Information -->
            <h4 class="mb-4">Address Information</h4>
    
            <div id="address-fields">
                <!-- Address Template -->
                <div class="address-field mb-4">
                    <button type="button" class="remove-address-btn" onclick="removeAddress(this)">
                        <i class="fas fa-trash-alt"></i> Remove
                    </button>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="street_no_0" class="form-label"><i class="fas fa-home"></i> Street No</label>
                            <input type="text" class="form-control" name="addresses[0][street_no]" id="street_no_0">
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="street_name_0" class="form-label"><i class="fas fa-road"></i> Street Name</label>
                            <input type="text" class="form-control" name="addresses[0][street_name]" id="street_name_0">
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="location_0" class="form-label"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" class="form-control" name="addresses[0][location]" id="location_0">
                        </div>
                    </div>
            
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="province_0" class="form-label"><i class="fas fa-globe"></i> Province</label>
                            <input type="text" class="form-control" name="addresses[0][province]" id="province_0">
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="city_0" class="form-label"><i class="fas fa-city"></i> City</label>
                            <input type="text" class="form-control" name="addresses[0][city]" id="city_0">
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="contact_person_name_0" class="form-label"><i class="fas fa-user-tie"></i> Contact Person Name</label>
                            <input type="text" class="form-control" name="addresses[0][contact_person_name]" id="contact_person_name_0">
                        </div>
                    </div>
            
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="contact_person_phone_0" class="form-label"><i class="fas fa-phone"></i> Contact Person Phone</label>
                            <input type="text" class="form-control" name="addresses[0][contact_person_phone]" id="contact_person_phone_0">
                        </div>
                    </div>
                </div>
            </div>
            
    
            <div class="text-center mt-4">
                <button type="button" class="btn btn-secondary" id="add-address"><i class="fas fa-plus"></i> Add Another Address</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        let addressIndex = 1;

        document.getElementById('add-address').addEventListener('click', function() {
            const addressFields = document.getElementById('address-fields');
            const newAddressField = document.createElement('div');
            newAddressField.classList.add('address-field', 'mb-4');
            newAddressField.innerHTML = `
                <button type="button" class="remove-address-btn" onclick="removeAddress(this)"><i class="fas fa-trash-alt"></i> Remove</button>
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="street_no_${addressIndex}" class="form-label"><i class="fas fa-home"></i> Street No</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][street_no]" id="street_no_${addressIndex}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="street_name_${addressIndex}" class="form-label"><i class="fas fa-road"></i> Street Name</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][street_name]" id="street_name_${addressIndex}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="location_${addressIndex}" class="form-label"><i class="fas fa-map-marker-alt"></i> Location</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][location]" id="location_${addressIndex}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="province_${addressIndex}" class="form-label"><i class="fas fa-globe"></i> Province</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][province]" id="province_${addressIndex}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="city_${addressIndex}" class="form-label"><i class="fas fa-city"></i> City</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][city]" id="city_${addressIndex}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="contact_person_name_${addressIndex}" class="form-label"><i class="fas fa-user-tie"></i> Contact Person Name</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][contact_person_name]" id="contact_person_name_${addressIndex}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="contact_person_phone_${addressIndex}" class="form-label"><i class="fas fa-phone"></i> Contact Person Phone</label>
                        <input type="text" class="form-control" name="addresses[${addressIndex}][contact_person_phone]" id="contact_person_phone_${addressIndex}">
                    </div>
                </div>
            `;
            addressFields.appendChild(newAddressField);
            addressIndex++;
        });

        function removeAddress(button) {
            console.log('Remove button clicked');
            const addressField = button.closest('.address-field');
            if (addressField) {
                addressField.remove();
            }
        }

        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
