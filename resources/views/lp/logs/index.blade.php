@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>LP Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>

    <table id="example" class="table">
        <thead>
            <tr>
                <th>Action User</th>
                <th>LP DBA</th>
                <th>Time</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lpLogs as $log)
                @php
                    $description = json_decode($log->description, true);
                @endphp
                <tr>
                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                    <td>{{ $log->lp ? $log->lp->dba : 'N/A' }}</td>
                    <td>{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal for viewing log details -->
                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="logModalLabel{{ $log->id }}">Action Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($log->action == 'updated')
                                    <div class="card-container">
                                        <div class="custom-card old-card half-width-card">
                                            <div class="custom-card-header">Old LP</div>
                                            <div class="custom-card-body">
                                                <div class="mb-2">
                                                    <strong>Name:</strong> {{ $description['old']['name'] ?? 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>DBA:</strong> {{ $log->lp->dba ?? 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Primary Contact Email:</strong> {{ $description['old']['primary_contact_email'] ?? 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Created At:</strong>
                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d-M-Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-card updated-card half-width-card">
                                            <div class="custom-card-header">Updated LP</div>
                                            <div class="custom-card-body">
                                                <div class="mb-2">
                                                    <strong>Name:</strong> {{ $description['new']['name'] ?? 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>DBA:</strong> {{ $log->lp->dba ?? 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Primary Contact Email:</strong> {{ $description['new']['primary_contact_email'] ?? 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Updated At:</strong>
                                                    {{ \Carbon\Carbon::parse($log->updated_at)->format('d-M-Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($log->action == 'created' || $log->action == 'deleted')
                                    <div class="custom-card">
                                        <div class="custom-card-header">{{ ucfirst($log->action) }} LP</div>
                                        <div class="custom-card-body">
                                            <div class="mb-2">
                                                <strong>Name:</strong> {{ $description['name'] ?? 'N/A' }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>DBA:</strong> {{ $log->lp->dba ?? 'N/A' }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Primary Contact Email:</strong> {{ $description['primary_contact_email'] ?? 'N/A' }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Created At:</strong>
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('d-M-Y h:i A') }}
                                            </div>
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




@push('scripts')
<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#example').DataTable({
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
            }
        });
    });
</script>
@endpush
