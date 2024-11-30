@extends('layouts.admin')

@section('content')
<h1 class="text-white" id="text">Reports</h1>

<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-3">
    <div class="row mb-4">
        <div class="col text-end">
            @if(auth()->user()->hasRole('Retailer'))
            <a href="{{ route('retailers.reports.create', ['retailer' => $retailers->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add Report
            </a>

            @endif

        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="reportsTable" class="table table-hover table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Distributor Organization Name</th>
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
                        <td>{{ ucfirst($report->pos) }}</td>
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
                        @if($report->status == 'Completed')
                            <td>${{ number_format($report->total_fee_sum,2) }}</td>
                        @else
                            <td>$0.00</td>
                        @endif
                        @if($report->status == 'Completed')
                            <td>${{ number_format($report->total_payout_with_tax, 2) }}</td>
                        @else
                            <td>$0.00</td>
                        @endif
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
                            <a href="{{ route('reports.exportStatement', $report->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Distributor  Statement">
                                <i style="color: black" class="fas fa-file-download"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        {{-- <tr><td colspan="11">No reports found.</td></tr> --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



</style>
@push('scripts')
<script>
    $(document).ready(function() {
        // Hide loader after page loads
        $("#loader").fadeOut("slow");

        // Initialize DataTable with responsive and horizontal scrolling options
        const table = $('#reportsTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: {
                emptyTable: "No reports found."
            },
            initComplete: function() {
                $('#loader').addClass('hidden'); // Hide the loader once the table is initialized

                // Prepend month filter to DataTable search box section
                $("#reportsTable_filter").prepend(`
                    <span class="me-2 text-white" style="font-weight: bold;">Filter:</span>
    <label class="me-3">
        <input type="month" id="monthFilter" class="form-control form-control-sm" placeholder="Select month" />
    </label>
                `);

                // Attach month filter change event to filter table
                $('#monthFilter').on('change', function() {
                    const selectedMonth = $(this).val();
                    if (selectedMonth) {
                        table.column(5).search(selectedMonth).draw(); // Assumes date column is column index 5
                    } else {
                        table.column(5).search('').draw();
                    }
                });
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
<style>

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{
        color: white  !important;}
</style>
@endsection
