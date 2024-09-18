@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Manage Info</h3>
        <div>
            <a href="{{ route('retailer.addLocation') }}" class="btn btn-primary ml-2">Add Location</a>
        </div>
    </div>

    <!-- Retailer Profile Form -->
    <form id="profileForm" action="{{ route('retailer.updateProfile') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Retailer Information</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $retailer->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $retailer->last_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $retailer->phone) }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $retailer->email) }}" required>
                </div>
                <div class="form-group">
                    <label for="corporate_name">Corporate Name</label>
                    <input type="text" class="form-control" id="corporate_name" name="corporate_name" value="{{ old('corporate_name', $retailer->corporate_name) }}">
                </div>
                <div class="form-group">
                    <label for="dba">DBA</label>
                    <input type="text" class="form-control" id="dba" name="dba" value="{{ old('dba', $retailer->dba) }}">
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">Update Profile</button>
            </div>
        </div>
    </form>

    <!-- Retailer Addresses -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Retailer Addresses</h5>
        </div>
        <div class="card-body">
            @if ($addresses->isNotEmpty())
                @foreach ($addresses as $address)
                    <div class="border rounded p-3 mb-3">
                        <h6>Address {{ $loop->iteration }}</h6>
                        <p><strong>Street No:</strong> {{ $address->street_no }}</p>
                        <p><strong>Street Name:</strong> {{ $address->street_name }}</p>
                        <p><strong>Province:</strong> {{ $address->province }}</p>
                        <p><strong>City:</strong> {{ $address->city }}</p>
                        <p><strong>Location:</strong> {{ $address->location }}</p>
                        <p><strong>Contact Person:</strong> {{ $address->contact_person_name }}</p>
                        <p><strong>Contact Phone:</strong> {{ $address->contact_person_phone }}</p>
                        <a href="{{ route('retailer.editAddress', $address->id) }}" class="btn btn-sm btn-warning">Edit Address</a>
                    </div>
                @endforeach
            @else
                <p>No addresses available yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
