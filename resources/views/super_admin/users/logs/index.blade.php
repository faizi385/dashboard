@extends('layouts.app')

@section('content')
<div class="container p-2">
    <h1 class="text-white">User Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">User Log</h5>
        </div>
        <div class="card-body">
            <table id="userLogsTable" class="table table-striped">
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
                            <td>{{ $log->actionUser ? ucfirst($log->actionUser->first_name) . ' ' . ucfirst($log->actionUser->last_name) : 'System' }}</td>
                            <td>{{ $log->user ? ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name) : '-' }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ ucfirst($log->action) }}</td>
                            <td class="text-center">
                                <button class="btn btn-link p-0  " type="button" data-bs-toggle="modal" data-bs-target="#userLogModal{{ $log->id }}">
                                    <i style="color: black"  class="fas fa-eye "></i>
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
                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                    @if($key === 'password')
                                                                        ********** <!-- Mask old password -->
                                                                    @elseif($key === 'updated_at' || $key === 'created_at')
                                                                        {{ \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') }} <!-- Format old updated_at -->
                                                                    @elseif(is_array($value))
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
                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                @if($key === 'password')
                    ********** <!-- Mask new password -->
                @elseif($key === 'updated_at' || $key === 'created_at')
                    {{ \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') }} <!-- Format new updated_at -->
                @elseif(is_array($value))
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
                                                    @if($key === 'updated_at' || $key === 'created_at')
                                                        <div class="mb-2">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            {{ \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') }} <!-- Format created_at or updated_at -->
                                                        </div>
                                                    @elseif($key !== 'role') <!-- Exclude the role field -->
                                                        <div class="mb-2">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
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
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>

</style>
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
