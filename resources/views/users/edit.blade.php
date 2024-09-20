@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
    </div>

    <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="row">
            <!-- First Name Field -->
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">
                    <i class="fas fa-user"></i> First Name
                </label>
                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter First Name" value="{{ old('first_name', $user->first_name ?? '') }}" required>
                @error('first_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Last Name Field -->
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">
                    <i class="fas fa-user"></i> Last Name
                </label>
                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter Last Name" value="{{ old('last_name', $user->last_name ?? '') }}" required>
                @error('last_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Email Field -->
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email Address" value="{{ old('email', $user->email ?? '') }}" required>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Phone Number Field -->
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">
                    <i class="fas fa-phone"></i> Phone Number
                </label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter Phone Number" value="{{ old('phone', $user->phone ?? '') }}">
                @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Password Field -->
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password" {{ !isset($user) ? 'required' : '' }}>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password Confirmation Field -->
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock"></i> Confirm Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" {{ !isset($user) ? 'required' : '' }}>
            </div>
        </div>

        <!-- Address Field -->
        <div class="mb-3">
            <label for="address" class="form-label">
                <i class="fas fa-address-card"></i> Address
            </label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address">{{ old('address', $user->address ?? '') }}</textarea>
            @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Roles Field -->
        <div class="row">
            <!-- Roles Field -->
            <div class="col-md-6 mb-3">
                <label for="roles" class="form-label">
                    <i class="fas fa-user-tag"></i> Roles
                </label>
                <select name="roles[]" id="roles" class="form-select @error('roles') is-invalid @enderror" multiple required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ isset($user) && $user->roles->pluck('id')->contains($role->id) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('roles')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ isset($user) ? 'Update' : 'Create' }}
        </button>
    </form>
</div>
@endsection
