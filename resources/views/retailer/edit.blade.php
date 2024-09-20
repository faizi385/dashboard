@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Edit Retailer</h3>
        <a href="{{ route('retailer.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <form action="{{ route('retailer.update', $retailer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Retailer Information</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="first_name"><i class="fas fa-user"></i> First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $retailer->first_name) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="last_name"><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $retailer->last_name) }}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="corporate_name"><i class="fas fa-building"></i> Corporate Name</label>
                        <input type="text" name="corporate_name" id="corporate_name" class="form-control" value="{{ old('corporate_name', $retailer->corporate_name) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="dba"><i class="fas fa-store"></i> DBA</label>
                        <input type="text" name="dba" id="dba" class="form-control" value="{{ old('dba', $retailer->dba) }}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $retailer->phone) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $retailer->email) }}" required>
                    </div>
                </div>
            </div>
        </div>

        @if($retailer->address->isNotEmpty())
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Address Information</h5>
                </div>
                <div class="card-body">
                    @foreach ($retailer->address as $index => $address)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title">Address {{ $index + 1 }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="street_no_{{ $index }}"><i class="fas fa-home"></i> Street No</label>
                                        <input type="text" name="address[{{ $index }}][street_no]" id="street_no_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.street_no', $address->street_no) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="street_name_{{ $index }}"><i class="fas fa-road"></i> Street Name</label>
                                        <input type="text" name="address[{{ $index }}][street_name]" id="street_name_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.street_name', $address->street_name) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="province_{{ $index }}"><i class="fas fa-map-marker-alt"></i> Province</label>
                                        <input type="text" name="address[{{ $index }}][province]" id="province_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.province', $address->province) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="city_{{ $index }}"><i class="fas fa-city"></i> City</label>
                                        <input type="text" name="address[{{ $index }}][city]" id="city_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.city', $address->city) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="location_{{ $index }}"><i class="fas fa-map-pin"></i> Location</label>
                                        <input type="text" name="address[{{ $index }}][location]" id="location_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.location', $address->location) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="contact_person_name_{{ $index }}"><i class="fas fa-user"></i> Contact Person Name</label>
                                        <input type="text" name="address[{{ $index }}][contact_person_name]" id="contact_person_name_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.contact_person_name', $address->contact_person_name) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="contact_person_phone_{{ $index }}"><i class="fas fa-phone"></i> Contact Person Phone</label>
                                        <input type="text" name="address[{{ $index }}][contact_person_phone]" id="contact_person_phone_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.contact_person_phone', $address->contact_person_phone) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Retailer</button>
        </div>
    </form>
</div>
@endsection
