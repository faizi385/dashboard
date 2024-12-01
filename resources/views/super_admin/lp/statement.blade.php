@extends('layouts.admin')

@section('content')
<div class="container p-3">
    <!-- Back Button -->

    <div class="d-flex justify-content-between mb-4">
    <h1 class="text-white" id="text">Supplier Statement</h1>
    <a href="{{ url()->previous() }}" class="btn btn-primary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
    <div class="row">
        <div class="col">
            <table id="lpStatementsTable" class="table table-hover table-bordered text-center align-middle">
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

            <!-- Display total fees below the table -->
            {{-- <div class="row mt-3">
                <div class="col text-end">
                    <strong>Total Payout Without Tax:</strong> ${{ number_format($totalFeeSum, 2) }} <br>
                    <strong>Total Payout With Tax:</strong> ${{ number_format($totalFeeWithTaxSum, 2) }}
                </div>
            </div> --}}
        </div>
    </div>
</div>

<style>
    /* Styling similar to the Reports table */
    table.dataTable {
        width: 100%;
        background-color: white;
        border-collapse: collapse;
    }
    table.dataTable th, table.dataTable td {
        padding: 12px;
        border: 1px solid #ddd;
    }
    table.dataTable thead th {
        background-color: #f5f5f5;
        color: #333;
        text-align: center;
    }
    table.dataTable tbody td {
        text-align: center;
        vertical-align: middle;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: white !important;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize DataTable
        $('#lpStatementsTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: { emptyTable: "" }
        });
    });
</script>
@endpush

@endsection
