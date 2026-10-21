@extends('layouts.app')

@section('title', 'Create Transaction')

@section('content')

<div class="container-fluid" style="max-width: 1000px;">

    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}" class="text-decoration-none">Transactions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
        <h1 class="tc-page-title mb-1">
            Initiate Device Transfer
        </h1>
        <p class="tc-page-subtitle mb-0">
            Document hardware specifications, accessories, condition evidence, and cryptographic proof of custody.
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" id="createTransactionForm">
        @csrf

        <!-- 1. Device Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:28px; height:28px; font-size:14px; font-weight:600;">1</span>
                    <h5 class="fw-bold mb-0">Device Specification</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Device Type <span class="text-danger">*</span></label>
                        <select name="device_type" class="form-select" required>
                            <option value="">Select Category...</option>
                            <option value="Smartphone" {{ old('device_type') == 'Smartphone' ? 'selected' : '' }}>Smartphone</option>
                            <option value="Laptop" {{ old('device_type') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                            <option value="Tablet" {{ old('device_type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Smartwatch" {{ old('device_type') == 'Smartwatch' ? 'selected' : '' }}>Smartwatch</option>
                            <option value="Audio / Headphones" {{ old('device_type') == 'Audio / Headphones' ? 'selected' : '' }}>Audio / Headphones</option>
                            <option value="Gaming Console" {{ old('device_type') == 'Gaming Console' ? 'selected' : '' }}>Gaming Console</option>
                            <option value="Desktop PC" {{ old('device_type') == 'Desktop PC' ? 'selected' : '' }}>Desktop PC</option>
                            <option value="Other Electronics" {{ old('device_type') == 'Other Electronics' ? 'selected' : '' }}>Other Electronics</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Brand / Manufacturer <span class="text-danger">*</span></label>
                        <input type="text" name="brand" class="form-control" placeholder="e.g. Apple, Samsung, Sony" value="{{ old('brand') }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Model Name & Spec <span class="text-danger">*</span></label>
                        <input type="text" name="model" class="form-control" placeholder="e.g. iPhone 15 Pro (256GB)" value="{{ old('model') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Serial Number (S/N)</label>
                        <input type="text" name="serial_number" class="form-control font-monospace" placeholder="e.g. C39ZT09MP0" value="{{ old('serial_number') }}">
                        <div class="form-text">Found on device settings, chassis or original box.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">IMEI (for cellular devices)</label>
                        <input type="text" name="imei" class="form-control font-monospace" placeholder="e.g. 354892110293847" value="{{ old('imei') }}">
                        <div class="form-text">Dial *#06# on mobile devices to view.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Agreed Transaction Price ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="agreed_price" class="form-control" placeholder="0.00" value="{{ old('agreed_price') }}" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Physical & Functional Condition <span class="text-danger">*</span></label>
                        <textarea name="condition_description" rows="3" class="form-control" placeholder="Describe cosmetic condition, battery health, any scratches, dents, screen condition, or repairs..." required>{{ old('condition_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Included Accessories -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:28px; height:28px; font-size:14px; font-weight:600;">2</span>
                    <h5 class="fw-bold mb-0">Included Accessories</h5>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addAccessoryBtn">
                    <i class="bi bi-plus-circle me-1"></i>Add Accessory
                </button>
            </div>
            <div class="card-body p-4">
                <p class="text-secondary small mb-3">
                    List accessories, cables, boxes, or documentation included in this transfer.
                </p>

                <div id="accessoriesContainer">
                    <div class="row g-2 mb-2 accessory-row align-items-center">
                        <div class="col-7 col-md-8">
                            <input type="text" name="accessories[0][name]" class="form-control" placeholder="Accessory name (e.g. Original 20W Charger, Retail Box)" value="Original Retail Box">
                        </div>
                        <div class="col-4 col-md-3">
                            <select name="accessories[0][condition]" class="form-select">
                                <option value="Like New">Like New</option>
                                <option value="Good" selected>Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Worn">Worn</option>
                            </select>
                        </div>
                        <div class="col-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-accessory-btn" title="Remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Cryptographic Evidence & Media Upload -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:28px; height:28px; font-size:14px; font-weight:600;">3</span>
                    <div>
                        <h5 class="fw-bold mb-0">Verification Evidence (SHA-256 Hashed)</h5>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-shield-lock-fill fs-3 text-primary"></i>
                    <div class="small text-secondary">
                        Every uploaded file is automatically fingerprinted with a <strong>cryptographic SHA-256 checksum</strong>. This ensures evidence cannot be modified, replaced, or repudiated after transaction creation.
                    </div>
                </div>

                <div id="evidenceContainer">
                    <div class="p-3 border rounded-3 bg-light mb-3 evidence-row">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label small fw-semibold">File / Photo (Max 10MB)</label>
                                <input type="file" name="evidence_files[]" class="form-control" accept="image/*,.pdf">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold">Evidence Category</label>
                                <select name="evidence_types[]" class="form-select">
                                    <option value="condition">Device Condition Photo</option>
                                    <option value="imei_verification">IMEI / Serial Screen</option>
                                    <option value="receipt">Purchase Invoice / Receipt</option>
                                    <option value="diagnostic">Diagnostic Report</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">Short Note / Description</label>
                                <input type="text" name="evidence_descriptions[]" class="form-control" placeholder="e.g. Front screen display with zero dead pixels">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary" id="addEvidenceBtn">
                    <i class="bi bi-file-earmark-plus me-1"></i>Add Another Evidence File
                </button>
            </div>
        </div>

        <!-- 4. Buyer Assignment & Notes -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:28px; height:28px; font-size:14px; font-weight:600;">4</span>
                    <h5 class="fw-bold mb-0">Buyer Assignment & Terms</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Buyer Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="buyer_email" list="buyersList" class="form-control" placeholder="buyer@example.com" value="{{ old('buyer_email') }}" required>
                        <datalist id="buyersList">
                            @foreach($potentialBuyers as $pb)
                                <option value="{{ $pb->email }}">{{ $pb->name }} ({{ $pb->email }})</option>
                            @endforeach
                        </datalist>
                        <div class="form-text">Enter the registered TrustChain email of the recipient buyer.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Seller Handover Notes</label>
                        <textarea name="seller_notes" rows="2" class="form-control" placeholder="Any specific handover instructions, meeting location, payment method confirmed, or warranty notes...">{{ old('seller_notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center pt-2 pb-5">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary px-4">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
                <i class="bi bi-shield-check me-2"></i>Create & Submit for Buyer Review
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
    // Dynamic Accessories
    let accessoryCount = 1;
    document.getElementById('addAccessoryBtn').addEventListener('click', function() {
        const container = document.getElementById('accessoriesContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 accessory-row align-items-center';
        row.innerHTML = `
            <div class="col-7 col-md-8">
                <input type="text" name="accessories[${accessoryCount}][name]" class="form-control" placeholder="Accessory name" required>
            </div>
            <div class="col-4 col-md-3">
                <select name="accessories[${accessoryCount}][condition]" class="form-select">
                    <option value="Like New">Like New</option>
                    <option value="Good" selected>Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Worn">Worn</option>
                </select>
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-accessory-btn" title="Remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        accessoryCount++;
    });

    document.getElementById('accessoriesContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-accessory-btn')) {
            const rows = document.querySelectorAll('.accessory-row');
            if (rows.length > 1) {
                e.target.closest('.accessory-row').remove();
            } else {
                alert('At least one accessory row must remain (you can leave it blank if none).');
            }
        }
    });

    // Dynamic Evidence
    document.getElementById('addEvidenceBtn').addEventListener('click', function() {
        const container = document.getElementById('evidenceContainer');
        const card = document.createElement('div');
        card.className = 'p-3 border rounded-3 bg-light mb-3 evidence-row';
        card.innerHTML = `
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold">File / Photo (Max 10MB)</label>
                    <input type="file" name="evidence_files[]" class="form-control" accept="image/*,.pdf">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">Evidence Category</label>
                    <select name="evidence_types[]" class="form-select">
                        <option value="condition">Device Condition Photo</option>
                        <option value="imei_verification">IMEI / Serial Screen</option>
                        <option value="receipt">Purchase Invoice / Receipt</option>
                        <option value="diagnostic">Diagnostic Report</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label small fw-semibold">Short Note / Description</label>
                        <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-evidence-btn" title="Remove file row">
                            <i class="bi bi-x-circle"></i> Remove
                        </button>
                    </div>
                    <input type="text" name="evidence_descriptions[]" class="form-control" placeholder="Description of photo...">
                </div>
            </div>
        `;
        container.appendChild(card);
    });

    document.getElementById('evidenceContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-evidence-btn')) {
            e.target.closest('.evidence-row').remove();
        }
    });
</script>
@endpush

@endsection
