@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Provinces</h1>
        <a href="{{ route('provinces.create') }}" class="btn btn-primary">Create Province</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="provincesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Timezone 1</th>
                <th>Timezone 2</th>
                <th>Tax Value</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($provinces as $province)
                <tr data-id="{{ $province->id }}">
                    <td>{{ $province->name }}</td>
                    <td>{{ $province->slug }}</td>
                    <td>{{ $province->timezone_1 }}</td>
                    <td>{{ $province->timezone_2 }}</td>
                    <td>{{ $province->tax_value }}</td>
                    <td class="status-text">
                        {{ $province->status ? 'Active' : 'Inactive' }}
                    </td>
                    <td class="text-center">
                        <div class="action-icons">
                            <!-- Status Toggle Icon -->
                            <button class="btn status-toggle p-0" data-id="{{ $province->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Toggle Status">
                                <i class="fas fa-toggle-{{ $province->status ? 'on' : 'off' }}"></i>
                            </button>
                            <!-- Edit Icon -->
                            <a href="{{ route('provinces.edit', $province) }}" class="text-warning mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Province">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Delete Icon -->
                            <form action="{{ route('provinces.destroy', $province) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Province">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- FontAwesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .action-icons {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .action-icons .btn, .action-icons a {
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: none;
    }
    .action-icons i {
        font-size: 1.25rem; /* Adjust icon size as needed */
        margin: 0 8px; /* Add spacing between icons */
    }
    .status-toggle {
        text-decoration: none; /* Remove underline from toggle button */
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#provincesTable').DataTable();

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize delete confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete It!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit the form if confirmed
                    }
                });
            });
        });

        // Handle status toggle with SweetAlert2 confirmation
        $('.status-toggle').click(function() {
            let row = $(this).closest('tr');
            let provinceId = $(this).data('id');
            let statusIcon = $(this).find('i');
            let statusText = row.find('.status-text');
            let isActive = statusIcon.hasClass('fa-toggle-on');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${isActive ? 'deactivate' : 'activate'} this province?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update It!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('provinces/status') }}/" + provinceId,
                        method: 'PATCH',
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: isActive ? 0 : 1
                        },
                        success: function(response) {
                            toastr.success('Status updated successfully.');
                            // Toggle icon
                            statusIcon.toggleClass('fa-toggle-on fa-toggle-off');
                            // Update status text
                            statusText.text(isActive ? 'Inactive' : 'Active');
                        },
                        error: function() {
                            toastr.error('Failed to update status.');
                        }
                    });
                }
            });
        });

        // Display Toastr messages
        @if(session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
    });
</script>
@endpush
@endsection
