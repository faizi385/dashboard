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
        .form-container {
            max-width: 800px;
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
            font-weight: normal; /* Removed bold text */
        }
        .form-error {
            color: red;
            font-size: 12px;
            margin-top: 4px;
        }
        .submit-btn {
            text-align: center;
        }
        .primary-btn {
            background-color: #007bff;
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
                            <label for="name" class="form-label"><i class="fas fa-user"></i> {{ __('Full Name') }}</label>
                            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your full name" />
                            @error('name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dba" class="form-label"><i class="fas fa-building"></i> {{ __('DBA') }}</label>
                            <input id="dba" class="form-control" type="text" name="dba" value="{{ old('dba') }}" required placeholder="Enter DBA name" />
                            @error('dba')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email" class="form-label"><i class="fas fa-envelope"></i> {{ __('Email') }}</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Enter your email address" />
                            @error('email')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Primary Contact Phone and Position -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="primary_contact_phone" class="form-label"><i class="fas fa-phone-alt"></i> {{ __('Primary Contact Phone') }}</label>
                            <input id="primary_contact_phone" class="form-control" type="text" name="primary_contact_phone" value="{{ old('primary_contact_phone') }}" required placeholder="Enter contact phone number" />
                            @error('primary_contact_phone')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="primary_contact_position" class="form-label"><i class="fas fa-briefcase"></i> {{ __('Primary Contact Position') }}</label>
                            <input id="primary_contact_position" class="form-control" type="text" name="primary_contact_position" value="{{ old('primary_contact_position') }}" required placeholder="Enter position of contact person" />
                            @error('primary_contact_position')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password and Confirm Password -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label"><i class="fas fa-lock"></i> {{ __('Password') }}</label>
                            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="Enter your password" />
                            @error('password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label"><i class="fas fa-lock"></i> {{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                            @error('password_confirmation')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
    
                <!-- Address (Street Number, Street Name, Postal Code) -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.street_number" class="form-label"><i class="fas fa-home"></i> {{ __('Street Number') }}</label>
                            <input id="address.street_number" class="form-control" type="text" name="address[street_number]" value="{{ old('address.street_number') }}" placeholder="Street number" />
                            @error('address.street_number')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.street_name" class="form-label"><i class="fas fa-road"></i> {{ __('Street Name') }}</label>
                            <input id="address.street_name" class="form-control" type="text" name="address[street_name]" value="{{ old('address.street_name') }}" placeholder="Street name" />
                            @error('address.street_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address.postal_code" class="form-label"><i class="fas fa-map-pin"></i> {{ __('Postal Code') }}</label>
                            <input id="address.postal_code" class="form-control" type="text" name="address[postal_code]" value="{{ old('address.postal_code') }}" placeholder="Postal code" />
                            @error('address.postal_code')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
    
                <!-- City and Province -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address.city" class="form-label"><i class="fas fa-city"></i> {{ __('City') }}</label>
                            <input id="address.city" class="form-control" type="text" name="address[city]" value="{{ old('address.city') }}" required placeholder="City name" />
                            @error('address.city')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address.province" class="form-label"><i class="fas fa-map"></i> {{ __('Province') }}</label>
                            <select id="address.province" class="form-control" name="address[province]" required>
                                <option value="">{{ __('Select Province') }}</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" @if(old('address.province') == $province->id) selected @endif>{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('address.province')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
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
            'use strict'

            // Enable validation on forms
            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>

</body>
</html>
