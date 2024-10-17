@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white"><i class="fas fa-user-plus"></i> Create Retailer</h1>
        <a href="{{ route('retailer.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <!-- Form for creating retailer -->
        <form action="{{ route('retailers.store') }}" method="POST" id="retailerForm">
            @csrf

            <!-- First Name and Last Name -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="first_name"><i class="fas fa-user"></i> First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name" value="{{ old('first_name') }}">
                        @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="last_name"><i class="fas fa-user"></i> Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" 
                               class="form-control @error('last_name') is-invalid @enderror" 
                               id="last_name"
                               oninput="removeValidation(this)"
                               value="{{ old('last_name') }}">
                        @error('last_name')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Email and Phone -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit and Clear Buttons -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-paper-plane"></i> Create Retailer
                </button>
                {{-- <button type="button" class="btn btn-primary btn-sm" id="clearFormButton">
                    <i class="fas fa-eraser"></i> Clear
                </button> --}}
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Clear button functionality
        $('#clearFormButton').on('click', function() {
            // Reset all fields in the form
            $('#retailerForm')[0].reset(); 

            // Optionally, clear any validation error styles
            $('#retailerForm input').removeClass('is-invalid');
            $('#retailerForm .invalid-feedback').hide();
        });

        // Remove validation errors when the user types in the fields
        $('#retailerForm input').on('input', function() {
            let $input = $(this); // Current input element
            if ($input.hasClass('is-invalid')) {
                // Remove the 'is-invalid' class
                $input.removeClass('is-invalid');
                // Hide the validation error message
                $input.next('.invalid-feedback').hide();
            }
        });

        // Display success/error messages with Toastr
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>
@endsection
