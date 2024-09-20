@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>LP Details</h3>
        <div>
            <a href="{{ route('lp.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">LP Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">General Information</h6>
            <p><strong>LP Name:</strong> {{ $lp->name }}</p>
            <p><strong>DBA:</strong> {{ $lp->dba }}</p>
            <p><strong>Primary Contact Email:</strong> {{ $lp->primary_contact_email }}</p>
            <p><strong>Primary Contact Phone:</strong> {{ $lp->primary_contact_phone }}</p>
            <p><strong>Primary Contact Position:</strong> {{ $lp->primary_contact_position }}</p>
        </div>
    </div>

    @forelse($lp->address as $index => $address)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Address {{ $index + 1 }} Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Address Details</h6>
            <p><strong>Street No:</strong> {{ $address->street_number }}</p>
            <p><strong>Street Name:</strong> {{ $address->street_name }}</p>
            <p><strong>Postal Code:</strong> {{ $address->postal_code }}</p>
            <p><strong>City:</strong> {{ $address->city }}</p>
        </div>
    </div>
    @empty
    <p class="text-muted mt-4">No address details available.</p>
    @endforelse

</div>

<style>
    .container {
        margin-top: 20px;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #2c3e50;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
    }

    .card-body {
        padding: 15px;
        background-color: #f9f9f9;
    }

    .mt-4 {
        margin-top: 1.5rem;
    }

    .container {
        padding-bottom: 100px; /* Adjust this value based on footer height */
    }
</style>
@endsection
