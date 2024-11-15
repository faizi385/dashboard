@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-key"></i> Edit Permission
        </h1>
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3 col-md-6">
                <label for="name" class="form-label">
                    <i class="fas fa-lock"></i> Permission Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name) }}" oninput="removeValidation(this)">
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="description" class="form-label">
                    <i class="fas fa-info-circle"></i> Description <span class="text-danger">*</span>
                </label>
                <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $permission->description) }}"  oninput="removeValidation(this)">
                @error('description')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Permission
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // JavaScript function to remove validation error messages dynamically
    function removeValidation(element) {
        if (element.classList.contains('is-invalid')) {
            element.classList.remove('is-invalid');
            let errorFeedback = element.nextElementSibling;
            if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
                errorFeedback.style.display = 'none';
            }
        }
    }

    // Apply `removeValidation` function on `input` events for form fields with `is-invalid` class
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.is-invalid').forEach(function (input) {
            input.addEventListener('input', function () {
                removeValidation(input);
            });
        });
    });
</script>
@endpush
