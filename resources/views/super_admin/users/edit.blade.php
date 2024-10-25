@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="row">
                <!-- First Name Field -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">
                        <i class="fas fa-user"></i> First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter First Name" value="{{ old('first_name', $user->first_name ?? '') }}" onfocus="removeValidation(this)">
                    @error('first_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Last Name Field -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">
                        <i class="fas fa-user"></i> Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter Last Name" value="{{ old('last_name', $user->last_name ?? '') }}" onfocus="removeValidation(this)">
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
                        <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email Address" value="{{ old('email', $user->email ?? '') }}" onfocus="removeValidation(this)">
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Phone Number Field -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone"></i> Phone Number <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter Phone Number" value="{{ old('phone', $user->phone ?? '') }}" onfocus="removeValidation(this)">
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
                        <i class="fas fa-lock"></i> Password <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password" {{ !isset($user) ? '' : '' }} onfocus="removeValidation(this)">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" {{ !isset($user) ? '' : '' }} onfocus="removeValidation(this)">
                </div>
            </div>

            <!-- Address Field -->
            <div class="mb-3">
                <label for="address" class="form-label">
                    <i class="fas fa-address-card"></i> Address
                </label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address" onfocus="removeValidation(this)">{{ old('address', $user->address ?? '') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Roles Field -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="roles" class="form-label">
                        <i class="fas fa-user-tag"></i> Roles <span class="text-danger">*</span>
                    </label>
                    <select name="roles[]" id="roles" class="form-select @error('roles') is-invalid @enderror" onfocus="removeValidation(this)">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ isset($user) && $user->roles->pluck('id')->contains($role->id) ? 'selected' : '' }}>
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

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($user) ? 'Update User' : 'Create' }}
            </button>
        </form>
    </div>
</div>

<script>
    function removeValidation(element) {
        element.classList.remove('is-invalid');
        let errorFeedback = element.nextElementSibling;
        if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
            errorFeedback.style.display = 'none';
        }
    }
</script>
@endsection
