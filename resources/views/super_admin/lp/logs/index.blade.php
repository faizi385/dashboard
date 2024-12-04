@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h1 class="text-white">Supplier Logs</h1>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="loader"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Supplier Log</h5>
        </div>
        <div class="card-body">
            <table id="example" class="table table-striped">
                <thead>
                    <tr>
                        <th>Action User</th>
                        <th>Organization Name</th>
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
                            <td class="text-center">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                    <i style="color: black" class="fas fa-eye"></i>
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
                                                    <div class="custom-card-header">Old Supplier</div>
                                                    <div class="custom-card-body">
                                                        @if(isset($description['old']))
                                                            @foreach ($description['old'] as $field => $value)
                                                                @if (isset($description['new'][$field]) && $description['new'][$field] != $value)
                                                                    @if($field == 'lp_id')
                                                                        @php $field = 'supplier_id'; @endphp
                                                                    @endif
                                                                    @if($field == 'dba')
                                                                        @php $field = 'organization_name'; @endphp
                                                                    @endif
                                                                    @if($field != 'email')
                                                                        @php $value =  ucwords($value); @endphp
                                                                    @endif
                                                                    @if($field == 'created_at' || $field == 'updated_at')
                                                                        @php $value = ucwords(\Carbon\Carbon::parse($value)->format('Y-m-d h:i:s')); @endphp
                                                                    @endif
                                                                    <div class="mb-2">
                                                                        <strong>{{ ucwords(str_replace('_', ' ', $field)) }}:</strong> {{ $value ?? 'N/A' }}
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <div class="mb-2">Old data not available</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="custom-card updated-card half-width-card">
                                                    <div class="custom-card-header">Updated Supplier</div>
                                                    <div class="custom-card-body">
                                                        @if(isset($description['new']))
                                                            @foreach ($description['new'] as $field => $value)
                                                                @if (isset($description['old'][$field]) && $description['old'][$field] != $value)
                                                                    @if($field == 'lp_id')
                                                                        @php $field = 'supplier_id'; @endphp
                                                                    @endif
                                                                    @if($field == 'dba')
                                                                        @php $field = 'organization_name'; @endphp
                                                                    @endif
                                                                    @if($field != 'email')
                                                                        @php $value =  ucwords($value); @endphp
                                                                    @endif
                                                                    <div class="mb-2">
                                                                        <strong>{{ ucwords(str_replace('_', ' ', $field)) }}:</strong> {{ $value ?? 'N/A' }}
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <div class="mb-2">New data not available</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($log->action == 'created' || $log->action == 'deleted')
                                            <div class="custom-card">
                                                <div class="custom-card-header">{{ ucfirst($log->action) }} Supplier</div>
                                                <div class="custom-card-body">
                                                    <div class="mb-2">
                                                        <strong>Name:</strong> {{ $description['name'] ?? 'N/A' }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Organization Name:</strong> {{ $log->lp->dba ?? 'N/A' }}
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
<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#example').DataTable({
            "initComplete": function() {
                // Hide the loader once the table is initialized
                $('#loader').addClass('hidden');
            }
        });
    });
</script>
@endpush
