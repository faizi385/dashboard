@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Logs</h1>

    <table id="logsTable" class="table table-striped ">
        <thead class="thead-dark">
            <tr>
                <th>Action User</th>
                <th>User</th>
                <th>Time</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                @php
                    $description = is_string($log->description) ? json_decode($log->description, true) : $log->description;
                @endphp
                <tr>
                    <td>
                        @if($log->actionUser)
                            {{ $log->actionUser->first_name }} {{ $log->actionUser->last_name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($log->user)
                            {{ $log->user->first_name }} {{ $log->user->last_name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                    <td>{{ $log->action }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#log{{ $log->id }}" aria-expanded="false" aria-controls="log{{ $log->id }}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </td>
                </tr>
                <tr class="collapse-row">
                    <td style="padding:0px " colspan="5">
                        <div id="log{{ $log->id }}" class="collapse">
                            @if($log->action == 'updated')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-card mb-3">
                                            <div class="custom-card-header">Old User</div>
                                            <div class="custom-card-body">
                                                <table class="table table-bordered small-rounded-table">
                                                    <tbody>
                                                        @if(isset($description['old']))
                                                            @foreach($description['old'] as $key => $value)
                                                                @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                                    <tr>
                                                                        <td><strong>{{ ucfirst($key) }}</strong></td>
                                                                        <td>{{ $value }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-card mb-3">
                                            <div class="custom-card-header">Updated User</div>
                                            <div class="custom-card-body">
                                                <table class="table table-bordered small-rounded-table">
                                                    <tbody>
                                                        @if(isset($description['new']))
                                                            @foreach($description['new'] as $key => $value)
                                                                @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                                    <tr>
                                                                        <td><strong>{{ ucfirst($key) }}</strong></td>
                                                                        <td>{{ $value }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($log->action == 'created')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-card mb-3">
                                            <div class="custom-card-header">Created User</div>
                                            <div class="custom-card-body">
                                                <table class="table table-bordered small-rounded-table">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Name</strong></td>
                                                            <td>{{ $description['name'] ?? '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Email</strong></td>
                                                            <td>{{ $description['email'] ?? '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Role</strong></td>
                                                            <td>{{ isset($description['role']) ? implode(', ', $description['role']) : '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Updated At</strong></td>
                                                            <td>{{ $description['updated_at'] ?? '' }}</td>
                                                        </tr>
                                                        @if(isset($description['phone']) || isset($description['address']))
                                                            <tr>
                                                                <td><strong>Phone</strong></td>
                                                                <td>{{ $description['phone'] ?? '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Address</strong></td>
                                                                <td>{{ $description['address'] ?? '' }}</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<!-- Custom CSS -->
<style>
    .custom-card {
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
    }

    .custom-card-header {
        background-color: #2c3e50;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .custom-card-body {
        padding: 10px;
        background-color: #f9f9f9;
    }

    .small-rounded-table td {
        padding: 5px;
    }

    .table-bordered {
        border: 1px solid black;
        border-radius: 8px;
        overflow: hidden;
    }

  

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #e9ecef;
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }

    /* Remove gap between rows */
    .table-striped tbody tr {
        border-bottom: 1px solid black;
    }

    .fas.fa-plus {
        font-size: 12px;
    }

    .col-md-6 {
        width: 50%;
    }
    /* Ensure table has borders for all cells */
.table {
    border-collapse: collapse;
}

.table-bordered th, .table-bordered td {
    border: 1px solid #dee2e6;
}

/* Add borders to the collapse rows (old and new user sections) */
.custom-card-body table {
    border: 1px solid #dee2e6;
}

/* Add inner borders for cells in the description tables */
.custom-card-body table td, .custom-card-body table th {
    border: 1px solid #dee2e6;
}

/* Remove any padding between rows and ensure they appear as a unified table */
.table-striped tbody tr {
    margin: 0;
    padding: 0;
}

/* Adjust the padding inside table cells for a more compact display */
.table td, .table th {
    padding: 8px;
}

/* Ensure the collapse content has no extra gaps */
.collapse {
    padding: 0;
    margin: 0;
}

/* Description section tables: apply consistent borders */
.small-rounded-table {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    width: 100%;
}

.small-rounded-table td, .small-rounded-table th {
    padding: 8px;
    border: 1px solid #dee2e6;
}

</style>
@endpush


