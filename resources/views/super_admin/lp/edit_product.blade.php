@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h1>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST" id="productForm">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <div class="row">
                <!-- Product Name Field -->
                <div class="col-md-6 mb-3">
                    <label for="product_name" class="form-label">
                        <i class="fas fa-box"></i> Product Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="product_name" id="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Enter Product Name" value="{{ old('product_name', $product->product_name ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('product_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Province Field -->
                <div class="col-md-6 mb-3">
                    <label for="province" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Province <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" placeholder="Enter Province" value="{{ old('province', $product->province ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('province')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Provincial SKU Field -->
                <div class="col-md-6 mb-3">
                    <label for="provincial_sku" class="form-label">
                        <i class="fas fa-barcode"></i> Provincial SKU <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="provincial_sku" id="provincial_sku" class="form-control @error('provincial_sku') is-invalid @enderror" placeholder="Enter Provincial SKU" value="{{ old('provincial_sku', $product->provincial_sku ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('provincial_sku')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- GTIN Field -->
                <div class="col-md-6 mb-3">
                    <label for="gtin" class="form-label">
                        <i class="fas fa-barcode"></i> GTIN <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="gtin" id="gtin" class="form-control @error('gtin') is-invalid @enderror" placeholder="Enter GTIN" value="{{ old('gtin', $product->gtin ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('gtin')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Category Field -->
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">
                        <i class="fas fa-list"></i> Category <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" placeholder="Enter Category" value="{{ old('category', $product->category ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('category')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Brand Field -->
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">
                        <i class="fas fa-tag"></i> Brand <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" placeholder="Enter Brand" value="{{ old('brand', $product->brand ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('brand')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <!-- Case Quantity Field -->
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">
                        <i class="fas fa-list"></i> Case Quantity <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="case_quantity" id="category" class="form-control @error('case_quantity') is-invalid @enderror" placeholder="Enter Case Quantity" value="{{ old('case_quantity', $product->case_quantity ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('case_quantity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Brand Field -->
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">
                        <i class="fas fa-tag"></i> Product Size <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="product_size" id="product_size" class="form-control @error('product_size') is-invalid @enderror" placeholder="Enter Product Size" value="{{ old('product_size', $product->product_size ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('product_size')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <!-- thc_range Field -->
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">
                        <i class="fas fa-list"></i> THC Range <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="thc_range" id="thc_range" class="form-control @error('thc_range') is-invalid @enderror" placeholder="Enter Case Quantity" value="{{ old('thc_range', $product->thc_range ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('thc_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- cbd_range Field -->
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">
                        <i class="fas fa-tag"></i> CBD Range <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="cbd_range" id="cbd_range" class="form-control @error('cbd_range') is-invalid @enderror" placeholder="Enter Product Size" value="{{ old('cbd_range', $product->cbd_range ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('cbd_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row" >
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">
                        <i class="fas fa-tag"></i> Unit Cost <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="unit_cost" class="form-control" placeholder="Enter Unit Cost" value="{{ old('unit_cost', $product->unit_cost ?? '') }}" oninput="removeValidation(this)" {{ $product->is_validate == 1 ? 'readonly' : '' }}>
                    @error('unit_cost')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
          <div class="col-md-6 mb-3">
            <div class="form-check">
                <input 
                    type="checkbox" 
                    name="is_validate" 
                    class="form-check-input" 
                    id="isValidateCheckbox" 
                    {{ $product->is_validate == 1 ? 'checked disabled' : '' }}
                >
                <label class="form-check-label" for="isValidateCheckbox">Validate</label>
            </div>
        </div>
            
           <button type="submit" class="btn btn-primary" {{ $product->is_validate == 1 ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
        </form>
    </div>
</div>

@endsection
