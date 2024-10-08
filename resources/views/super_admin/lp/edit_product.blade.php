@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Product</h3>
    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Use PUT for update requests -->
        
        <!-- Add your form fields here, for example: -->
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" class="form-control" name="product_name" id="product_name" value="{{ $product->product_name }}" required>
        </div>
        
        <!-- Add other fields as necessary -->
        
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
@endsection
