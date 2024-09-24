@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle"></i> Add Single Offer</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('offers.store') }}" method="POST">
            @csrf
            
            <div class="mb-3 col-md-6">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" name="product_name" id="product_name" class="form-control @error('product_name') is-invalid @enderror" required>
                @error('product_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="provincial_sku" class="form-label">Provincial SKU</label>
                <input type="text" name="provincial_sku" id="provincial_sku" class="form-control @error('provincial_sku') is-invalid @enderror">
                @error('provincial_sku')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="gtin" class="form-label">GTIN</label>
                <input type="text" name="gtin" id="gtin" class="form-control @error('gtin') is-invalid @enderror">
                @error('gtin')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="province" class="form-label">Province</label>
                <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror">
                @error('province')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="datafee" class="form-label">Data Fee (%)</label>
                <input type="number" name="datafee" id="datafee" class="form-control @error('datafee') is-invalid @enderror" step="0.01">
                @error('datafee')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="unit_cost" class="form-label">Unit Cost (Excl. HST)</label>
                <input type="number" name="unit_cost" id="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror" step="0.01">
                @error('unit_cost')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="category" class="form-label">Category</label>
                <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                @error('category')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror">
                @error('brand')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="lp" class="form-label">LP</label>
                <input type="text" name="lp" id="lp" class="form-control @error('lp') is-invalid @enderror">
                @error('lp')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="case_quantity" class="form-label">Case Quantity (Units per Case)</label>
                <input type="number" name="case_quantity" id="case_quantity" class="form-control @error('case_quantity') is-invalid @enderror">
                @error('case_quantity')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="offer_start" class="form-label">Offer Start</label>
                <input type="date" name="offer_start" id="offer_start" class="form-control @error('offer_start') is-invalid @enderror" required>
                @error('offer_start')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="offer_end" class="form-label">Offer End</label>
                <input type="date" name="offer_end" id="offer_end" class="form-control @error('offer_end') is-invalid @enderror" required>
                @error('offer_end')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="product_size" class="form-label">Product Size (g)</label>
                <input type="number" name="product_size" id="product_size" class="form-control @error('product_size') is-invalid @enderror" step="0.01">
                @error('product_size')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="thc_range" class="form-label">THC % Range</label>
                <input type="text" name="thc_range" id="thc_range" class="form-control @error('thc_range') is-invalid @enderror">
                @error('thc_range')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="cbd_range" class="form-label">CBD % Range</label>
                <input type="text" name="cbd_range" id="cbd_range" class="form-control @error('cbd_range') is-invalid @enderror">
                @error('cbd_range')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="comment" class="form-label">Comment</label>
                <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="3"></textarea>
                @error('comment')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="link_info" class="form-label">Link to Product and Brand Info</label>
                <input type="url" name="link_info" id="link_info" class="form-control @error('link_info') is-invalid @enderror">
                @error('link_info')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Add Offer</button>
        </form>
    </div>
</div>
@endsection
