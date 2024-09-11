@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-map-marker-alt"></i> Edit Province
        </h1>
        <a href="{{ route('provinces.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('provinces.update', $province) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Name Field -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">
                    <i class="fas fa-signature"></i> Name
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $province->name) }}" placeholder="Enter Province Name" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Slug Field -->
            <div class="col-md-6 mb-3">
                <label for="slug" class="form-label">
                    <i class="fas fa-tag"></i> Slug
                </label>
                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $province->slug) }}" placeholder="Enter Province Slug" required>
                @error('slug')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Timezone 1 Field -->
            <div class="col-md-6 mb-3">
                <label for="timezone_1" class="form-label">
                    <i class="fas fa-clock"></i> Timezone 1
                </label>
                <input type="text" name="timezone_1" id="timezone_1" class="form-control @error('timezone_1') is-invalid @enderror" value="{{ old('timezone_1', $province->timezone_1) }}" placeholder="Enter Timezone 1" required>
                @error('timezone_1')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Timezone 2 Field -->
            <div class="col-md-6 mb-3">
                <label for="timezone_2" class="form-label">
                    <i class="fas fa-clock"></i> Timezone 2
                </label>
                <input type="text" name="timezone_2" id="timezone_2" class="form-control @error('timezone_2') is-invalid @enderror" value="{{ old('timezone_2', $province->timezone_2) }}" placeholder="Enter Timezone 2" required>
                @error('timezone_2')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Tax Value Field -->
            <div class="col-md-6 mb-3">
                <label for="tax_value" class="form-label">
                    <i class="fas fa-percent"></i> Tax Value
                </label>
                <input type="number" step="0.01" name="tax_value" id="tax_value" class="form-control @error('tax_value') is-invalid @enderror" value="{{ old('tax_value', $province->tax_value) }}" placeholder="Enter Tax Value" required>
                @error('tax_value')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Status Field -->
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">
                    <i class="fas fa-toggle-on"></i> Status
                </label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="1" {{ old('status', $province->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $province->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i> Update Province
        </button>
    </form>
</div>
@endsection
