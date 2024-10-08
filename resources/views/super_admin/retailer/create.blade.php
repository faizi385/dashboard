@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1><i class="fas fa-user-plus"></i> Create Retailer</h1>

    <div class="bg-white p-4 rounded shadow-sm mb-4"> <!-- Added white background -->
        <!-- Form without card -->
        <form action="{{ route('retailers.store') }}" method="POST" id="retailerForm">
            @csrf

            <!-- Row for First Name and Last Name -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="first_name"><i class="fas fa-user"></i> First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name">
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
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" id="last_name">
                        @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Row for Email and Phone -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email">
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
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone">
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-sm w-20">
                <i class="fas fa-paper-plane"></i> Create Retailer
            </button>
        </form>
    </div> <!-- End of white background div -->
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Function to remove validation error when the user types in the field
        $('input').on('input', function() {
            // If the input has the 'is-invalid' class, remove it
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
                
                // Find the closest .invalid-feedback element and remove it
                $(this).next('.invalid-feedback').remove();
            }
        });

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>
@endsection
