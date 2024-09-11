@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-map-marker-alt"></i> 
            {{ isset($province) ? 'Edit Province' : 'Create Province' }}
        </h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ isset($province) ? route('provinces.update', $province) : route('provinces.store') }}" method="POST">
        @csrf
        @if(isset($province))
            @method('PUT')
        @endif

        <div class="row">
            <!-- Name and Slug Fields (Two fields in one row) -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">
                    <i class="fas fa-signature"></i> Name
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Province Name" value="{{ old('name', $province->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="slug" class="form-label">
                    <i class="fas fa-tag"></i> Slug
                </label>
                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="Enter Province Slug" value="{{ old('slug', $province->slug ?? '') }}" required>
                @error('slug')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Timezone 1 and Timezone 2 Fields (Two fields in one row) -->
            <div class="col-md-6 mb-3">
                <label for="timezone_1" class="form-label">
                    <i class="fas fa-clock"></i> Timezone 1
                </label>
                <input type="text" name="timezone_1" id="timezone_1" class="form-control @error('timezone_1') is-invalid @enderror" placeholder="Enter Timezone 1" value="{{ old('timezone_1', $province->timezone_1 ?? '') }}" required>
                @error('timezone_1')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="timezone_2" class="form-label">
                    <i class="fas fa-clock"></i> Timezone 2
                </label>
                <input type="text" name="timezone_2" id="timezone_2" class="form-control @error('timezone_2') is-invalid @enderror" placeholder="Enter Timezone 2" value="{{ old('timezone_2', $province->timezone_2 ?? '') }}" required>
                @error('timezone_2')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Tax Value and Status Fields (Two fields in one row) -->
            <div class="col-md-6 mb-3">
                <label for="tax_value" class="form-label">
                    <i class="fas fa-percent"></i> Tax Value
                </label>
                <input type="number" step="0.01" name="tax_value" id="tax_value" class="form-control @error('tax_value') is-invalid @enderror" placeholder="Enter Tax Value" value="{{ old('tax_value', $province->tax_value ?? '') }}" required>
                @error('tax_value')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">
                    <i class="fas fa-toggle-on"></i> Status
                </label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="1" {{ old('status', $province->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $province->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i> {{ isset($province) ? 'Update Province' : 'Create Province' }}
        </button>
    </form>
</div>
@endsection
