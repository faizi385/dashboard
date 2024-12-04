@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-user-tag"></i> Edit Role
        </h1>
        <a href="{{ route('roles.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form id="roleForm" action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3 col-md-6">
                <label for="name" class="form-label">
                    <i class="fas fa-briefcase"></i> Role Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name', $role->original_name) }}" >
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Permissions
                </label>
                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                    id="permission_{{ $permission->id }}" class="form-check-input" 
                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                    {{ \Illuminate\Support\Str::title($permission->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('permissions')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Role
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to remove validation errors when user interacts with an input
        function removeValidationErrors(input) {
            input.addEventListener('input', function () {
                if (input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
                    const errorDiv = input.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv.style.display = 'none'; // Hide the error message
                    }
                }
            });

            if (input.type === 'checkbox') {
                input.addEventListener('change', function () {
                    if (input.classList.contains('is-invalid')) {
                        input.classList.remove('is-invalid');
                        const errorDiv = input.closest('.form-check').querySelector('.invalid-feedback');
                        if (errorDiv) {
                            errorDiv.style.display = 'none'; // Hide the error message
                        }
                    }
                });
            }
        }

        // Add event listeners to all form inputs for validation error removal
        const formInputs = document.querySelectorAll('#roleForm input');
        formInputs.forEach(function (input) {
            removeValidationErrors(input);
        });
    });
</script>
@endpush
