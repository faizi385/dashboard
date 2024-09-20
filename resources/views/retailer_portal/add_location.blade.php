@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Add Location</h3>
    
    <!-- Form to add new location -->
    <form action="{{ route('retailer.storeLocation') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Location Information</h5>
            </div>
            <div class="card-body">
                <!-- Add fields for location details -->
                <div class="form-group">
                    <label for="street_no">Street No</label>
                    <input type="text" class="form-control" id="street_no" name="street_no" required>
                </div>
                <div class="form-group">
                    <label for="street_name">Street Name</label>
                    <input type="text" class="form-control" id="street_name" name="street_name" required>
                </div>
                <div class="form-group">
                    <label for="province">Province</label>
                    <input type="text" class="form-control" id="province" name="province" required>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="contact_person_name">Contact Person Name</label>
                    <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" required>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">Save Location</button>
            </div>
        </div>
    </form>
</div>
@endsection
