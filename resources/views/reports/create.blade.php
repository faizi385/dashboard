@extends('layouts.admin')

@section('content')
<div class="container p-4">




    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white mb-4">Add Report for {{ $retailer->dba }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>


    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <form action="{{ route('retailers.reports.store', $retailer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Location Dropdown -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                    <select name="location" id="location" class="form-select @error('location') is-invalid @enderror">
                        <option value="">Select Location</option>
                        @foreach($addresses as $address)
                            <option value="{{ $address->id }}">{{ $address->full_address }}</option>
                        @endforeach
                    </select>
                    @error('location')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- POS Dropdown -->
                <div class="col-md-6">
                    <label for="pos" class="form-label">POS System <span class="text-danger">*</span></label>
                    <select name="pos" id="pos" class="form-select @error('pos') is-invalid @enderror" onchange="toggleFileUpload()">
                        <option value="">Select POS</option>
                        <option value="greenline">Greenline</option>
                        <option value="techpos">TechPOS</option>
                        <option value="cova">COVA</option>
                        <option value="barnet">Barnet</option>
                        <option value="profittech">ProfitTech</option>
                        <option value="global">Global Till</option>
                        <option value="ideal">Ideal</option>
                        <option value="tendy">Tendy</option>
                        <option value="otherpos">Other POS</option>
                    </select>
                    @error('pos')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- File Uploads -->
            <div class="row">
                <!-- Diagnostic Report Upload -->
                <div class="col-md-6">
                    <div id="multipleUploads" class="upload-container">
                        <label for="diagnostic_report" class="form-label">Diagnostic Report</label>
                        <div class="file-upload-box">
                            <input type="file" name="diagnostic_report" id="diagnostic_report" class="file-upload-input @error('diagnostic_report') is-invalid @enderror">
                            <span class="upload-icon">+</span>
                            <p class="upload-text">Choose Excel File</p>
                        </div>
                        @error('diagnostic_report')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Sales Summary Report Upload -->
                <div class="col-md-6">
                    <div id="salesSummaryUpload" class="upload-container">
                        <label for="sales_summary_report" class="form-label">Sales Summary Report</label>
                        <div class="file-upload-box">
                            <input type="file" name="sales_summary_report" id="sales_summary_report" class="file-upload-input @error('sales_summary_report') is-invalid @enderror">
                            <span class="upload-icon">+</span>
                            <p class="upload-text">Choose Excel File</p>
                        </div>
                        @error('sales_summary_report')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Single Upload Section -->
                <div class="col-md-6" id="singleUpload" style="display: none;">
                    <div class="upload-container">
                        <label for="inventory_log_summary" class="form-label">Inventory Log Summary</label>
                        <div class="file-upload-box">
                            <input type="file" name="inventory_log_summary" id="inventory_log_summary" class="file-upload-input @error('inventory_log_summary') is-invalid @enderror">
                            <span class="upload-icon">+</span>
                            <p class="upload-text">Choose Excel File</p>
                        </div>
                        @error('inventory_log_summary')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mt-4">
                {{-- <button type="button" class="btn btn-outline-primary">Back</button> --}}
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFileUpload() {
        const posSelect = document.getElementById('pos');
        const selectedPos = posSelect.value;
        const multipleUploads = document.getElementById('multipleUploads');
        const salesSummaryUpload = document.getElementById('salesSummaryUpload');
        const singleUpload = document.getElementById('singleUpload');

        // Show/hide uploads based on POS selection
        if (['greenline', 'techpos', 'barnet', 'profittech', 'otherpos', 'global'].includes(selectedPos)) {
            multipleUploads.style.display = 'none';
            salesSummaryUpload.style.display = 'none';
            singleUpload.style.display = 'block';
        } else if (['cova', 'ideal', 'tendy'].includes(selectedPos)) {
            multipleUploads.style.display = 'block';
            salesSummaryUpload.style.display = 'block';
            singleUpload.style.display = 'none';
        } else {
            multipleUploads.style.display = 'block';
            salesSummaryUpload.style.display = 'block';
            singleUpload.style.display = 'none';
        }
    }

    function removeValidation(element) {
        element.classList.remove('is-invalid');
        const errorFeedback = element.nextElementSibling;
        if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
            errorFeedback.style.display = 'none';
        }
    }

    // Update the text with selected file name and remove validation error when file is chosen
    document.querySelectorAll('.file-upload-input').forEach(input => {
        input.addEventListener('change', function () {
            const fileName = this.files[0]?.name || 'Choose Excel File';
            const uploadBox = this.closest('.file-upload-box');
            uploadBox.querySelector('p').textContent = fileName;

            // Remove validation error
            removeValidation(this);
        });
    });

    // Remove validation error on dropdown change
    document.getElementById('location').addEventListener('change', function () {
        removeValidation(this);
    });

    document.getElementById('pos').addEventListener('change', function () {
        removeValidation(this);
    });

    // Show error message if session error is set
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

<!-- Custom CSS for styling the upload area -->

@endsection
