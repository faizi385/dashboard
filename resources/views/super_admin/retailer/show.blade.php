@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Retailer Details</h3>
        <div>
            <a href="{{ route('retailer.index') }}" class="btn btn-secondary">Back to List</a>
            <a href="{{ route('retailer.address.create', $retailer->id) }}" class="btn btn-primary">Add Location</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Retailer Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">General Information</h6>
            <p><strong>Retailer Name:</strong> {{ $retailer->first_name }} {{ $retailer->last_name }}</p>
            <p><strong>Corporate Name:</strong> {{ $retailer->corporate_name ?? '-' }}</p>
            <p><strong>DBA:</strong> {{ $retailer->dba ?? '-' }}</p>
            <p><strong>Phone:</strong> {{ $retailer->phone }}</p>
            <p><strong>Email:</strong> {{ $retailer->email }}</p>
        </div>
    </div>

    @if(optional($retailer->address)->count() > 0)
    @foreach($retailer->address as $index => $address)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Address {{ $index + 1 }} Information</h5>
  
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Address Details</h6>
            <p><strong>Street No:</strong> {{ $address->street_no }}</p>
            <p><strong>Street Name:</strong> {{ $address->street_name }}</p>
            <p><strong>Province:</strong> {{ $address->province }}</p>
            <p><strong>City:</strong> {{ $address->city }}</p>
            <p><strong>Location:</strong> {{ $address->location }}</p>
            <p><strong>Contact Person Name:</strong> {{ $address->contact_person_name }}</p>
        </div>
    </div>
    @endforeach
@else
    <p class="text-muted mt-4">No address details available.</p>
   
@endif

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
