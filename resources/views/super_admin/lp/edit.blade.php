@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit LP</h1>

    <form action="{{ route('lp.update', $lp) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <!-- LP Name -->
                    <div class="col-md-6 form-group">
                        <label for="name"><i class="fas fa-building"></i> LP Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $lp->name) }}" >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- DBA -->
                    <div class="col-md-6 form-group">
                        <label for="dba"><i class="fas fa-store"></i> DBA</label>
                        <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" value="{{ old('dba', $lp->dba) }}">
                        @error('dba')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <!-- Primary Contact Email -->
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_email"><i class="fas fa-envelope"></i> Primary Contact Email</label>
                        <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control @error('primary_contact_email') is-invalid @enderror" value="{{ old('primary_contact_email', $lp->primary_contact_email) }}" >
                        @error('primary_contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Primary Contact Phone -->
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_phone"><i class="fas fa-phone"></i> Primary Contact Phone</label>
                        <input type="text" name="primary_contact_phone" id="primary_contact_phone" class="form-control @error('primary_contact_phone') is-invalid @enderror" value="{{ old('primary_contact_phone', $lp->primary_contact_phone) }}">
                        @error('primary_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <!-- Primary Contact Position -->
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_position"><i class="fas fa-user-tie"></i> Primary Contact Position</label>
                        <input type="text" name="primary_contact_position" id="primary_contact_position" class="form-control @error('primary_contact_position') is-invalid @enderror" value="{{ old('primary_contact_position', $lp->primary_contact_position) }}">
                        @error('primary_contact_position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update LP</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
