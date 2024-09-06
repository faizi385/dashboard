@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ isset($province) ? 'Edit Province' : 'Create Province' }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    </div>

    <form action="{{ isset($province) ? route('provinces.update', $province) : route('provinces.store') }}" method="POST">
        @csrf
        @if(isset($province))
            @method('PUT')
        @endif

        <div class="row">
            <!-- Name and Slug Fields (Two fields in one row) -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $province->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $province->slug ?? '') }}" required>
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
                <label for="timezone_1" class="form-label">Timezone 1</label>
                <input type="text" name="timezone_1" id="timezone_1" class="form-control @error('timezone_1') is-invalid @enderror" value="{{ old('timezone_1', $province->timezone_1 ?? '') }}" required>
                @error('timezone_1')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="timezone_2" class="form-label">Timezone 2</label>
                <input type="text" name="timezone_2" id="timezone_2" class="form-control @error('timezone_2') is-invalid @enderror" value="{{ old('timezone_2', $province->timezone_2 ?? '') }}" required>
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
                <label for="tax_value" class="form-label">Tax Value</label>
                <input type="number" step="0.01" name="tax_value" id="tax_value" class="form-control @error('tax_value') is-invalid @enderror" value="{{ old('tax_value', $province->tax_value ?? '') }}" required>
                @error('tax_value')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
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

        <button type="submit" class="btn btn-primary">{{ isset($province) ? 'Update Province' : 'Create Province' }}</button>
    </form>
</div>
@endsection
