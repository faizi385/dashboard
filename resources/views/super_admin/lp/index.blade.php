
@extends('layouts.app')

@section('content')
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>
<div class="container p-2">
    <h1 class="text-white">Supplier Management</h1>

    <div class="col text-end mb-3">
        <a href="{{ route('lp.create') }}" class="btn btn-primary">Create Supplier</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
            Add Deal
        </button>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Supplier</h5>
        </div>
        <div class="card-body">
            <table id="lpTable" class="table table-striped table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Organization Name</th>
                        <th>Primary Contact Email</th>
                        <th>Province</th> <!-- New Province Column -->
                        <th>Status</th>
                        <th>Approval</th> <!-- New Approval Column -->
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lps as $lp)
                        <tr>
                            <td>{{ $lp->name }}</td>
                            <td>{{ $lp->dba }}</td>
                            <td>{{ $lp->primary_contact_email }}</td>
                            <td>{{ $lp->address->first()?->province->name ?? '-' }}</td>


                            <td>{{ ucfirst($lp->status) }}</td>
                            <td class="text-center">
                                @if($lp->status !== 'approved')
                                <form action="{{ route('lp.updateStatus', $lp->id) }}" method="POST" class="d-inline" id="approveForm{{ $lp->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                     <button type="submit" class="d-none" id="approveButton{{ $lp->id }}"></button>
                                        <img
                                            src="{{ asset('check-mark.png') }}"
                                            alt="Approve"
                                            style="cursor: pointer; width: 24px; height: 24px;"
                                            onclick="document.getElementById('approveButton{{ $lp->id }}').click();"
                                        >
                                </form>
                                @endif

                                @if($lp->status !== 'rejected')
                                <form action="{{ route('lp.updateStatus', $lp->id) }}" method="POST" class="d-inline" id="rejectForm{{ $lp->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                     <button type="submit" class="d-none" id="approveButtonreject{{ $lp->id }}"></button>
                                        <img
                                            src="{{ asset('cross.png') }}"
                                            alt="Reject"
                                            style="cursor: pointer; width: 24px; height: 24px;"
                                            onclick="document.getElementById('approveButtonreject{{ $lp->id }}').click();"
                                        >
                                </form>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('lp.show', $lp->id) }}" class="icon-action text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="View Supplier">
                                    <i style="color: black" class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('lp.edit', $lp) }}" class="icon-action text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Supplier">
                                    <i style="color: black" class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('lp.destroy', $lp) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Supplier" style="color: inherit; text-decoration: none;">
                                        <i style="color: black" class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Add Offer Modal -->
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
                            <input type="hidden" name="source" value="1">

                            <!-- Supplier Selection -->
                            <div class="mb-3">
                                <label for="lpSelect" class="form-label">Select Supplier</label>
                                <select class="form-select" id="lpSelect" name="lp_id">
                                    <option value="" selected disabled>Select Supplier</option>
                                    @foreach($lps as $lp)
                                        <option value="{{ $lp->id }}">{{ $lp->name }} ({{ $lp->dba }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="lpSelect" class="form-label">Select Province</label>
                                <select class="form-select" id="lpSelect" name="province">
                                    <option value="" selected disabled>Select Province</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <!-- File Upload Section -->
                            <div class="mb-3">
                                <label for="offerExcel" class="form-label">Upload Bulk Deals (Excel)</label>
                                <input type="file" class="form-control" id="offerExcel" name="offerExcel" accept=".xlsx, .xls, .csv">
                            </div>
 <!-- Radio buttons for Month Selection -->
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

                    <!-- Single Offer Add Option -->
                    <div>
                        <a href="{{ route('offers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Single Deal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");

// Show the loader on form submission
$('form[action="{{ route('offers.import') }}"]').on('submit', function(e) {
    $("#loader").fadeIn("slow");
});

        // Initialize DataTable
        $('#lpTable').DataTable({
            "initComplete": function() {}
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Delete confirmation with SweetAlert
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Approve LP with SweetAlert
        $(document).on('submit', 'form[id^="approveForm"]', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to approve this Supplier?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(form).find('input[name="status"]').val('approved');
                    form.submit();
                }
            });
        });

        // Reject LP with SweetAlert
        $(document).on('submit', 'form[id^="rejectForm"]', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to reject this Supplier?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(form).find('input[name="status"]').val('rejected');
                    form.submit();
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
        const modalForm = document.querySelector('form[action="{{ route('offers.import') }}"]');
        const offerExcel = modalForm.querySelector('#offerExcel');

        modalForm.addEventListener('submit', function (e) {
            // Clear previous errors
            const errorContainer = offerExcel.nextElementSibling;
            if (errorContainer) {
                errorContainer.remove();
            }

            let isValid = true;

            // Validate the file input
            if (!offerExcel.value.trim()) {
                isValid = false;

                // Add error message
                const errorMessage = document.createElement('div');
                errorMessage.classList.add('text-danger', 'mt-1');
                errorMessage.textContent = 'Please upload a valid file.';
                offerExcel.parentNode.appendChild(errorMessage);
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Remove error dynamically when user interacts
        offerExcel.addEventListener('change', function () {
            const errorContainer = offerExcel.nextElementSibling;
            if (errorContainer) {
                errorContainer.remove();
            }
        });
    });
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
    document.addEventListener('DOMContentLoaded', function () {
        const modalForm = document.querySelector('form[action="{{ route('offers.import') }}"]');
        const lpSelect = modalForm.querySelector('#lpSelect');
        const offerExcel = modalForm.querySelector('#offerExcel');

        modalForm.addEventListener('submit', function (e) {
            // Clear previous errors
            [lpSelect, offerExcel].forEach((field) => {
                const errorContainer = field.nextElementSibling;
                if (errorContainer) {
                    errorContainer.remove();
                }
            });

            let isValid = true;

            // Validate the Supplier dropdown
            if (!lpSelect.value.trim()) {
                isValid = false;

                // Add error message
                const errorMessage = document.createElement('div');
                errorMessage.classList.add('text-danger', 'mt-1');
                errorMessage.textContent = 'Please select a supplier.';
                lpSelect.parentNode.appendChild(errorMessage);
            }

            // Validate the file input
            if (!offerExcel.value.trim()) {
                isValid = false;

                // Add error message
                const errorMessage = document.createElement('div');
                errorMessage.classList.add('text-danger', 'mt-1');
                errorMessage.textContent = 'Please upload a valid file.';
                offerExcel.parentNode.appendChild(errorMessage);
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Remove error dynamically when user interacts
        [lpSelect, offerExcel].forEach((field) => {
            field.addEventListener('change', function () {
                const errorContainer = field.nextElementSibling;
                if (errorContainer) {
                    errorContainer.remove();
                }
            });
        });
    });
</script>
@endpush



@endsection
