<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    
        .form-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 20px;
        }
    
        .form-wrapper {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    
        .form-group {
            margin-bottom: 16px;
        }
    
        .form-label {
            font-weight: normal;
        /* Removed bold text */
        }
    
 



        .primary-btn {
            background-color: #171718;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    
        .primary-btn:hover {
            background-color: #0056b3;
        }
    </style>
    
</head>
<body>

    <!-- Form Container for Centering -->
    <div class="form-container">
        <div class="form-wrapper">
            <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                @csrf
                <h3 class="mb-4 text-center">Complete Your Supplier Details</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> {{ __('Full Name') }} <span class="text-danger">*</span>
                            </label>
                            <input 
                                id="name" 
                                class="form-control" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                autocomplete="name" 
                                placeholder="Enter your full name"
                                pattern="[A-Za-z\s]+" 
                                title="Full name must contain only alphabets and spaces."
                            />
                            <div class="invalid-feedback">
                                Full Name is required with only alphabets .
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dba" class="form-label"><i class="fas fa-building"></i> {{ __('DBA (Doing Business As)') }} <span class="text-danger">*</span></label>
                            <input id="dba" class="form-control" type="text" name="dba" value="{{ old('dba') }}" required placeholder="Enter DBA name" />
                            <div class="invalid-feedback">
                                DBA is required.
                            </div>
                        
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> {{ __('Email') }} <span class="text-danger">*</span>
                            </label>
                            <input 
                                id="email" 
                                class="form-control" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                placeholder="Enter your email address" 
                                oninput="validateEmail(this)" 
                            />
                            <div class="invalid-feedback">
                                Email must end with ".com".
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        function validateEmail(input) {
                            const emailValue = input.value;
                            if (emailValue && !emailValue.endsWith('.com')) {
                                input.setCustomValidity('Email must end with ".com".');
                            } else {
                                input.setCustomValidity(''); // Clear custom validation message
                            }
                        }
                    </script>
                    
                </div>

                <!-- Primary Contact Phone and Position -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="primary_contact_phone" class="form-label">
                                <i class="fas fa-phone-alt"></i> {{ __('Primary Contact Phone') }} <span class="text-danger">*</span>
                            </label>
                            <input id="primary_contact_phone" class="form-control" type="tel" name="primary_contact_phone" value="{{ old('primary_contact_phone') }}" required placeholder="Enter phone number" pattern="(\+1\s?)?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{4}" />
                                <div class="invalid-feedback">
                                    Phone is required.
                                </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="primary_contact_position" class="form-label"><i class="fas fa-briefcase"></i> {{ __('Primary Contact Position') }} <span class="text-danger">*</span></label>
                            <input id="primary_contact_position" class="form-control" type="text" name="primary_contact_position" value="{{ old('primary_contact_position') }}" required placeholder="Enter position of contact person" />
                            <div class="invalid-feedback">
                                 Contact Position is required.
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> {{ __('Address') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="address[address]" value="{{ old('address.address') }}" required class="form-control" id="address" />
                    
                            <div class="invalid-feedback">
                                Address is required.
                            </div>
                        </div>
                    </div>
                    

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.postal_code" class="form-label"><i class="fas fa-map-pin"></i> {{ __('Postal Code') }} <span class="text-danger">*</span></label>
                            <input id="address.postal_code" class="form-control" type="text" name="address[postal_code]" value="{{ old('address.postal_code') }}" required pattern="^\d{5}(-\d{4})?$" placeholder="Postal code" />
                            <div class="invalid-feedback">
                               Postal Code is required.
                             </div>
                       
                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.city" class="form-label"><i class="fas fa-city"></i> {{ __('City') }} <span class="text-danger">*</span></label>
                            <input id="address.city" class="form-control" type="text" name="address[city]" value="{{ old('address.city') }}" required placeholder="City name" />
                            <div class="invalid-feedback">
                             City is required.
                             </div>
                       
                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.province" class="form-label"><i class="fas fa-map"></i> {{ __('Province') }} <span class="text-danger">*</span></label>
                            <select id="address.province" class="form-control" name="address[province]" required> 
                                <option value="">{{ __('Select Province') }}</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" @if(old('address.province') == $province->id) selected @endif>{{ $province->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                            Province is required.
                             </div>
                       
                         </div>
                    </div>
                </div>
    
                <!-- City and Province -->
                <div class="row">
             
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password" class="form-label"><i class="fas fa-lock"></i> {{ __('Password') }} <span class="text-danger">*</span></label>
                            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="Enter your password" />
                            <div class="invalid-feedback">
                               Password is required.
                           </div>
                      
                       </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label"><i class="fas fa-lock"></i> {{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                            <div class="invalid-feedback">
                                Confirm Password is required.
                           </div>
                       
                       </div>
                    </div>

                  
                </div>
          
                <div class="submit-btn">
                    <button type="submit" class="primary-btn">{{ __('Submit') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
    <!-- JavaScript to Enable Form Validation -->
    <script>
        (function () {
            'use strict';
    
            // Enable validation on forms
            var forms = document.querySelectorAll('.needs-validation');
    
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    var emailInput = form.querySelector('#email');
                    var emailError = form.querySelector('#email + .invalid-feedback');
    
                    // Custom email validation logic
                    if (emailInput) {
                        var emailValue = emailInput.value;
                        if (!emailValue.endsWith('.com')) {
                            emailInput.setCustomValidity('Email must end with .com');
                            emailError.textContent = 'Please enter a valid email ';
                        } else {
                            emailInput.setCustomValidity(''); // Clear custom error
                        }
                    }
    
                    // General form validation
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
    

</body>
</html>
