@extends('layouts.app')

@section('content')
<h1>Users</h1>
<div class="container bg-light">

    <div class="row mb-4">
        <div class="col text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">Add User</a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="example" class="table  table-hover ">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<!-- Custom CSS for alternating row colors and clearer borders -->
<style>
    table.dataTable {
        border-collapse: collapse !important;
    }
    table.dataTable thead th {
        background-color: #f8f9fa;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }
    table.dataTable tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    table.dataTable tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }
    table.dataTable tbody td {
        border: 1px solid #dee2e6;
    }
    table.dataTable tbody td, table.dataTable thead th {
        padding: 10px;
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

<script>
    $(document).ready(function() {
        $('#example').DataTable();

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
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit the form if confirmed
                    }
                });
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
