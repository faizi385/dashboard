@extends('layouts.app')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Profile Settings</h1>
    </div>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Display success message -->
    @if (session('toast_success'))
        <div class="alert alert-success">
            {{ session('toast_success') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <!-- Settings form -->
        <form action="{{ route('settings.update') }}" method="POST" id="settingsForm">
            @csrf

            <div class="row">
                <!-- Email Field -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-envelope"></i> Name: <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->first_name . ' ' . $user->last_name) }}" oninput="removeValidation(this)" required>                    
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" oninput="removeValidation(this)" required >
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Phone Number Field -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone-alt"></i> Phone Number
                    </label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" oninput="removeValidation(this)">
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
                        <i class="fas fa-lock"></i> New Password (Leave blank if you don't want to change)
                    </label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" oninput="removeValidation(this)">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i> Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" oninput="removeValidation(this)">
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
