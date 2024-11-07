@extends('layouts.admin')

@section('content')
<h1 class="text-white">Retailer Logs</h1>

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container">
    <table id="example" class="table table-hover">
        <thead>
            <tr>
                <th>Action User</th>
                <th>Retailer DBA</th>
                <th>Time</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($retailerLogs as $log)
                @php
                    $description = json_decode($log->description, true);
                @endphp
                <tr>
                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                    <td>
                        @if($log->retailer && $log->retailer->deleted_at === null)
                            {{ $log->retailer->dba }}
                        @elseif($log->retailer_dba)
                            {{ $log->retailer_dba }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td class="text-center">
                        <button class="btn btn-link p-0" type="button" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                            <i style="color: black" class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal -->
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
                                            <div class="custom-card-header">Old Retailer</div>
                                            <div class="custom-card-body">
                                                @foreach($description['old'] ?? [] as $key => $value)
                                                    @if(isset($description['new'][$key]) && $description['old'][$key] !== $description['new'][$key] && !in_array($key, ['id', 'user_id', 'corporate_name', 'dba', 'created_at', 'updated_at']))
                                                        <div class="mb-2">
                                                            <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                                <div class="mb-2">
                                                    <strong>Created At:</strong> {{ \Carbon\Carbon::parse($log->created_at)->format('d-M-Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-card updated-card half-width-card">
                                            <div class="custom-card-header">Updated Retailer</div>
                                            <div class="custom-card-body">
                                                @foreach($description['new'] ?? [] as $key => $value)
                                                    @if(isset($description['old'][$key]) && $description['old'][$key] !== $description['new'][$key] && !in_array($key, ['id', 'user_id', 'created_at', 'updated_at']))
                                                        <div class="mb-2">
                                                            <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                                <div class="mb-2">
                                                    <strong>Updated At:</strong> {{ \Carbon\Carbon::parse($log->updated_at)->format('d-M-Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($log->action == 'created')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Created Retailer</div>
                                        <div class="custom-card-body">
                                            @foreach($description as $key => $value)
                                                @if(!in_array($key, ['id', 'user_id', 'created_at', 'updated_at']))
                                                    <div class="mb-2">
                                                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    </div>
                                                @endif
                                            @endforeach
                                            <div class="mb-2">
                                                <strong>Created At:</strong> {{ \Carbon\Carbon::parse($log->created_at)->format('d-M-Y h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($log->action == 'deleted')
                                    <div class="custom-card">
                                        <div class="custom-card-header">Deleted Retailer</div>
                                        <div class="custom-card-body">
                                            @foreach($description as $key => $value)
                                                @if(!in_array($key, ['id', 'user_id', 'created_at', 'updated_at']))
                                                    <div class="mb-2">
                                                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                                                        @if(is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                            <div class="mb-2">
                                                <strong>Deleted At:</strong> {{ \Carbon\Carbon::parse($log->created_at)->format('d-M-Y h:i A') }}
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        $('#example').DataTable({
            "initComplete": function() {
                $('#loader').addClass('hidden');
            }
        });
    });
</script>
@endpush
