<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your LP Details</title>
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
        .form-group label {
            display: flex;
            align-items: center;
        }
        .form-group label i {
            margin-right: 8px;
        }
        .row-form {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4 text-center">Complete Your LP Details</h3>

        <form action="{{ route('lp.submitCompleteForm') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <!-- Hidden field to pass the LP ID -->
            <input type="hidden" name="lp_id" value="{{ $lp->id }}">

            <div class="row row-form">
                <div class="col-md-4 form-group">
                    <label for="name"><i class="fas fa-store"></i> LP Name  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ $lp->name }}" required>
                    <div class="invalid-feedback">Please provide a valid LP name.</div>
                </div>
                <div class="col-md-4 form-group">
                    <label for="dba"><i class="fas fa-tag"></i> DBA (Doing Business As)  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="dba" id="dba" value="{{ $lp->dba }}">
                </div>
                <div class="col-md-4 form-group">
                    <label for="primary_contact_email"><i class="fas fa-envelope"></i> Primary Contact Email  <span class="text-danger">*</label>
                    <input type="email" class="form-control" name="primary_contact_email" id="primary_contact_email" value="{{ $lp->primary_contact_email }}" required>
                    <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>
            </div>

            <div class="row row-form">
                <div class="col-md-4 form-group">
                    <label for="primary_contact_phone"><i class="fas fa-phone"></i> Primary Contact Phone  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="primary_contact_phone" id="primary_contact_phone" value="{{ $lp->primary_contact_phone }}">
                </div>
                <div class="col-md-4 form-group">
                    <label for="primary_contact_position"><i class="fas fa-briefcase"></i> Primary Contact Position  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="primary_contact_position" id="primary_contact_position" value="{{ $lp->primary_contact_position }}">
                </div>
                <div class="col-md-4 form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password  <span class="text-danger">*</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                    <div class="invalid-feedback">Please provide a password.</div>
                </div>
            </div>

            <div class="row row-form">
                <div class="col-md-4 form-group">
                    <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password  <span class="text-danger">*</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                    <div class="invalid-feedback">Please confirm your password.</div>
                </div>
                <div class="col-md-4 form-group">
                    <label for="street_number"><i class="fas fa-home"></i> Street Number  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="address[street_number]" id="street_number">
                </div>
                <div class="col-md-4 form-group">
                    <label for="street_name"><i class="fas fa-road"></i> Street Name  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="address[street_name]" id="street_name">
                </div>
            </div>

            <div class="row row-form">
                <div class="col-md-4 form-group">
                    <label for="postal_code"><i class="fas fa-postcode"></i> Postal Code  <span class="text-danger">*</label>
                    <input type="text" class="form-control" name="address[postal_code]" id="postal_code">
                </div>
                <div class="col-md-4 form-group">
                    <label for="city"><i class="fas fa-city"></i> City  <span class="text-danger">*</label>
                    <select class="form-control" name="address[city]" id="city" required>
                        <option value="">Select City</option>
                        <option value="City1">City1</option>
                        <option value="City2">City2</option>
                        <option value="City3">City3</option>
                        <option value="City4">City4</option>
                        <option value="City5">City5</option>
                    </select>
                    <div class="invalid-feedback">Please select a city.</div>
                </div>
                <!-- Add an empty col-md-4 div if you want to keep the row with three columns -->
                <div class="col-md-4 form-group"></div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
        </form>
    </div>

    <!-- JavaScript for form validation -->
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            });
        })();
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
