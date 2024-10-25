@extends('layouts.admin')

@section('content')
<div class="container p-2">
    <h3 class="text-white">Edit Carveout</h3>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Carveout for {{ $carveout->retailer->dba ?? 'N/A' }}</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('carveouts.update', $carveout->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="lp_id" value="{{ $carveout->lp_id }}"> <!-- Pass the LP ID here -->

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Province
                        </label>
                        <select class="form-control" id="province" name="province" required>
                            <option value="" disabled>Select Province</option>
                            <option value="Ontario" {{ $carveout->province == 'Ontario' ? 'selected' : '' }}>Ontario</option>
                            <option value="Alberta" {{ $carveout->province == 'Alberta' ? 'selected' : '' }}>Alberta</option>
                            <option value="British Columbia" {{ $carveout->province == 'British Columbia' ? 'selected' : '' }}>British Columbia</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="retailer" class="form-label">
                            <i class="fas fa-store"></i> Retailer
                        </label>
                        <select class="form-control" id="retailer" name="retailer" required>
                            <option value="" disabled>Select Retailer</option>
                            @foreach($retailers as $retailer)
                                <option value="{{ $retailer->id }}" {{ $carveout->retailer_id == $retailer->id ? 'selected' : '' }}>
                                    {{ $retailer->dba }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">
                            <i class="fas fa-map-marker"></i> Location
                        </label>
                        <input type="text" class="form-control" id="location" name="location" value="{{ $carveout->location }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="sku" class="form-label">
                            <i class="fas fa-barcode"></i> SKU
                        </label>
                        <input type="text" class="form-control" id="sku" name="sku" value="{{ $carveout->sku }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="carveout_date" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Carveout Date
                    </label>
                    <input type="date" class="form-control" id="carveout_date" name="carveout_date" value="{{ \Carbon\Carbon::parse($carveout->date)->format('Y-m-d') }}" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('carveouts.index', ['lp_id' => $carveout->lp_id]) }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Carveout</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
