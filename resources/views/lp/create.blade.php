@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="my-4">Create LP</h1>

    <!-- Display validation errors if any -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-4 rounded shadow-sm mb-4"> <!-- Added white background -->
        <form id="createLpForm" action="{{ route('lp.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <!-- LP Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label"><i class="fas fa-building"></i> LP Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- DBA -->
                <div class="col-md-6 mb-3">
                    <label for="dba" class="form-label"><i class="fas fa-tag"></i> DBA</label>
                    <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" required>
                    @error('dba')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- Primary Contact Email -->
                <div class="col-md-6 mb-3">
                    <label for="primary_contact_email" class="form-label"><i class="fas fa-envelope"></i> Primary Contact Email</label>
                    <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control @error('primary_contact_email') is-invalid @enderror" required>
                    @error('primary_contact_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Primary Contact Phone -->
                <div class="col-md-6 mb-3">
                    <label for="primary_contact_phone" class="form-label"><i class="fas fa-phone"></i> Primary Contact Phone</label>
                    <input type="text" name="primary_contact_phone" id="primary_contact_phone" class="form-control @error('primary_contact_phone') is-invalid @enderror" required>
                    @error('primary_contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- Primary Contact Position -->
                <div class="col-md-6 mb-3">
                    <label for="primary_contact_position" class="form-label"><i class="fas fa-user-tie"></i> Primary Contact Position</label>
                    <input type="text" name="primary_contact_position" id="primary_contact_position" class="form-control @error('primary_contact_position') is-invalid @enderror" required>
                    @error('primary_contact_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create LP</button>
        </form>
    </div> <!-- End of white background div -->
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
    @if(session('toast_success'))
        toastr.success('{{ session('toast_success') }}', 'Success');
    @endif

    @if($errors->any())
        toastr.error('There were some errors. Please check the form and try again.', 'Error');
    @endif
</script>
@endsection
@endsection
