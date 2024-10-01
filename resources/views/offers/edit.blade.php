@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h3>Edit Offer</h3>
    <form action="{{ route('offers.update', $offer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Province
                        </label>
                        <input type="text" class="form-control" id="province" name="province" value="{{ old('province', $offer->province) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="product_name" class="form-label">
                            <i class="fas fa-box"></i> Product
                        </label>
                        <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name', $offer->product_name) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">
                            <i class="fas fa-th-list"></i> Category
                        </label>
                        <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $offer->category) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="brand" class="form-label">
                            <i class="fas fa-tag"></i> Brand
                        </label>
                        <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand', $offer->brand) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="provincial_sku" class="form-label">
                            <i class="fas fa-barcode"></i> Provincial SKU
                        </label>
                        <input type="text" class="form-control" id="provincial_sku" name="provincial_sku" value="{{ old('provincial_sku', $offer->provincial_sku) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gtin" class="form-label">
                            <i class="fas fa-qrcode"></i> GTIN
                        </label>
                        <input type="text" class="form-control" id="gtin" name="gtin" value="{{ old('gtin', $offer->gtin) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="offer_start" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Start Date
                        </label>
                        <input type="date" class="form-control" id="offer_start" name="offer_start" value="{{ old('offer_start', \Carbon\Carbon::parse($offer->offer_start)->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="offer_end" class="form-label">
                            <i class="fas fa-calendar-times"></i> End Date
                        </label>
                        <input type="date" class="form-control" id="offer_end" name="offer_end" value="{{ old('offer_end', \Carbon\Carbon::parse($offer->offer_end)->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="data_fee" class="form-label">
                            <i class="fas fa-percent"></i> Data Fee (%)
                        </label>
                        <input type="number" class="form-control" id="data_fee" name="data_fee" value="{{ old('data_fee', $offer->data_fee) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="unit_cost" class="form-label">
                            <i class="fas fa-dollar-sign"></i> Cost ($)
                        </label>
                        <input type="number" class="form-control" id="unit_cost" name="unit_cost" value="{{ old('unit_cost', $offer->unit_cost) }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Offer</button>
            </div>
        </div>
    </form>
</div>
@endsection
