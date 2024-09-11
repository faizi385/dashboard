@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-key"></i> Create Permission
        </h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-lock"></i> Permission Name
            </label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter permission name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Permission
        </button>
    </form>
</div>
@endsection
