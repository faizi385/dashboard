@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1><i class="fas fa-user-plus"></i> Create Retailer</h1>

    <!-- Form without card -->
    <form action="{{ route('retailers.store') }}" method="POST">
        @csrf

        <!-- Row for First Name and Last Name -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name"><i class="fas fa-user"></i> First Name</label>
                    <input type="text" name="first_name" class="form-control" id="first_name" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name"><i class="fas fa-user"></i> Last Name</label>
                    <input type="text" name="last_name" class="form-control" id="last_name" required>
                </div>
            </div>
        </div>

        <!-- Row for Email and Phone -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" name="phone" class="form-control" id="phone" required>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-sm w-20"><i class="fas fa-paper-plane"></i> Create Retailer</button>
    </form>
</div>
@endsection

@section('scripts')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<script>
    $(document).ready(function() {
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>
@endsection
