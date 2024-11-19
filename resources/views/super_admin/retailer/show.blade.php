@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Distributor  Details</h3>
        <div>
            <a href="{{ route('retailer.index') }}" class="btn btn-primary">  <i class="fas fa-arrow-left"></i> Back </a>
            <a href="{{ route('retailer.address.create', $retailer->id) }}" class="btn btn-primary">Add Location</a>
            <a href="{{ route('retailers.reports.create', $retailer->id) }}" class="btn btn-primary">
                Add Report
            </a>
            <!-- View Statement Button -->
            <a href="{{ route('retailer.statement.view', $retailer->id) }}" class="btn btn-primary">View Statement</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Distributor  Information</h5>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">General Information</h6>
            <p><strong>Distributor  Name:</strong> {{ $retailer->first_name }} {{ $retailer->last_name }}</p>
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
            <p><strong>Province:</strong> {{ $address->provinceDetails->name ?? 'Unknown' }}</p>

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

<!-- Table for displaying the statement data -->
@if(isset($statements) && $statements->count() > 0)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Statement Information</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th>Retailer DBA</th>
                        <th>LP DBA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statements as $statement)
                        <tr>
                            <td>{{ $statement->province ?? 'N/A' }}</td>
                            <td>{{ $retailer->dba ?? 'N/A' }}</td>
                            <td>{{ $statement->lp_dba ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

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

@endsection
