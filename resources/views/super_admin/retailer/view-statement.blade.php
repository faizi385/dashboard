@extends('layouts.admin')

@section('content')
<div class="container p-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white" id="text">Retailer Statement</h1>
    <a href="{{ url()->previous() }}" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    </div>
    <div class="row">
        <div class="col">
            <table id="statementsTable" class="table table-hover table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th>Retailer DBA</th>
                        <th>LP DBA</th>
                        <th>Payout Without Tax</th>
                        <th>Payout With Tax</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($statements->isNotEmpty())
                        @php
                            // Tax rates for provinces
                            $taxRates = [
                                'Alberta' => 0.05,
                                'Ontario' => 0.03,
                                'Manitoba' => 0.05,
                                'British Columbia' => 0.05,
                                'Saskatchewan' => 0.05
                            ];
                            
                            // Group statements by province
                            $groupedByProvince = $statements->groupBy('province');
                        @endphp

                        @foreach($groupedByProvince as $province => $group)
                            @php
                                // Initialize sums for each province
                                $totalFeeWithoutTax = 0;
                                $totalTaxWithTax = 0;

                                // Calculate totals for the group (province)
                                foreach($group as $statement) {
                                    $totalFeeWithoutTax += $statement->total_fee;
                                    $taxRate = $taxRates[$statement->province] ?? 0;
                                    $taxAmount = $statement->total_fee * $taxRate;
                                    $totalTaxWithTax += $taxAmount;
                                }
                            @endphp

                            <tr>
                                <td>{{ $province ?? 'N/A' }}</td>
                                <td>{{ $retailer->dba ?? 'N/A' }}</td>
                                <td>{{ $group->first()->lp->dba ?? 'N/A' }}</td>
                                <td>{{ number_format($totalFeeWithoutTax, 2) }}</td>
                                <td>{{ number_format($totalFeeWithoutTax + $totalTaxWithTax, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.exportStatement', $reportId) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Retailer Statement">
                                        <i style="color: black" class="fas fa-file-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">No statements found for this retailer.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
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
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{
    color: white  !important;
}
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize DataTable
        $('#statementsTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: { emptyTable: "No statements found." }
        });
    });
</script>
@endpush

@endsection
