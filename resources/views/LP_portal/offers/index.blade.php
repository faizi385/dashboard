@extends('layouts.admin')

@section('content')

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">Deals List</h3>
        <div class="d-flex">
            @if(auth()->user()->hasRole('LP'))
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                    <i class="fas fa-upload"></i> Upload Offer
                </button>
            @endif
            @if(isset($lp))
            <a href="{{ url()->previous() }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            @if(isset($lp))
                <h5 class="card-title">Deals for Supplier: {{ $lp->name }} ({{ $lp->dba }})</h5>
            @else
                <h5 class="card-title">Deals for All Suppliers</h5>
            @endif
        </div>
        <div class="card-body">
            <table id="offersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Supplier Organization Name</th>
                        <th>Province</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Provincial SKU</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Date</th>
                        <th>GTIN</th>
                        <th>Data Fee (%)</th>
                        <th>Cost ($)</th>
                        <th>Exclusive</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                    <tr>
                        <td>{{ $offer->lp->dba ?? 'N/A' }}</td>
                        <td>{{ $offer->province }}</td>
                        <td>{{ $offer->product_name }}</td>
                        <td>{{ $offer->category }}</td>
                        <td>{{ $offer->brand }}</td>
                        <td>{{ $offer->provincial_sku }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->offer_start)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->offer_end)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->offer_date)->format('Y-m-d') }}</td>
                        <td>{{ $offer->gtin }}</td>
                        <td>{{ $offer->data_fee }}</td>
                        <td>{{ $offer->unit_cost }}</td>
                        <td>{{ $offer->retailer ? $offer->retailer->first_name . ' ' . $offer->retailer->last_name : 'ALL' }}</td>
                        <td class="text-center">
                            <a href="{{ route('offers.edit', $offer->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Offer">
                                <i style="color: black" class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('offers.destroy', $offer->id) }}" method="POST" style="display:inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link p-0 delete-offer" data-id="{{ $offer->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Offer">
                                    <i style="color: black" class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>
<div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOfferModalLabel">Add Deals</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <form action="{{ route('offers.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="lp_id" value="{{ $lp->id ?? null}}">
                            <input type="hidden" name="source" value="1">
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <p><strong>{{ $lp->name ?? null}} ({{ $lp->dba ?? null }})</strong></p>
                            </div>
                            <div class="mb-3">
                                <label for="lpSelect" class="form-label">Select Province</label>
                                <select class="form-select"  name="province">
                                    <option value="" selected disabled>Select Province</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="offerExcel" class="form-label">Upload Bulk Deals (Excel)</label>
                                <input type="file" class="form-control" id="offerExcel" name="offerExcel" accept=".xlsx, .xls, .csv">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Month</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="month" id="currentMonth" value="current" checked>
                                        <label class="form-check-label" for="currentMonth">
                                            Current Month
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="month" id="nextMonth" value="next">
                                        <label class="form-check-label" for="nextMonth">
                                            Next Month
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Excel
                            </button>
                        </form>
                    </div>
                    <div>
                        <a href="{{ route('offers.create', ['lp_id' => $lp->id ?? null]) }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Single Deal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        var table = $('#offersTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: {
                emptyTable: "No offers found."
            },
            dom: '<"d-flex justify-content-between"lf>rtip',
            initComplete: function() {
                $('#loader').addClass('hidden');
                $("#offersTable_filter").prepend(`
                  <span class="me-2 " style="font-weight: bold;">Filter:</span>
                    <label class="me-3">

                        <input type="month" id="monthFilter" class="form-control form-control-sm" placeholder="Select month" />
                    </label>
                `);
                $('#monthFilter').on('change', function() {
                    const selectedMonth = $(this).val();
                    if (selectedMonth) {
                        table.column(8).search(selectedMonth).draw(); // Assumes 'Date' is column index 8
                    } else {
                        table.column(8).search('').draw();
                    }
                });
            }
        });
        $(document).on('click', '.delete-offer', function() {
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
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>

<style>
    .container {
        margin-top: 20px;
    }
    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: black;
    }
</style>
@endsection
