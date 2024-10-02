@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>

    <table id="userLogsTable" class="table">
        <thead>
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
                    $description = json_decode($log->description, true);
                @endphp
                <tr>
                    <td>{{ $log->actionUser ? $log->actionUser->first_name . ' ' . $log->actionUser->last_name : 'System' }}</td>
                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'N/A' }}</td>
                    <td>{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#userLogModal{{ $log->id }}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="userLogModal{{ $log->id }}" tabindex="-1" aria-labelledby="userLogModalLabel{{ $log->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content custom-modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="userLogModalLabel{{ $log->id }}">Action Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($log->action == 'updated')
                                    <div class="row">
                                        <!-- Old User Card -->
                                        <div class="col-md-6">
                                            <div class="custom-card old-card">
                                                <div class="custom-card-header">Old User</div>
                                                <div class="custom-card-body">
                                                    @foreach($description['old'] ?? [] as $key => $value)
                                                        @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                            <div class="mb-2">
                                                                <strong>{{ ucfirst($key) }}:</strong> 
                                                                @if(is_array($value))
                                                                    {{ implode(', ', $value) }}
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Updated User Card -->
                                        <div class="col-md-6">
                                            <div class="custom-card updated-card">
                                                <div class="custom-card-header">Updated User</div>
                                                <div class="custom-card-body">
                                                    @foreach($description['new'] ?? [] as $key => $value)
                                                        @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                            <div class="mb-2">
                                                                <strong>{{ ucfirst($key) }}:</strong> 
                                                                @if(is_array($value))
                                                                    {{ implode(', ', $value) }}
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($log->action == 'created')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Created User</div>
                                        <div class="custom-card-body">
                                            @foreach($description as $key => $value)
                                                @if($key === 'updated_at')
                                                    <div class="mb-2">
                                                        <strong>{{ ucfirst($key) }}:</strong>
                                                        {{ \Carbon\Carbon::parse($value)->format('d-M-Y h:i A') }} <!-- Format updated_at -->
                                                    </div>
                                                @elseif($key !== 'role') <!-- Exclude the role field -->
                                                    <div class="mb-2">
                                                        <strong>{{ ucfirst($key) }}:</strong> 
                                                        @if(is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($log->action == 'deleted')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Deleted User</div>
                                        <div class="custom-card-body">
                                            @foreach($description as $key => $value)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong> 
                                                    @if(is_array($value))
                                                        {{ implode(', ', $value) }}
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


@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#userLogsTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true, // Enables pagination
            "order": [[2, "desc"]], // Sort by the 'Time' column in descending order
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
            }
        });
    });
</script>
@endpush
