@extends('layouts.admin')

@section('content')
<h1 class="text-white" id="text">Reports</h1>

<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-3">
    <div class="row mb-4">
        <div class="col text-end">
            {{-- @if(auth()->user()->hasRole('Retailer'))
            <a href="{{ route('retailers.reports.create', ['retailer' => auth()->user()->id]) }}" class="btn btn-primary mt-3">Add Report</a>
        @endif --}}
        
        
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="reportsTable" class="table table-hover table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Retailer DBA</th>
                        <th>Location</th>
                        <th>POS</th>
                        <th>File 1</th>
                        <th>File 2</th>
                        <th>Date</th>
                        <th>Payout without Tax</th>
                        <th>Payout with Tax</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->retailer->dba ?? '-' }}</td>
                        <td>{{ $report->location }}</td>
                        <td>{{ $report->pos }}</td>
                        <td>
                            <a href="{{ route('reports.downloadFile', ['reportId' => $report->id, 'fileNumber' => 1]) }}" download="{{ basename($report->file_1) }}">
                                Download File 1
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('reports.downloadFile', ['reportId' => $report->id, 'fileNumber' => 2]) }}" download="{{ basename($report->file_2) }}">
                                Download File 2
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($report->date)->format('Y-m-d') }}</td>
                        <td>${{ number_format($retailerSums[$report->retailer_id]['total_fee_sum'] ?? 0, 2) }}</td>
                        <td>${{ number_format($retailerSums[$report->retailer_id]['total_payout_with_tax'] ?? 0, 2) }}</td>
                        <td>{{ $report->status }}</td>
                        <td class="text-center">
                            <form action="{{ route('reports.destroy', ['report' => $report->id]) }}" method="POST" style="display:inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link p-0 delete-report" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Report">
                                    <i style="color: black" class="fas fa-trash"></i>
                                </button>
                            </form>

                            <!-- Export CleanSheet Icon -->
                            <a href="{{ route('reports.exportCleanSheets', $report->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Export CleanSheet">
                                <i style="color: black" class="fas fa-file-export"></i>
                            </a>

                            <!-- Export Retailer Statement Icon -->
                            <a href="{{ route('reports.exportStatement', $report->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Retailer Statement">
                                <i style="color: black" class="fas fa-file-download"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="11">No reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
    /* General table styling */
table.dataTable {
    width: 100%;
    background-color: white;
    border-collapse: collapse;
}

table.dataTable th, 
table.dataTable td {
    padding: 12px;
    border: 1px solid #ddd;
}

table.dataTable thead th {
    background-color: #f5f5f5;
    color: #333;
}

/* Alignment and centering */
table.dataTable thead th {
    text-align: center;
}

table.dataTable tbody td {
    text-align: center;
    vertical-align: middle; /* Center vertically */
}

table.dataTable tbody td. {
    text-align: left; /* Left-align for specific columns */
}

/* Actions column */
table.dataTable tbody td.actions-column {
    width: 100px;
    text-align: center;
}


/* Set the background for alternating rows */
table.dataTable tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

table.dataTable tbody tr:nth-child(even) {
    background-color: #fff;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled{
    color: white  !important;
}

</style>
@push('scripts')
<script>
    $(document).ready(function() {
        // Hide loader after page loads
        $("#loader").fadeOut("slow");

        // Initialize DataTable with responsive and horizontal scrolling options
        const table = $('#reportsTable').DataTable({
            responsive: true,
            scrollX: true, // Enable horizontal scrolling
            language: {
                emptyTable: "No offers found." // Custom message for no data
            },
            initComplete: function() {
                $('#loader').addClass('hidden'); // Hide the loader once the table is initialized
            }
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Function to handle delete confirmation with SweetAlert
        function initializeDeleteConfirmation() {
            $('.delete-report').each(function() {
                $(this).off('click').on('click', function(e) {
                    e.preventDefault();
                    const deleteForm = $(this).closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteForm.submit();
                        }
                    });
                });
            });
        }

        // Call the function to initialize delete confirmation on page load
        initializeDeleteConfirmation();

        // Reinitialize delete confirmation after each DataTable redraw (pagination, search, etc.)
        table.on('draw', function() {
            initializeDeleteConfirmation();
        });

        // Display Toastr messages if session has toast_success
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>

@endpush

@endsection
