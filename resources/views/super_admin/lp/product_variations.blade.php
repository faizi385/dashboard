@extends('layouts.admin')

@section('content')

<!-- Loader -->
<div id="loader" class="loader-overlay">
    <div class="loader"></div>
</div>

<div class="container p-2">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-white">Product Variations</h3>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Product Variations for GTIN: {{ $gtin }}</h5> <!-- Display GTIN in header -->
        </div>

        <div class="card-body">
            <table id="productsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Supplier Organization Name</th>
                        <th>Province</th>
                        <th>Product</th>
                        <th>Provincial SKU</th>
                        <th>GTIN</th>
                        <th>Category</th>
                        <th>Brand</th>
                         <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->lp->dba ?? 'N/A' }}</td> 
                        <td>{{ $product->province }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->provincial_sku }}</td>
                        <td>{{ $product->gtin }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->brand }}</td>
                        <td class="text-center">
                            <a href="{{ route('product_variation.edit', $product->id) }}" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Product Variation">
                                <i class="fas fa-edit" style="color: black;"></i> <!-- Yellow edit icon -->
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Hide the loader when the page is fully loaded
        $("#loader").fadeOut("slow");

        // Initialize DataTable with scroll and responsiveness
        $('#productsTable').DataTable({
            responsive: true,
            "scrollX": true,
            autoWidth: false, 
            "language": {
                "emptyTable": "No product variations found for this GTIN."
            },
            "initComplete": function() {
                $('#loader').addClass('hidden'); // Hide the loader when DataTable is initialized
            }
        });
    });
</script>

<style>
    .container {
        margin-top: 20px;
    }

   

    .table th, .table td {
    padding: 0.75rem; /* Adjust padding for a balanced layout */
    vertical-align: middle; /* Center content vertically */
    white-space: nowrap; /* Prevent text from wrapping */
    overflow: hidden; /* Hide any overflowing text */
    text-overflow: ellipsis; /* Show ellipsis for any overflow */
}

.table {
    width: 100%; /* Ensure the table takes full width */
    table-layout: auto; /* Allow table columns to adjust to content */
}

.dataTables_wrapper {
    width: 100%; /* Make sure DataTables is full width */
    margin: 0 auto; /* Center DataTables */
}


    
</style>

@endsection
