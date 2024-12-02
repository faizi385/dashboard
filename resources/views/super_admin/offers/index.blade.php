@extends('layouts.admin')

@section('content')

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">Deals List</h3>
        <a href="{{ route('lp.show', $lp->id) }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Deals for All Suppliers</h5>
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
<script>
    $(document).ready(function() {
        var lpId = {{$lp->id}};
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
                        window.location.href = "{{ route('all-offers.lp-wise') }}?lp_id="+lpId+"&month=" + selectedMonth;
                    } else {
                        window.location.href = "{{ route('all-offers.lp-wise') }}?lp_id="+lpId;
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
