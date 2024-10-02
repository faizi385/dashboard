@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Products</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>LP ID</th>
                <th>Province</th>
                <th>Product</th>
                <th>Provincial SKU</th>
                <th>GTIN</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Date</th>
                <th>Data Fee (%)</th>
                <th>Cost ($)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->lp_id }}</td>
                <td>{{ $product->province }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->provincial_sku }}</td>
                <td>{{ $product->gtin }}</td>
                <td>{{ $product->category }}</td>
                <td>{{ $product->brand }}</td>
                <td>{{ $product->offer_date ? $product->offer_date->format('d-M-Y') : 'N/A' }}</td>
                <td>{{ $product->general_data_fee }}</td>
                <td>{{ $product->unit_cost }}</td>
                <td>
                    <!-- Add action buttons if needed, e.g., View, Edit, Delete -->
                    {{-- <a href="{{ route('offer.view', $product->id) }}" class="btn btn-info">View</a> --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
