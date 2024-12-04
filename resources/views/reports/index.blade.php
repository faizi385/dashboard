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
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Reports</h5>
        </div>
        <div class="card-body">
            <table id="reportsTable" class="table table-striped">
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
            lengthMenu: [10, 25, 50, 100],
            language: {
                emptyTable: "No reports found."
            },
            initComplete: function() {
                $('#loader').addClass('hidden');
                $("#reportsTable_filter").prepend(`
                    <span class="me-2 " style="font-weight: bold;">Filter:</span>
                    <label class="me-3">
                        <div class="input-group date">
                            <input type="text" class="form-control" id="calendarFilter" placeholder="Select a date" value="{{ \Carbon\Carbon::parse($date)->format('F-Y') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </label>
                `);
                $('#calendarFilter').on('change', function() {
                    const selectedMonth = $(this).val();
                    if (selectedMonth) {
                        window.location.href = "{{ route('super_admin.reports.index') }}?month=" + selectedMonth;
                    } else {
                        window.location.href = "{{ route('super_admin.reports.index') }}";
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
        $('#calendarFilter').datepicker({
            format: 'MM-yyyy',
            minViewMode: 1,
            autoclose: true,
            startView: "months",
            viewMode: "months",
            minDate: new Date(),
            onSelect: function(dateText) {
                var formattedDate = $.datepicker.formatDate('MM-yyyy', new Date(dateText));
                $('#calendarFilter').val(formattedDate);
            },
            setDate: new Date(),
            changeMonth: true,
            changeYear: true
        });
        // Display Toastr messages if session has toast_success
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush
@endsection
