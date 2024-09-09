@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Province Logs</h1>

    <table id="example" class="table table-striped">
        <thead>
            <tr>
                <th>Action User</th>
                <th>Province Name</th>
                <th>Time</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($provinceLogs as $log)
                @php
                    $description = json_decode($log->description, true);
                @endphp
                <tr>
                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                    <td>{{ $log->province->name }}</td>
                    <td>{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#log{{ $log->id }}" aria-expanded="false" aria-controls="log{{ $log->id }}">
                            <i class="fas fa-plus"></i>
                        </button>
                        <div id="log{{ $log->id }}" class="collapse mt-2">
                            @if($log->action == 'updated')
                                <div class="custom-card mb-3">
                                    <div class="custom-card-header">Old Province</div>
                                    <div class="custom-card-body">
                                        @foreach($description['old'] ?? [] as $key => $value)
                                            @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="custom-card mb-3">
                                    <div class="custom-card-header">Updated Province</div>
                                    <div class="custom-card-body">
                                        @foreach($description['new'] ?? [] as $key => $value)
                                            @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($log->action == 'created')
                                <div class="custom-card mb-3">
                                    <div class="custom-card-header">Created Province</div>
                                    <div class="custom-card-body">
                                        @foreach($description as $key => $value)
                                            <div class="mb-2">
                                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                            </div>
                                        @endforeach
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
        width: 100%; /* Ensure the card takes the full width */
        margin-bottom: 1rem;
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

    .collapse {
        padding: 0;
        margin: 0;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    .fas.fa-plus {
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>
@endpush
