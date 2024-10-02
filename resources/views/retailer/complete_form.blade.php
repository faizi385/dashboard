<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Details</title>

    <!-- Bootstrap and Font Awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #e9ecef; /* Light grey background */
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        h3, h4 {
            color: #343a40; /* Darker text color */
        }
        .form-label {
            font-weight: 600;
        }
        .form-control {
            border-radius: 0.5rem; /* Softer input field borders */
        }
        .btn-primary, .btn-secondary {
            border-radius: 0.5rem;
        }
        .address-field {
            position: relative;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 20px;
        }
        .remove-address-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 0.875rem;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .remove-address-btn:hover {
            background: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-primary:hover, .btn-secondary:hover {
            opacity: 0.9;
        }
        .container.mt-5 {
            padding-top: 40px;
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
                    <input type="tel" class="form-control" name="phone" id="phone" value="{{ $retailer->phone }}" required pattern="^\+?\d{1,2}\s?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$">
                    <div class="invalid-feedback">Please provide a valid phone number in the format +1 (952) 473-2014.</div>
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
                    <input type="password" class="form-control" name="password" id="password" minlength="8" required>
                    <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                </div>
    
                <!-- Confirm Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" minlength="8" required>
                    <div class="invalid-feedback">Passwords do not match.</div>
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
                            <input type="text" class="form-control" name="addresses[0][street_no]" id="street_no_0" required>
                            <div class="invalid-feedback">Please provide a valid street number.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="street_name_0" class="form-label"><i class="fas fa-road"></i> Street Name</label>
                            <input type="text" class="form-control" name="addresses[0][street_name]" id="street_name_0" required>
                            <div class="invalid-feedback">Please provide a valid street name.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="location_0" class="form-label"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" class="form-control" name="addresses[0][location]" id="location_0" required>
                            <div class="invalid-feedback">Please provide a valid location.</div>
                        </div>
                    </div>
            
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="province_0" class="form-label"><i class="fas fa-globe"></i> Province</label>
                            <input type="text" class="form-control" name="addresses[0][province]" id="province_0" required>
                            <div class="invalid-feedback">Please provide a valid province.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="city_0" class="form-label"><i class="fas fa-city"></i> City</label>
                            <input type="text" class="form-control" name="addresses[0][city]" id="city_0" required>
                            <div class="invalid-feedback">Please provide a valid city.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="postal_code_0" class="form-label"><i class="fas fa-envelope"></i> Postal Code</label>
                            <input type="text" class="form-control" name="addresses[0][postal_code]" id="postal_code_0" required pattern="^\d{5}(-\d{4})?$">
                            <div class="invalid-feedback">Please provide a valid postal code (5 digits).</div>
                        </div>
                    </div>
                </div>
            </div>
    
            <button type="button" class="btn btn-secondary" onclick="addAddress()">Add Address</button>

            <hr>
    
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Form validation
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Add address field
        let addressCount = 1;
        function addAddress() {
            const addressTemplate = `
                <div class="address-field mb-4">
                    <button type="button" class="remove-address-btn" onclick="removeAddress(this)">
                        <i class="fas fa-trash-alt"></i> Remove
                    </button>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="street_no_${addressCount}" class="form-label"><i class="fas fa-home"></i> Street No</label>
                            <input type="text" class="form-control" name="addresses[${addressCount}][street_no]" id="street_no_${addressCount}" required>
                            <div class="invalid-feedback">Please provide a valid street number.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="street_name_${addressCount}" class="form-label"><i class="fas fa-road"></i> Street Name</label>
                            <input type="text" class="form-control" name="addresses[${addressCount}][street_name]" id="street_name_${addressCount}" required>
                            <div class="invalid-feedback">Please provide a valid street name.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="location_${addressCount}" class="form-label"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" class="form-control" name="addresses[${addressCount}][location]" id="location_${addressCount}" required>
                            <div class="invalid-feedback">Please provide a valid location.</div>
                        </div>
                    </div>
            
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="province_${addressCount}" class="form-label"><i class="fas fa-globe"></i> Province</label>
                            <input type="text" class="form-control" name="addresses[${addressCount}][province]" id="province_${addressCount}" required>
                            <div class="invalid-feedback">Please provide a valid province.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="city_${addressCount}" class="form-label"><i class="fas fa-city"></i> City</label>
                            <input type="text" class="form-control" name="addresses[${addressCount}][city]" id="city_${addressCount}" required>
                            <div class="invalid-feedback">Please provide a valid city.</div>
                        </div>
            
                        <div class="col-md-4 mb-3">
                            <label for="postal_code_${addressCount}" class="form-label"><i class="fas fa-envelope"></i> Postal Code</label>
                            <input type="text" class="form-control" name="addresses[${addressCount}][postal_code]" id="postal_code_${addressCount}" required pattern="^\\d{5}(-\\d{4})?$">
                            <div class="invalid-feedback">Please provide a valid postal code (5 digits).</div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('address-fields').insertAdjacentHTML('beforeend', addressTemplate);
            addressCount++;
        }

        // Remove address field
        function removeAddress(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>
