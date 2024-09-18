<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Details</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        .btn-primary {
            border-radius: 0.25rem;
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
            <div class="row">
                <!-- First Name -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $retailer->first_name }}" required>
                    <div class="invalid-feedback">Please provide a valid first name.</div>
                </div>
    
                <!-- Last Name -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $retailer->last_name }}" required>
                    <div class="invalid-feedback">Please provide a valid last name.</div>
                </div>
            </div>
    
            <div class="row">
                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ $retailer->email }}" required>
                    <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>
    
                <!-- Phone Number -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ $retailer->phone }}" required>
                    <div class="invalid-feedback">Please provide a valid phone number.</div>
                </div>
            </div>
    
            <!-- Company Information -->
            <h4 class="mb-4">Company Information</h4>
    
            <div class="row">
                <!-- Corporate Name -->
                <div class="col-md-6 mb-3">
                    <label for="corporate_name" class="form-label">Corporate Name</label>
                    <input type="text" class="form-control" name="corporate_name" id="corporate_name" value="{{ $retailer->corporate_name }}">
                </div>
    
                <!-- DBA -->
                <div class="col-md-6 mb-3">
                    <label for="dba" class="form-label">DBA (Doing Business As)</label>
                    <input type="text" class="form-control" name="dba" id="dba" value="{{ $retailer->dba }}">
                </div>
            </div>
    
            <div class="row">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
    
                <!-- Confirm Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                </div>
            </div>
    
            <!-- Address Information -->
            <h4 class="mb-4">Address Information</h4>
    
            <div class="row">
                <!-- Street No -->
                <div class="col-md-6 mb-3">
                    <label for="street_no" class="form-label">Street No</label>
                    <input type="text" class="form-control" name="street_no" id="street_no" value="{{ $retailer->street_no }}">
                </div>
    
                <!-- Street Name -->
                <div class="col-md-6 mb-3">
                    <label for="street_name" class="form-label">Street Name</label>
                    <input type="text" class="form-control" name="street_name" id="street_name" value="{{ $retailer->street_name }}">
                </div>
            </div>
    
            <div class="row">
                <!-- Province -->
                <div class="col-md-6 mb-3">
                    <label for="province" class="form-label">Province</label>
                    <input type="text" class="form-control" name="province" id="province" value="{{ $retailer->province }}">
                </div>
    
                <!-- City -->
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">City</label>
                    <select name="city" id="city" class="form-control" required>
                        <option value="" disabled selected>Select a city</option>
                        <option value="New York" {{ $retailer->city == 'New York' ? 'selected' : '' }}>New York</option>
                        <option value="Los Angeles" {{ $retailer->city == 'Los Angeles' ? 'selected' : '' }}>Los Angeles</option>
                        <option value="Chicago" {{ $retailer->city == 'Chicago' ? 'selected' : '' }}>Chicago</option>
                        <option value="Houston" {{ $retailer->city == 'Houston' ? 'selected' : '' }}>Houston</option>
                        <option value="Phoenix" {{ $retailer->city == 'Phoenix' ? 'selected' : '' }}>Phoenix</option>
                        <!-- Add more cities as needed -->
                    </select>
                    <div class="invalid-feedback">Please select a city.</div>
                </div>
            </div>
    
            <div class="row">
                <!-- Location -->
                <div class="col-md-6 mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" id="location" value="{{ $retailer->location }}">
                </div>
            </div>
    
            <!-- Contact Information -->
            <h4 class="mb-4">Contact Information</h4>
    
            <div class="row">
                <!-- Contact Person Name -->
                <div class="col-md-6 mb-3">
                    <label for="contact_person_name" class="form-label">Contact Person Name</label>
                    <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" value="{{ $retailer->contact_person_name }}">
                </div>
    
                <!-- Contact Person Phone -->
                <div class="col-md-6 mb-3">
                    <label for="contact_person_phone" class="form-label">Contact Person Phone</label>
                    <input type="text" class="form-control" name="contact_person_phone" id="contact_person_phone" value="{{ $retailer->contact_person_phone }}">
                </div>
            </div>
    
            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
