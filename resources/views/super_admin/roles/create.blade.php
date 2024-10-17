@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-user-tag"></i> Create Role
        </h1>
        <a href="{{ route('roles.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
            @csrf

            <!-- Role Name Input -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-briefcase"></i> Role Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter role name" value="{{ old('name') }}" >
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Permissions Selection -->
            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Assign Permissions 
                </label>
                <div class="row">
                    @foreach($permissions as $permission)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" class="form-check-input" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                {{ \Illuminate\Support\Str::title($permission->name) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                    <div class="invalid-feedback d-block">
                        {{ $message }} <!-- Show error message for permissions -->
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Role
            </button>
        </form>
    </div> <!-- End of white background div -->
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to remove 'is-invalid' class and error message when user starts typing
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
        }

        // Add event listeners to all form inputs to remove validation errors when typing
        const formInputs = document.querySelectorAll('#roleForm input[type="text"], #roleForm input[type="checkbox"]');
        formInputs.forEach(function (input) {
            removeValidationErrors(input);
        });
    });
</script>
@endpush
