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
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="street_no"><i class="fas fa-home"></i> Street No</label>
                        <input type="text" class="form-control" id="street_no" name="street_no" value="{{ $retailer->address->street_no ?? '' }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="street_name"><i class="fas fa-road"></i> Street Name</label>
                        <input type="text" class="form-control" id="street_name" name="street_name" value="{{ $retailer->address->street_name ?? '' }}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="province"><i class="fas fa-map-marker-alt"></i> Province</label>
                        <input type="text" class="form-control" id="province" name="province" value="{{ $retailer->address->province ?? '' }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="city"><i class="fas fa-city"></i> City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ $retailer->address->city ?? '' }}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="location"><i class="fas fa-map-pin"></i> Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="{{ $retailer->address->location ?? '' }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="contact_person_name"><i class="fas fa-user"></i> Contact Person Name</label>
                        <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" value="{{ $retailer->address->contact_person_name ?? '' }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="contact_person_phone"><i class="fas fa-phone"></i> Contact Person Phone</label>
                        <input type="text" class="form-control" id="contact_person_phone" name="contact_person_phone" value="{{ $retailer->address->contact_person_phone ?? '' }}">
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Address</button>
            </div>
        </div>
    </form>
</div>
@endsection
