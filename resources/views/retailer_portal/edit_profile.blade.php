@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Edit Profile</h3>
        <a href="{{ route('retailer.manageInfo') }}" class="btn btn-secondary">Back to Manage Info</a>
    </div>

    <form action="{{ route('retailer.updateProfile') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Profile Fields -->
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $retailer->first_name) }}" required>
        </div>
        <!-- Other profile fields -->

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>
@endsection
