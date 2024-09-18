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
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $retailer->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $retailer->last_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="corporate_name">Corporate Name</label>
                    <input type="text" name="corporate_name" id="corporate_name" class="form-control" value="{{ old('corporate_name', $retailer->corporate_name) }}">
                </div>
                <div class="form-group">
                    <label for="dba">DBA</label>
                    <input type="text" name="dba" id="dba" class="form-control" value="{{ old('dba', $retailer->dba) }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $retailer->phone) }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $retailer->email) }}" required>
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
                                <div class="form-group">
                                    <label for="street_no_{{ $index }}">Street No</label>
                                    <input type="text" name="address[{{ $index }}][street_no]" id="street_no_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.street_no', $address->street_no) }}">
                                </div>
                                <div class="form-group">
                                    <label for="street_name_{{ $index }}">Street Name</label>
                                    <input type="text" name="address[{{ $index }}][street_name]" id="street_name_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.street_name', $address->street_name) }}">
                                </div>
                                <div class="form-group">
                                    <label for="province_{{ $index }}">Province</label>
                                    <input type="text" name="address[{{ $index }}][province]" id="province_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.province', $address->province) }}">
                                </div>
                                <div class="form-group">
                                    <label for="city_{{ $index }}">City</label>
                                    <input type="text" name="address[{{ $index }}][city]" id="city_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.city', $address->city) }}">
                                </div>
                                <div class="form-group">
                                    <label for="location_{{ $index }}">Location</label>
                                    <input type="text" name="address[{{ $index }}][location]" id="location_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.location', $address->location) }}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_person_name_{{ $index }}">Contact Person Name</label>
                                    <input type="text" name="address[{{ $index }}][contact_person_name]" id="contact_person_name_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.contact_person_name', $address->contact_person_name) }}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_person_phone_{{ $index }}">Contact Person Phone</label>
                                    <input type="text" name="address[{{ $index }}][contact_person_phone]" id="contact_person_phone_{{ $index }}" class="form-control" value="{{ old('address.' . $index . '.contact_person_phone', $address->contact_person_phone) }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Update Retailer</button>
        </div>
    </form>
</div>
@endsection
