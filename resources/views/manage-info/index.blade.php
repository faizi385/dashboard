@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Info</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('manage-info.update') }}" method="POST">
        @csrf

        <!-- LP information -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $lp->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $lp->primary_contact_email) }}" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $lp->primary_contact_phone) }}">
        </div>

        <!-- Address information -->
        <h2>Address</h2>

        @forelse($lp->address as $address)
            <div class="form-group">
                <label for="street_number_{{ $address->id }}">Street Number</label>
                <input type="text" name="address[{{ $address->id }}][street_number]" class="form-control" value="{{ old('address.'.$address->id.'.street_number', $address->street_number) }}">
            </div>

            <div class="form-group">
                <label for="street_name_{{ $address->id }}">Street Name</label>
                <input type="text" name="address[{{ $address->id }}][street_name]" class="form-control" value="{{ old('address.'.$address->id.'.street_name', $address->street_name) }}">
            </div>

            <div class="form-group">
                <label for="postal_code_{{ $address->id }}">Postal Code</label>
                <input type="text" name="address[{{ $address->id }}][postal_code]" class="form-control" value="{{ old('address.'.$address->id.'.postal_code', $address->postal_code) }}">
            </div>

            <div class="form-group">
                <label for="city_{{ $address->id }}">City</label>
                <input type="text" name="address[{{ $address->id }}][city]" class="form-control" value="{{ old('address.'.$address->id.'.city', $address->city) }}">
            </div>
        @empty
            <p>No addresses available.</p>
        @endforelse

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
