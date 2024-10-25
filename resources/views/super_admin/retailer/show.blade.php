@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Retailer Details</h3>
        <div>
            <a href="{{ route('retailer.index') }}" class="btn btn-primary">  <i class="fas fa-arrow-left"></i> Back </a>
            <a href="{{ route('retailer.address.create', $retailer->id) }}" class="btn btn-primary">Add Location</a>
            <a href="{{ route('retailers.reports.create', $retailer->id) }}" class="btn btn-primary">
                Add Report
            </a>
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
        background-color: white;
        color: black;
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

{{-- <script>
    $(document).ready(function() {
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        
        // Display specific Greenline errors
        @if (session('greenline_error'))
            toastr.error("{{ session('greenline_error') }}");
        @endif
    });
</script> --}}
@endsection