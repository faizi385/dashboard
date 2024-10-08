@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-white">Carveout Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>

    <table id="carveoutLogsTable" class="table">
        <thead>
            <tr>
                <th>Action User</th>
                <th>Retailer DBA</th>
                <th>LP DBA</th>
                <th>Time</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($carveoutLogs as $log)
                @php
                    $description = json_decode($log->description, true) ?? []; // Decode JSON description
                @endphp
                <tr>
                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                    <td>{{ $log->carveout ? optional($log->carveout->retailer)->dba : 'N/A' }}</td>
                    <td>{{ $log->carveout ? optional($log->carveout->lp)->dba : 'N/A' }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td class="text-center">
                        <button class="btn btn-link p-0" type="button" data-bs-toggle="modal" data-bs-target="#carveoutLogModal{{ $log->id }}">
                            <i style="color: black"  class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal for Viewing Log Description -->
              <!-- Modal for Viewing Log Description -->
<div class="modal fade" id="carveoutLogModal{{ $log->id }}" tabindex="-1" aria-labelledby="carveoutLogModalLabel{{ $log->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content custom-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carveoutLogModalLabel{{ $log->id }}">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($log->action == 'created' || $log->action == 'updated')
                    <div class="row">
                        @if($log->action == 'updated')
                            <!-- Old Data Card -->
                            <div class="col-md-6">
                                <div class="custom-card old-card">
                                    <div class="custom-card-header">Old Carveout</div>
                                    <div class="custom-card-body">
                                        @foreach($description['old'] ?? [] as $key => $value)
                                            @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                    @if($key === 'password')
                                                        ********** <!-- Mask old password -->
                                                    @elseif($key === 'updated_at' || $key === 'created_at')
                                                        {{ \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') }} <!-- Format old timestamps -->
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
                            <!-- Updated Data Card -->
                            <div class="col-md-6">
                                <div class="custom-card updated-card">
                                    <div class="custom-card-header">Updated Carveout</div>
                                    <div class="custom-card-body">
                                        @foreach($description['new'] ?? [] as $key => $value)
                                            @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key])
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                    @if($key === 'password')
                                                        ********** <!-- Mask new password -->
                                                    @elseif($key === 'updated_at' || $key === 'created_at')
                                                        {{ \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') }} <!-- Format new timestamps -->
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
                        @else
                            <!-- Display for Created Carveout -->
                            <div class="custom-card">
                                <div class="custom-card-header">{{ ucfirst($log->action) }} Carveout</div>
                                <div class="custom-card-body">
                                    @foreach($description['new'] ?? [] as $key => $value)
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
                @elseif($log->action == 'deleted')
                    <div class="custom-card">
                        <div class="custom-card-header">Deleted Carveout</div>
                        <div class="custom-card-body">
                            @foreach($description['old'] ?? [] as $key => $value)
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
        $('#carveoutLogsTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true, // Enables pagination
            "order": [[3, "desc"]], // Sort by the 'Time' column in descending order
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
            }
        });
    });
</script>
@endpush
