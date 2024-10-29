@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
{{-- 
    <p class="text-muted"><small><span class="text-danger">*</span> indicates required fields.</small></p> --}}

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" id="userForm">
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
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter First Name" value="{{ old('first_name', $user->first_name ?? '') }}" oninput="removeValidation(this)">
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
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter Last Name" value="{{ old('last_name', $user->last_name ?? '') }}" oninput="removeValidation(this)">
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
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email Address" value="{{ old('email', $user->email ?? '') }}" oninput="removeValidation(this)">
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone-alt"></i> Phone Number <span class="text-danger">*</span>
                    </label>
                    <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter Phone Number" value="{{ old('phone', $user->phone ?? '') }}" maxlength="20" oninput="removeValidation(this)">
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
                        <i class="fas fa-lock"></i> Password <strong class="text-danger">{{ !isset($user) ? '*' : '' }}</strong>&nbsp;
                    </label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password" {{ !isset($user) ? '' : '' }} oninput="removeValidation(this)">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password <strong class="text-danger">{{ !isset($user) ? '*' : '' }}</strong>&nbsp;
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password') is-invalid @enderror" placeholder="Confirm Password" {{ !isset($user) ? '' : '' }} oninput="removeValidation(this)">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

         <!-- Roles Field -->
<div class="col-md-6 mb-3">
    <label for="roles" class="form-label">
        <i class="fas fa-user-tag"></i> Roles 
    </label>
    <select name="roles[]" id="roles" class="form-select @error('roles') is-invalid @enderror" oninput="removeValidation(this)">
        <option value="">Select a role</option>
        @foreach($roles as $role)
            @if(!in_array($role->original_name, ['LP', 'Retailer']))
                <option value="{{ $role->original_name }}" {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                    {{ $role->original_name }}
                </option>
            @endif
        @endforeach
    </select>
    @error('roles')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


            <div class="mb-3">
                <!-- Address Field -->
                <label for="address" class="form-label">
                    <i class="fas fa-map-marker-alt"></i> Address
                </label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address" oninput="removeValidation(this)">{{ old('address', $user->address ?? '') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function removeValidation(element) {
        element.classList.remove('is-invalid');
        let errorFeedback = element.nextElementSibling;
        if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
            errorFeedback.style.display = 'none';
        }
    }
</script>
@endpush
