@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Required Fields Note -->
    <p class="text-muted"><small><strong>*</strong> indicates required fields.</small></p>

    <div class="bg-white p-4 rounded shadow-sm mb-4"> <!-- Add margin-bottom here -->
        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="row">
                <!-- First Name Field -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">
                        <i class="fas fa-user"></i> First Name <strong>*</strong>
                    </label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter First Name" value="{{ old('first_name', $user->first_name ?? '') }}" required pattern="[A-Za-z\s]+" title="First Name must contain only letters and spaces.">
                    @error('first_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- Last Name Field -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">
                        <i class="fas fa-user"></i> Last Name <strong>*</strong>
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter Last Name" value="{{ old('last_name', $user->last_name ?? '') }}" required pattern="[A-Za-z\s]+" title="Last Name must contain only letters and spaces.">
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
                        <i class="fas fa-envelope"></i> Email <strong>*</strong>
                    </label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email Address" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone"></i> Phone Number<strong>*</strong>
                    </label>
                    <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter Phone Number" value="{{ old('phone', $user->phone ?? '') }}" title="Phone number must contain only digits.">
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
                        <i class="fas fa-lock"></i> Password <strong>{{ !isset($user) ? '*' : '' }}</strong>
                    </label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password" {{ !isset($user) ? 'required' : '' }}>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- Confirm Password Field -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password <strong>{{ !isset($user) ? '*' : '' }}</strong>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" {{ !isset($user) ? 'required' : '' }}>
                </div>
            </div>

            <div class="row">
                <!-- Roles Field -->
                <div class="col-md-6 mb-3">
                    <label for="roles" class="form-label">
                        <i class="fas fa-user-tag"></i> Roles <strong>*</strong>
                    </label>
                    <select name="roles[]" id="roles" class="form-select @error('roles') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->original_name }}" {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->original_name }}
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
            

            <div class="mb-3">
                <!-- Address Field -->
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
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
        </form>
    </div> <!-- End of white background div -->
</div>
@endsection
