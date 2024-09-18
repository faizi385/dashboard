@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Settings</h1>
    
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

    <!-- Settings form -->
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <!-- Email field -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
        </div>

        <!-- Phone number field -->
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $user->phone }}">
        </div>

        <!-- Password update fields -->
        <div class="form-group">
            <label for="password">New Password (Leave blank if you don't want to change)</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
