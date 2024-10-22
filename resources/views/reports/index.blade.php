@extends('layouts.admin')

@section('content')
<h1 class="text-white" id="text">Reports</h1>

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-3">

    <div class="row mb-4">
        <div class="col text-end">
            {{-- <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addReportModal">
                Add Report
            </button> --}}
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="reportsTable" class="table table-hover table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th class="">Retailer DBA</th>
                        <th class="">Location</th>
                        <th class="">POS</th>
                        <th class="">Status</th>
                        <th>File 1</th>
                        <th>File 2</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th> <!-- Centered Actions header -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td class="">{{ $report->retailer->dba ?? 'N/A' }}</td>
                        <td class="">{{ $report->location }}</td>
                        <td class="">{{ $report->pos }}</td>
                        <td class="">{{ $report->status }}</td>
                        <td><a href="{{ asset('storage/' . $report->file_1) }}">Download</a></td>
                        <td><a href="{{ asset('storage/' . $report->file_2) }}">Download</a></td>
                        <td>{{ \Carbon\Carbon::parse($report->date)->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <!-- Actions for edit and delete -->
                            <a href="#" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Report">
                                <i style="color: black" class="fas fa-edit"></i>
                            </a>
                            <form action="#" method="POST" style="display:inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link p-0 delete-report" data-id="{{ $report->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Report">
                                    <i style="color: black" class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <!-- Remove this line -->
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

/* Adjust download link color */
a {
    color: #007bff; /* Customize link color */
}

/* Set the background for alternating rows */
table.dataTable tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

table.dataTable tbody tr:nth-child(even) {
    background-color: #fff;
}

/* Loader */
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: rgba(255, 255, 255, 0.8);
}

.loader {
    position: absolute;
    top: 50%;
    left: 50%;
    border: 16px solid #f3f3f3;
    border-top: 16px solid #3498db;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

</style>
@push('scripts')
<script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");

        // Initialize DataTable
        var table = $('#reportsTable').DataTable({
            responsive: true,
         
          
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Function to handle delete confirmation with SweetAlert
        function initializeDeleteConfirmation() {
            document.querySelectorAll('.delete-report').forEach(button => {
                button.addEventListener('click', function() {
                    const deleteForm = this.closest('form');
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

        // Reinitialize the SweetAlert delete confirmation when DataTable is redrawn (pagination, search, etc.)
        table.on('draw', function() {
            initializeDeleteConfirmation();
        });

        // Display Toastr messages
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush

@endsection
