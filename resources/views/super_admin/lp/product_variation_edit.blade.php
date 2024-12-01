@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Edit Product Variation</h1>
        <a href="{{ route('products.variations',[$lpID,$gtin]) }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('variation.edit', $productVariation->id) }}" method="POST" id="productForm">
            @csrf

            <div class="row">
                <!-- Product Name Field -->
                <div class="col-md-6 mb-3">
                    <label for="product_name" class="form-label">
                        <i class="fas fa-box"></i> Product Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="product_name" id="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Enter Product Name" value="{{ old('product_name', $productVariation->product_name ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <select class="form-control" id="province" name="province" {{ $productVariation->is_validate == 1 ? 'checked disabled' : '' }}>
                            <option value="" selected disabled>Select Province</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ old('province', $productVariation->province_id) == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
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
                    <input type="text" name="provincial_sku" id="provincial_sku" class="form-control @error('provincial_sku') is-invalid @enderror" placeholder="Enter Provincial SKU" value="{{ old('provincial_sku', $productVariation->provincial_sku ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="text" name="gtin" id="gtin" class="form-control @error('gtin') is-invalid @enderror" placeholder="Enter GTIN" value="{{ old('gtin', $productVariation->gtin ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" placeholder="Enter Category" value="{{ old('category', $productVariation->category ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" placeholder="Enter Brand" value="{{ old('brand', $productVariation->brand ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="number" name="case_quantity" id="category" class="form-control @error('case_quantity') is-invalid @enderror" placeholder="Enter Case Quantity" value="{{ old('case_quantity', $productVariation->case_quantity ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="text" name="product_size" id="product_size" class="form-control @error('product_size') is-invalid @enderror" placeholder="Enter Product Size" value="{{ old('product_size', $productVariation->product_size ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="text" name="thc_range" id="thc_range" class="form-control @error('thc_range') is-invalid @enderror" placeholder="Enter Case Quantity" value="{{ old('thc_range', $productVariation->thc_range ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
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
                    <input type="text" name="cbd_range" id="cbd_range" class="form-control @error('cbd_range') is-invalid @enderror" placeholder="Enter Product Size" value="{{ old('cbd_range', $productVariation->cbd_range ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
                    @error('cbd_range')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">
                        <i class="fas fa-tag"></i> Price Per Unit <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="price_per_unit" class="form-control" placeholder="Enter Price Per Unit" value="{{ old('price_per_unit', $productVariation->price_per_unit ?? '') }}" oninput="removeValidation(this)" {{ $productVariation->is_validate == 1 ? 'readonly' : '' }}>
                    @error('price_per_unit')
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
                    {{ $productVariation->is_validate == 1 ? 'checked disabled' : '' }}
                >
                <label class="form-check-label" for="isValidateCheckbox">Validate</label>
            </div>
        </div>
            
           <button type="submit" class="btn btn-primary" {{ $productVariation->is_validate == 1 ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
</div>

@endsection
