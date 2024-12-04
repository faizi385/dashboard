@extends('layouts.admin')

@section('content')
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>
<div class="container p-2">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Carveout List</h3>
        <div>

            @if(isset($lp)) <!-- Check if $lp is set -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarveoutModal">
                Add Carveout
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
            @if(isset($lp)) <!-- Check if $lp is set -->
            <div class="d-flex justify-content-between">
                <h5 class="card-title">Carveouts for Supplier: {{ $lp->name }} ({{ $lp->dba }})</h5>
                {{-- <button class="btn btn-info" onclick="window.location.href='{{ route('offers.index', ['lp_id' => $lp->id]) }}'">
                    View Offers
                </button> --}}
            </div>
            @else
                <h5 class="card-title">Carveouts for All Suppliers</h5> <!-- Fallback when $lp is not available -->
            @endif
        </div>

        <div class="card-body">
            <table id="carveoutTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Organization Name</th>
                        {{-- <th>Address</th>
                        <th>Carveout</th> --}}
                        <th>Location</th>
                        <th>SKU</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($carveouts) && $carveouts->count() > 0)
                        @foreach($carveouts as $carveout)
                        <tr>
                            <td>{{ $carveout->retailer->dba ?? 'N/A' }}</td> <!-- Display retailer's DBA -->
                            {{-- <td>{{ $carveout->address ?? '-'}}</td>
                            <td>{{ $carveout->carveout ?? '-' }}</td> --}}
                            <td>{{ $carveout->retailerAddress->location  }}</td>
                            <td>{{ $carveout->sku ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($carveout->date)->format('Y-m-d') }}</td>
                            <td>{{ $carveout->lp->dba ?? 'N/A' }}</td> <!-- Display LP's DBA -->
                            <td class="text-center">
                                <!-- Edit Carveout Icon -->
                                {{-- <a href="{{ route('carveouts.edit', $carveout->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Carveout">
                                    <i style="color: black" class="fas fa-edit "></i> <!-- Edit Icon -->
                                </a> --}}

                                <!-- Delete Carveout Icon -->
                                <form action="{{ route('carveouts.destroy', $carveout->id) }}" method="POST" style="display:inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn delete-btn btn-link p-0" data-id="{{ $carveout->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Carveout">
                                        <i style="color: black" class="fas fa-trash "></i> <!-- Delete Icon -->
                                    </button>
                                </form>
                            </td>


                        </tr>
                        @endforeach
                    @else
                       {{-- <tr>
                          <td colspan="8" class="text-center">No carveouts found.</td>
                       </tr> --}}
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Carveout Modal -->
<div class="modal fade" id="addCarveoutModal" tabindex="-1" aria-labelledby="addCarveoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('carveouts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCarveoutModalLabel">Add Carveout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lp_id" value="{{ $lp_id }}"> <!-- Pass the LP ID here -->

                    <div class="mb-3">
                        <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                        <select class="form-control" id="province" name="province">
                            <option value="" disabled selected>Select Province</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="retailer" class="form-label">Distributor <span class="text-danger">*</span></label>
                        <select class="form-control" id="retailer" name="retailer" >
                            <option value="" disabled selected>Select Distributor</option>
                            @foreach($retailers as $retailer)
                                <option value="{{ $retailer->id }}">{{ $retailer->dba }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location </label>
                        <select class="form-control" id="location" name="location" >
                            <option value="" disabled selected>Select Location</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" class="form-control" id="sku" name="sku">
                    </div>

                    <div class="mb-3">
                        <label for="carveout_date" class="form-label">Carveout Date <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="carveout_date" name="carveout_date" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Carveout</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Include DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");


    var table = $('#carveoutTable').DataTable({
        responsive: true,
        scrollX: true,
        autoWidth: false,
        language: {
            emptyTable: "No carveouts found."
        },
        dom: '<"d-flex justify-content-between"lf>rtip',
        initComplete: function() {
            $('#loader').addClass('hidden'); // Hide loader once table is initialized

            {{--$("#carveoutsTable_filter").prepend(`--}}
            {{--        <span class="me-2 " style="font-weight: bold;">Filter:</span>--}}
            {{--        <label class="me-3">--}}
            {{--            <div class="input-group date">--}}
            {{--                <input type="text" class="form-control" id="calendarFilter" placeholder="Select a date" value="{{ \Carbon\Carbon::parse($date)->format('F-Y') }}">--}}
            {{--                <div class="input-group-append">--}}
            {{--                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--        </label>--}}
            {{--    `);--}}
            // $('#calendarFilter').on('change', function() {
            //     const selectedMonth = $(this).val();
                {{--if (selectedMonth) {--}}
                {{--    window.location.href = "{{ route('offers.index') }}?month=" + selectedMonth;--}}
                {{--} else {--}}
                {{--    window.location.href = "{{ route('offers.index') }}";--}}
                {{--}--}}
            // });
        }
    });

        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Handle delete button click using event delegation
        $('#carveoutTable tbody').on('click', '.delete-btn', function (event) {
            event.preventDefault(); // Prevent form submission
            const form = $(this).closest('form'); // Get the parent form

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
                    form.submit(); // Submit the form if confirmed
                }
            });
        });

        // Clear form data when modal is closed
        $('#addCarveoutModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset(); // Reset the form
            $(this).find('.form-control').removeClass('is-invalid'); // Remove any validation classes if present
        });

        // Display errors with toastr if there are any
        @if ($errors->any())
            toastr.error("{{ $errors->first() }}");
        @endif
    });

    document.addEventListener('DOMContentLoaded', function () {
        const retailerSelect = document.getElementById('retailer');
        const locationSelect = document.getElementById('location');

        retailerSelect.addEventListener('change', function () {
            const retailerId = this.value;

            // Clear existing options
            locationSelect.innerHTML = '<option value="" disabled selected>Select Location</option>';

            if (retailerId) {
                fetch(`/retailers/${retailerId}/addresses`) // Update this URL according to your routes
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(address => {
                            // Format the address as desired
                            const formattedAddress = `${address.street_no} ${address.street_name}, ${address.province}, ${address.city}, ${address.location}`;

                            const option = document.createElement('option');
                            option.value = address.id; // Assuming the address has an ID
                            option.textContent = formattedAddress; // Set the formatted address as the option text
                            option.style.color = "black"; // Set the text color to black
                            locationSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching addresses:', error));
            }
        });
    });
    </script>


<style>


</style>
@endsection
