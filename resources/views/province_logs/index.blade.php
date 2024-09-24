@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Province Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>

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
                    <div class="modal-dialog modal-sm">
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


@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#example').DataTable({
            "responsive": true,
            "autoWidth": false,
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
            }
        });
    });
</script>
@endpush
