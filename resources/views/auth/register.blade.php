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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
        .loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Make sure it's above other elements */
}

.loader {
    border: 5px solid #f3f3f3; /* Light grey */
    border-top: 5px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 50px; /* Size of the loader */
    height: 50px; /* Size of the loader */
    animation: spin 1s linear infinite; /* Spin animation */
}
    .invalid-feedback {
        display: none;
        color: red; 
    }
    .is-invalid + .invalid-feedback {
        display: block; 
    }

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

      .needs-validation .form-control:valid {
    background-image: none !important; /* Remove the tick icon */
    border-color: #ced4da; /* Restore default border color */
}   

    </style>
    


</head>
<body>
    <!-- <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div> -->
    <!-- Form Container for Centering -->
    <div class="form-container">
        <div class="form-wrapper">
            <form method="POST" action="{{ route('register') }}" class="needs-validation" id="register_form" novalidate>
                @csrf
                <h3 class="mb-4 text-center">Partner with Us – Supplier Registration</h3>
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
                                Enter your valid full name with only alphabets
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dba" class="form-label"><i class="fas fa-building"></i> {{ __('Organization Name') }} <span class="text-danger">*</span></label>
                            <input id="dba" class="form-control" type="text" name="dba" value="{{ old('dba') }}" required placeholder="Enter Organization Name" />
                            <div class="invalid-feedback">
                                Provide your valid organization name
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
                                Enter a valid email address
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
                            <input 
                                id="primary_contact_phone" 
                                class="form-control" 
                                type="tel" 
                                name="primary_contact_phone" 
                                value="{{ old('primary_contact_phone') }}" 
                                required 
                                placeholder="Enter phone number"
                                oninput="validatePhone(this)" 
                            />
                            <div id="invalid-feedback" style="display: none; font-size:.875em; color:red;">
                                Provide a valid phone number (at least 7 digits).
                            </div>
                        </div>
                    </div>

                    <script>
                        function validatePhone(input) {
                            const phoneValue = input.value;
                            const phoneError = document.getElementById('invalid-feedback');
                            phoneError.style.display = 'none';
                        }
                        document.getElementById('register_form').addEventListener('submit', function(event) {
                            const phoneInput = document.getElementById('primary_contact_phone');
                            const phoneError = document.getElementById('invalid-feedback');
                            if (!/^\d{7,}$/.test(phoneInput.value)) {
                                event.preventDefault();  
                                phoneError.style.display = 'block'; 
                                phoneInput.classList.add('is-invalid'); 
                            } else {
                                phoneError.style.display = 'none'; 
                                phoneInput.classList.remove('is-invalid'); 
                            }
                        });
                    </script>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="primary_contact_position" class="form-label"><i class="fas fa-briefcase"></i> {{ __('Primary Contact Position') }} <span class="text-danger">*</span></label>
                            <input id="primary_contact_position" class="form-control" type="text" name="primary_contact_position" value="{{ old('primary_contact_position') }}" required placeholder="Enter position of contact person" />
                            <div class="invalid-feedback">
                                Provide the valid contact position
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> {{ __('Address') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="address[address]" value="{{ old('address.address') }}" required class="form-control" id="address"  placeholder="Enter Address"/>
                    
                            <div class="invalid-feedback">
                                Address is required
                            </div>
                        </div>
                    </div>
                    

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.postal_code" class="form-label"><i class="fas fa-map-pin"></i> {{ __('Postal Code') }} <span class="text-danger">*</span></label>
                            <input id="address.postal_code" class="form-control" type="numeric" name="address[postal_code]" value="{{ old('address.postal_code') }}" required pattern="^\d{5}(-\d{4})?$" placeholder="Postal code" />
                            <div class="invalid-feedback">
                                Enter a valid postal code (5 digit long)
                            </div>
                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.city" class="form-label"><i class="fas fa-city"></i> {{ __('City') }} <span class="text-danger">*</span></label>
                            <input id="address.city" class="form-control" type="text" name="address[city]" value="{{ old('address.city') }}" required placeholder="City name" />
                            <div class="invalid-feedback">
                                City is required
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
                                Select a province
                             </div>
                       
                         </div>
                    </div>
                </div>
    
                <!-- City and Province -->
                <div class="row">
                <!-- Password must be at least 8 characters long. -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> {{ __('Password') }} <span class="text-danger">*</span>
                        </label>
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="Enter your password" />
                        <div class="invalid-feedback">
                            Password is required
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i> {{ __('Confirm Password') }} <span class="text-danger">*</span>
                        </label>
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                        <div id="password-error" class="text-danger" style="display: none;">Passwords do not match, try again</div>
                    </div>
                </div>


                  
                </div>
          
                <div class="submit-btn">
                    <button type="submit" class="primary-btn">{{ __('Submit') }}</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .needs-validation .form-control:valid {
    background-image: none !important; /* Remove the tick icon */
    border-color: #ced4da; /* Restore default border color */
}

/* Show red border for invalid inputs */

/* Optional: Style invalid feedback */

    </style>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
    <script>
        document.getElementById('primary_contact_phone').addEventListener('input', function(event) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
    <script>
        document.getElementById('address.postal_code').addEventListener('input', function(event) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>

<script>
    function validateForm(event) {
        event.preventDefault();
        let form = document.getElementById('register_form');
        let isValid = true;
        const inputs = form.querySelectorAll('input, select');
        let password = form.querySelector('input[name="password"]');
        let confirmPassword = form.querySelector('input[name="password_confirmation"]');
        let phoneInput = form.querySelector('input[name="primary_contact_phone"]');
        let passwordError = document.getElementById('password-error');
        let phoneError = document.getElementById('phone-error');

        inputs.forEach(input => {
            let invalidFeedback = input.nextElementSibling;
            if (!input.checkValidity()) {
                isValid = false;
                input.classList.add('is-invalid');
                if (invalidFeedback) {
                    invalidFeedback.style.display = 'block';
                }
            } else {
                input.classList.remove('is-invalid');
                if (invalidFeedback) {
                    invalidFeedback.style.display = 'none';
                }
            }
        });

        if (password && confirmPassword && password.value !== confirmPassword.value) {
            isValid = false;
            confirmPassword.classList.add('is-invalid');
            if (passwordError) {
                passwordError.textContent = 'Passwords do not match, try again';
                passwordError.style.display = 'block';
            }
        } else {
            confirmPassword.classList.remove('is-invalid');
            if (passwordError) {
                passwordError.style.display = 'none';
            }
        }

        if (phoneInput && phoneInput.value.length < 7) {
            isValid = false;
            phoneInput.classList.add('is-invalid');
            if (phoneError) {
                phoneError.textContent = 'Phone number must be at least 7 digits.';
                phoneError.style.display = 'block';
            }
        } else if (phoneInput) {
            phoneInput.classList.remove('is-invalid');
            if (phoneError) {
                phoneError.style.display = 'none';
            }
        }

        if (isValid) {
            form.submit();
        }
    }

    document.getElementById('register_form').addEventListener('submit', validateForm);

    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            if (this.name === 'password' || this.name === 'password_confirmation') {
                let password = document.querySelector('input[name="password"]').value;
                let confirmPassword = document.querySelector('input[name="password_confirmation"]').value;
                let passwordError = document.getElementById('password-error');

                if (password !== confirmPassword) {
                    this.classList.add('is-invalid');
                    if (passwordError) {
                        passwordError.textContent = 'Enter confirmation Password';
                        passwordError.style.display = 'block';
                    }
                } else {
                    this.classList.remove('is-invalid');
                    if (passwordError) {
                        passwordError.style.display = 'none';
                    }
                }
            }

            if (this.name === 'primary_contact_phone') {
                let phoneError = document.getElementById('phone-error');
                if (this.value.length < 7) {
                    this.classList.add('is-invalid');
                    if (phoneError) {
                        phoneError.textContent = 'Phone number must be at least 7 digits.';
                        phoneError.style.display = 'block';
                    }
                } else {
                    this.classList.remove('is-invalid');
                    if (phoneError) {
                        phoneError.style.display = 'none';
                    }
                }
            }

            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                let invalidFeedback = this.nextElementSibling;
                if (invalidFeedback) {
                    invalidFeedback.style.display = 'none';
                }
            } else {
                this.classList.add('is-invalid');
                let invalidFeedback = this.nextElementSibling;
                if (invalidFeedback) {
                    invalidFeedback.style.display = 'block';
                }
            }
        });
    });
</script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.querySelector("#primary_contact_phone");
            const errorMessage = document.querySelector("#phone-error");

            const iti = intlTelInput(input, {
                initialCountry: "pk",
                separateDialCode: true,
                preferredCountries: ["pk"],
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js",
            });

            const form = input.closest("form");
            form.addEventListener("submit", function (e) {
                errorMessage.style.display = "none"; 
                input.classList.remove("is-invalid");
                const fullPhoneNumber = iti.getNumber();

                if (!fullPhoneNumber) {
                    e.preventDefault();
                    input.classList.add("is-invalid");
                    errorMessage.style.display = "block"; 
                    return; 
                }
                let hiddenInput = form.querySelector("input[name='full_phone_number']");
                if (!hiddenInput) {
                    hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "full_phone_number";
                    form.appendChild(hiddenInput);
                }
                hiddenInput.value = fullPhoneNumber;
            });
            input.addEventListener('input', function () {
                errorMessage.style.display = "none"; 
                input.classList.remove("is-invalid"); 
            });
        });
    </script>
    
</body>
</html>
