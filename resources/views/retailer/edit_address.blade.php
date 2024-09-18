@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Edit Location</h3>
        <a href="{{ route('retailer.show', $retailer->id) }}" class="btn btn-secondary">Back to Details</a>
    </div>

    <form action="{{ route('retailer.address.update', $retailer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Address Information</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="street_no">Street No</label>
                    <input type="text" class="form-control" id="street_no" name="street_no" value="{{ $retailer->address->street_no ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="street_name">Street Name</label>
                    <input type="text" class="form-control" id="street_name" name="street_name" value="{{ $retailer->address->street_name ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="province">Province</label>
                    <input type="text" class="form-control" id="province" name="province" value="{{ $retailer->address->province ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ $retailer->address->city ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ $retailer->address->location ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label for="contact_person_name">Contact Person Name</label>
                    <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" value="{{ $retailer->address->contact_person_name ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="contact_person_phone">Contact Person Phone</label>
                    <input type="text" class="form-control" id="contact_person_phone" name="contact_person_phone" value="{{ $retailer->address->contact_person_phone ?? '' }}">
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">Update Address</button>
            </div>
        </div>
    </form>
</div>
@endsection
