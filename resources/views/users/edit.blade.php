@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    </div>

    <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif
        
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" {{ !isset($user) ? 'required' : '' }}>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" {{ !isset($user) ? 'required' : '' }}>
        </div>
        <div class="form-group mt-3">
            <label for="roles">Roles</label>
            <select name="roles[]" id="roles" class="form-control" multiple>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ isset($user) && $user->roles->pluck('id')->contains($role->id) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">{{ isset($user) ? 'Update' : 'Create' }}</button>
    </form>
</div>
@endsection
