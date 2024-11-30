@extends('layouts.app')

@section('content')
<div class="container p-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Supplier Info</h1>
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow-sm">
        <form action="{{ route('manage-info.update') }}" method="POST">
            @csrf

            <!-- LP Information Fields -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Name <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $lp->name) }}" 
                        oninput="removeValidation(this)"
                    >
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="dba" class="form-label">
                        <i class="fas fa-tag"></i> Organization Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="dba" id="dba" class="form-control @error('dba') is-invalid @enderror" placeholder="Enter Organization Name" readonly value="{{ old('dba', $lp->dba ?? '') }}" >
                    @error('dba')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        value="{{ old('email', $lp->primary_contact_email) }}" 
                        oninput="removeValidation(this)"
                        readonly
                    >
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone"></i> Phone
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        value="{{ old('phone', $lp->primary_contact_phone) }}" 
                        oninput="removeValidation(this)"
                    >
                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <h2 class="mt-4">Address</h2>

            @forelse($lp->address as $address)
                <div class="row mb-3">

                    <div class="col-md-4">
                        <label for="street_name_{{ $address->id }}" class="form-label">Full Address</label>
                        <input 
                            type="text" 
                            name="address[{{ $address->id }}][street_name]" 
                            class="form-control @error('address.'.$address->id.'.street_name') is-invalid @enderror" 
                            value="{{ old('address.'.$address->id.'.street_name', $address->street_name) }}" 
                            oninput="removeValidation(this)"
                        >
                        @error('address.'.$address->id.'.street_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="postal_code_{{ $address->id }}" class="form-label">Postal Code</label>
                        <input 
                            type="number" 
                            name="address[{{ $address->id }}][postal_code]" 
                            class="form-control @error('address.'.$address->id.'.postal_code') is-invalid @enderror" 
                            value="{{ old('address.'.$address->id.'.postal_code', $address->postal_code) }}" 
                            oninput="removeValidation(this)"
                        >
                        @error('address.'.$address->id.'.postal_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="city_{{ $address->id }}" class="form-label">City</label>
                        <input 
                            type="text" 
                            name="address[{{ $address->id }}][city]" 
                            class="form-control @error('address.'.$address->id.'.city') is-invalid @enderror" 
                            value="{{ old('address.'.$address->id.'.city', $address->city) }}" 
                            oninput="removeValidation(this)"
                        >
                        @error('address.'.$address->id.'.city')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            @empty
                <p>No addresses available.</p>
            @endforelse

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function removeValidation(element) {
        console.log("removeValidation triggered for:", element.name); // Debug log
        if (element.value.trim()) {
            element.classList.remove('is-invalid');
            const errorFeedback = element.nextElementSibling;
            if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
                errorFeedback.style.display = 'none';
            }
        }
    }
</script>
@endsection
