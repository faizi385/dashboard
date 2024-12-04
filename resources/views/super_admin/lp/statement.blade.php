@extends('layouts.admin')

@section('content')
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>
<div class="container p-2">
        <!-- Back Button -->

    <div class="d-flex justify-content-between mb-4">
        <h1 class="text-white" id="text">Supplier Statement</h1>
        <a href="{{ url()->previous() }}" class="btn btn-primary mb-3">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            @if(isset($lp))
                <h5 class="card-title">Statement for Supplier: {{ $lp->name }} ({{ $lp->dba }})</h5>
            @else
                <h5 class="card-title">Statement for All Suppliers</h5>
            @endif
        </div>
        <div class="card-body">
            <table id="lpStatementsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Supplier Organization Name</th>
                        <th>Name</th>
                        <th>Payout Without Tax</th>
                        <th>Payout With Tax</th>
                        <th>Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($lpStatement as $report)
                    @php
                        $date = $report->year .'-'.$report->month;
                    @endphp
                    <tr>
                        <td>{{ $lp->dba ?? 'N/A' }}</td>
                        <td>{{ $lp->name ?? 'N/A' }}</td>
                        <td style="text-align: center;">$ {{ number_format($report->total,2) }}</td>
                        <td style="text-align: center">$ {{ number_format($report->total_with_tax,2) }}</td>
                        <td>{{ \Carbon\carbon::parse($date)->format('M-Y') ?? 'NULL' }}</td>
                        <td class="text-center">
                            <a href="{{ route('lp.statement.export', ['lp_id' => $lp->id, 'date' => $lp->report_date ?? now()->format('Y-m-d')]) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Supplier Statement">
                                <i style="color: black" class="fas fa-file-download"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var lpId = {{$lp->id}};
        $("#loader").fadeOut("slow");
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        $('#lpStatementsTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: {
                emptyTable: "No Statement at the moment."
            },
            dom: '<"d-flex justify-content-between"lf>rtip',
            initComplete: function() {
                $('#loader').addClass('hidden');
                $("#lpStatementsTable_filter").prepend(`
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
                    const url = `{{ route('lp.statement.view', ':lpId') }}`.replace(':lpId', lpId);
                    if (selectedMonth) {
                        window.location.href = url + "?month=" + selectedMonth;
                    } else {
                        window.location.href = url;
                    }
                });
            }
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
    });
</script>
@endpush

@endsection
