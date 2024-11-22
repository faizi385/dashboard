@extends('layouts.admin')

@section('content')

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-2">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Products Family</h3>
        <div>
            @if(isset($lp)) 
            <a href="{{ url()->previous() }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Products for Supplier </h5>
        </div>
        <div class="card-body">
            <table id="productsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Supplier DBA</th>
                        <th>Province</th>
                        <th>Product</th>
                        <th>Provincial SKU</th>
                        <th>GTIN</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Action</th> <!-- Ensure this column is included -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->lp->dba ?? 'N/A' }}</td> <!-- Use the DBA name -->
                        <td>{{ $product->province }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->provincial_sku }}</td>
                        <td>
                            <a href="{{ route('products.variations', ['lp_id' => $product->lp_id, 'gtin' => $product->gtin]) }}">
                                {{ $product->gtin }}
                            </a>
                        </td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->brand }}</td>
                        <td class="text-center">
                            <!-- Edit Icon -->
                            <a href="{{ route('products.edit', $product->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Product">
                                <i class="fas fa-edit" style="color: black;"></i> <!-- Yellow edit icon -->
                            </a>
                            
                            <!-- Delete Icon -->
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="delete-form" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link p-0 delete-btn" data-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Product">
                                    <i class="fas fa-trash" style="color: black;"></i> <!-- Red trash icon -->
                                </button>
                            </form>
                        </td>
                        
                    </tr>
                    @empty
                    <!-- Handle case when no products are available -->
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include SweetAlert and DataTables -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

  <script>
    $(document).ready(function() {
        $("#loader").fadeOut("slow");
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize DataTable
        var table = $('#productsTable').DataTable({
            responsive: true,
            scrollX: true, 
            autoWidth: false, 
            language: {
                emptyTable: "No products found." // Custom message for no data
            },
            dom: '<"d-flex justify-content-between"lf>rtip',
            initComplete: function() {
                $('#loader').addClass('hidden'); // Hide loader once table is initialized

                // Add "Filter" label and month filter input next to the search box
                $("#productsTable_filter").prepend(`
                    <span class="me-2 " style="font-weight: bold;">Filter:</span>
                    <label class="me-3">
                       
                        <input type="month" id="monthFilter" class="form-control form-control-sm" placeholder="Select month" />
                    </label>
                    
                `);

                // Month filter functionality to filter table by selected month
                $('#monthFilter').on('change', function() {
                    const selectedMonth = $(this).val();
                    if (selectedMonth) {
                        table.column(8).search(selectedMonth).draw(); 
                    } else {
                        table.column(8).search('').draw();
                    }
                });
            }
        });

        // SweetAlert for Delete Confirmation
        $('.delete-btn').on('click', function() {
            var form = $(this).closest('form'); // Get the form

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
</script>

<style>
    .container {
        margin-top: 20px;
    }

   
    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table th {
        font-size: 0.85rem;
        padding: 0.75rem;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }
    .dataTables_wrapper .dataTables_filter label{
        color: black
    }
</style>
@endsection
