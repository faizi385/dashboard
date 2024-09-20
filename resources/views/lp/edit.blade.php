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
                    <div class="col-md-6 form-group">
                        <label for="name"><i class="fas fa-building"></i> LP Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $lp->name) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="dba"><i class="fas fa-store"></i> DBA</label>
                        <input type="text" name="dba" id="dba" class="form-control" value="{{ old('dba', $lp->dba) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_email"><i class="fas fa-envelope"></i> Primary Contact Email</label>
                        <input type="email" name="primary_contact_email" id="primary_contact_email" class="form-control" value="{{ old('primary_contact_email', $lp->primary_contact_email) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_phone"><i class="fas fa-phone"></i> Primary Contact Phone</label>
                        <input type="text" name="primary_contact_phone" id="primary_contact_phone" class="form-control" value="{{ old('primary_contact_phone', $lp->primary_contact_phone) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label for="primary_contact_position"><i class="fas fa-user-tie"></i> Primary Contact Position</label>
                        <input type="text" name="primary_contact_position" id="primary_contact_position" class="form-control" value="{{ old('primary_contact_position', $lp->primary_contact_position) }}">
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
