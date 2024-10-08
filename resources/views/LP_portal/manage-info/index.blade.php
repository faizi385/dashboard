@extends('layouts.app')

@section('content')
<div class="container p-4">
    <h1 class="mb-4">Manage Info</h1>

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

    <div class="bg-white p-4 rounded shadow-sm">
        <form action="{{ route('manage-info.update') }}" method="POST">
            @csrf

            <!-- LP information -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $lp->name) }}" required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lp->primary_contact_email) }}" required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $lp->primary_contact_phone) }}">
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <h2 class="mt-4">Address</h2>

            @forelse($lp->address as $address)
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="street_number_{{ $address->id }}">Street Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                </div>
                                <input type="text" name="address[{{ $address->id }}][street_number]" class="form-control @error('address.'.$address->id.'.street_number') is-invalid @enderror" value="{{ old('address.'.$address->id.'.street_number', $address->street_number) }}">
                            </div>
                            @error('address.'.$address->id.'.street_number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="street_name_{{ $address->id }}">Street Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-road"></i></span>
                                </div>
                                <input type="text" name="address[{{ $address->id }}][street_name]" class="form-control @error('address.'.$address->id.'.street_name') is-invalid @enderror" value="{{ old('address.'.$address->id.'.street_name', $address->street_name) }}">
                            </div>
                            @error('address.'.$address->id.'.street_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="postal_code_{{ $address->id }}">Postal Code</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                </div>
                                <input type="text" name="address[{{ $address->id }}][postal_code]" class="form-control @error('address.'.$address->id.'.postal_code') is-invalid @enderror" value="{{ old('address.'.$address->id.'.postal_code', $address->postal_code) }}">
                            </div>
                            @error('address.'.$address->id.'.postal_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city_{{ $address->id }}">City</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <input type="text" name="address[{{ $address->id }}][city]" class="form-control @error('address.'.$address->id.'.city') is-invalid @enderror" value="{{ old('address.'.$address->id.'.city', $address->city) }}">
                            </div>
                            @error('address.'.$address->id.'.city')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            @empty
                <p>No addresses available.</p>
            @endforelse

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
