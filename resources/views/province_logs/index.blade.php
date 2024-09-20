@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Province Logs</h1>

    <table id="example" class="table">
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
                    <td>{{ $log->province ? $log->province->name : 'N/A' }}</td>
                    <td>{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-sm"> <!-- Updated to modal-sm -->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="logModalLabel{{ $log->id }}">Action Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($log->action == 'updated')
                                    <div class="card-container">
                                        <div class="custom-card old-card half-width-card">
                                            <div class="custom-card-header">Old Province</div>
                                            <div class="custom-card-body">
                                                @foreach($description['old'] ?? [] as $key => $value)
                                                @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                    <div class="mb-2">
                                                        <strong>{{ ucfirst($key) }}:</strong>
                                                        @if($key === 'status')
                                                            {{ $value == 1 ? 'Active' : 'Inactive' }}
                                                        @elseif(is_string($value) && strtotime($value) !== false)
                                                            {{ \Carbon\Carbon::parse($value)->format('d-M-Y h:i A') }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                            
                                            </div>
                                        </div>
                                        <div class="custom-card updated-card half-width-card">
                                            <div class="custom-card-header">Updated Province</div>
                                            <div class="custom-card-body">
                                                @foreach($description['new'] ?? [] as $key => $value)
                                                @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                    <div class="mb-2">
                                                        <strong>{{ ucfirst($key) }}:</strong>
                                                        @if($key === 'status')
                                                            {{ $value == 1 ? 'Active' : 'Inactive' }}
                                                        @elseif(is_string($value) && strtotime($value) !== false)
                                                            {{ \Carbon\Carbon::parse($value)->format('d-M-Y h:i A') }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                            
                                            </div>
                                        </div>
                                    </div>
                                @elseif($log->action == 'created')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Created Province</div>
                                        <div class="custom-card-body">
                                            @foreach($description as $key => $value)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                    @if(is_string($value) && strtotime($value) !== false)
                                                        {{ \Carbon\Carbon::parse($value)->format('d-M-Y h:i A') }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($log->action == 'deleted')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Deleted Province</div>
                                        <div class="custom-card-body">
                                            @foreach($description as $key => $value)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                    @if(is_string($value) && strtotime($value) !== false)
                                                        {{ \Carbon\Carbon::parse($value)->format('d-M-Y h:i A') }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

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
        margin-bottom: 1rem;
    }

    .custom-card-header {
        background-color: #2c3e50;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
    }

    .custom-card-body {
        padding: 10px;
        background-color: #f9f9f9;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
    .modal-dialog {
        width: 90%; /* Adjust width as necessary */
        max-width: 600px; /* Set a max-width */
    }
    .modal-content {
        height: auto; /* Adjust height as necessary */
    }
    .modal-body {
        max-height: 300px; /* Adjust height if needed */
        overflow-y: auto;
    }
    .card-container {
        display: flex;
        gap: 1rem;
    }

    .custom-card.old-card,
    .custom-card.updated-card {
        flex: 1;
    }

    .half-width-card {
        width: 100%;
    }

    .updated-card {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "responsive": true,
            "autoWidth": false
        });
    });
</script>
@endpush
