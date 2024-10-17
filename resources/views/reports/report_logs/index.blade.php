@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-white">Report Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>

    <table id="reportLogsTable" class="table">
        <thead>
            <tr>
                <th>Action User</th>
                <th>Report ID</th>
                <th>Time</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportLogs as $log)
                @php
                    $description = json_decode($log->description, true) ?? []; // Fallback to empty array if null
                @endphp
                <tr>
                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                    <td>{{ $log->report_id }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td class="text-center">
                        <button class="btn btn-link p-0" type="button" data-bs-toggle="modal" data-bs-target="#reportLogModal{{ $log->id }}">
                            <i style="color: black" class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="reportLogModal{{ $log->id }}" tabindex="-1" aria-labelledby="reportLogModalLabel{{ $log->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content custom-modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reportLogModalLabel{{ $log->id }}">Action Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($log->action == 'created' || $log->action == 'updated')
                                    <div class="row">
                                        <div class="col-md-12">
                                            {{-- <div class="mb-2">
                                                <strong>Retailer:</strong>
                                                {{ $log->report->retailer ? $log->report->retailer->dba ?? null : 'N/A' }}
                                            </div> --}}
                                        </div>
                                        @if($log->action == 'updated')
                                            <!-- Old User Card -->
                                            <div class="col-md-6">
                                                <div class="custom-card old-card">
                                                    <div class="custom-card-header">Old Report Details</div>
                                                    <div class="custom-card-body">
                                                        @foreach($description['old'] ?? [] as $key => $value)
                                                            @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                                <div class="mb-2">
                                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Updated User Card -->
                                            <div class="col-md-6">
                                                <div class="custom-card updated-card">
                                                    <div class="custom-card-header">Updated Report Details</div>
                                                    <div class="custom-card-body">
                                                        @foreach($description['new'] ?? [] as $key => $value)
                                                            @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                                <div class="mb-2">
                                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Display for Created Report -->
                                            <div class="custom-card">
                                                <div class="custom-card-header">{{ ucfirst($log->action) }} Report</div>
                                                <div class="custom-card-body">
                                                    @foreach($description['new'] ?? [] as $key => $value)
                                                        <div class="mb-2">
                                                            <strong>{{ ucfirst($key) }}:</strong>
                                                            {{ is_array($value) ? implode(', ', $value) : $value }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($log->action == 'deleted')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Deleted Report</div>
                                        <div class="custom-card-body">
                                            @foreach($description['old'] ?? [] as $key => $value)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                    {{ is_array($value) ? implode(', ', $value) : $value }}
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
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#reportLogsTable').DataTable({
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
