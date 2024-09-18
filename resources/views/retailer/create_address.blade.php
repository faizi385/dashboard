@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Add Location</h3>
        <a href="{{ route('retailer.show', $retailer->id) }}" class="btn btn-secondary">Back to Details</a>
    </div>

    <!-- Updated form for creating a new address -->
    <form action="{{ route('retailer.address.store', $retailer->id) }}" method="POST">
        @csrf
        <div id="address-forms">
            <div class="card address-form">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title">Address Information</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-address" style="display:none;">Remove</button>
                </div>
                <div class="card-body">
                    <!-- Form fields -->
                    <div class="form-group">
                        <label for="street_no">Street No</label>
                        <input type="text" class="form-control" name="addresses[0][street_no]" required>
                    </div>
                    <div class="form-group">
                        <label for="street_name">Street Name</label>
                        <input type="text" class="form-control" name="addresses[0][street_name]" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Province</label>
                        <select class="form-control" name="addresses[0][province]" required>
                            <option value="" disabled selected>Select a province</option>
                            <option value="Alberta">Alberta</option>
                            <option value="British Columbia">British Columbia</option>
                            <option value="Ontario">Ontario</option>
                            <option value="Manitoba">Manitoba</option>
                            <option value="Saskatchewan">Saskatchewan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city-dropdown">City</label>
                        <select class="form-control city-dropdown" name="addresses[0][city]" required>
                            <option value="" disabled selected>Select a city</option>
                            <option value="Calgary">Calgary</option>
                            <option value="Vancouver">Vancouver</option>
                            <option value="Toronto">Toronto</option>
                            <option value="Winnipeg">Winnipeg</option>
                            <option value="Regina">Regina</option>
                            <option value="other">Other (Specify)</option>
                        </select>
                    </div>
                    <div class="form-group custom-city-input" style="display:none;">
                        <label for="custom_city">Enter City Name</label>
                        <input type="text" class="form-control" name="addresses[0][custom_city]" placeholder="Enter your city name">
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" name="addresses[0][location]" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_person_name">Contact Person Name</label>
                        <input type="text" class="form-control" name="addresses[0][contact_person_name]">
                    </div>
                    <div class="form-group">
                        <label for="contact_person_phone">Contact Person Phone</label>
                        <input type="text" class="form-control" name="addresses[0][contact_person_phone]">
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="add-address" class="btn btn-primary mt-3">Add Another Address</button>
        <button type="submit" class="btn btn-success mt-3">Save Locations</button>
    </form>

    <script>
        let addressCount = 1;

        // Add new address form functionality
        document.getElementById('add-address').addEventListener('click', function() {
            let newAddressForm = document.querySelector('.address-form').cloneNode(true);
            newAddressForm.querySelectorAll('input, select').forEach(function(input) {
                input.name = input.name.replace(/\d+/, addressCount);
                input.value = '';
                if (input.classList.contains('custom-city-input')) {
                    input.style.display = 'none'; // Hide custom city input in cloned form
                }
            });

            // Display the remove button on new forms
            newAddressForm.querySelector('.remove-address').style.display = 'inline-block';
            
            document.getElementById('address-forms').appendChild(newAddressForm);
            addressCount++;

            // Apply city toggle functionality for new form
            handleCityDropdown(newAddressForm);
        });

        // Handle city dropdown toggle
        function handleCityDropdown(form) {
            form.querySelector('.city-dropdown').addEventListener('change', function() {
                if (this.value === 'other') {
                    form.querySelector('.custom-city-input').style.display = 'block';
                } else {
                    form.querySelector('.custom-city-input').style.display = 'none';
                }
            });
        }

        // Apply city dropdown toggle for the initial form
        document.querySelectorAll('.address-form').forEach(handleCityDropdown);

        // Remove address form functionality
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-address')) {
                if (document.querySelectorAll('.address-form').length > 1) {
                    event.target.closest('.address-form').remove();
                } else {
                    alert('At least one address is required.');
                }
            }
        });
    </script>
</div>
@endsection
