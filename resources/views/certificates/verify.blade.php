@extends('layouts.app')

@section('title', 'Verify Certificate')

@section('content')

<div class="container-fluid" style="max-width: 800px;">

    <!-- Header -->
    <div class="text-center mb-5 mt-2">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 70px; height: 70px; background: #eff6ff; color: #2563eb;">
            <i class="bi bi-qr-code-scan fs-2"></i>
        </div>
        <h1 class="tc-page-title mb-2">
            Public Certificate Verification Portal
        </h1>
        <p class="tc-page-subtitle mb-0">
            Verify the authenticity, ownership history, and physical condition records of any device registered on TrustChain.
        </p>
    </div>

    <!-- Search Form Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('certificates.verify') }}" method="GET">
                <label class="form-label fw-bold text-dark mb-2">
                    Enter Certificate Number or Verification Token
                </label>

                <div class="input-group input-group-lg mb-3 shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-shield-check text-primary"></i>
                    </span>
                    <input
                        type="text"
                        name="query"
                        class="form-control border-start-0 font-monospace"
                        placeholder="e.g. TC-CERT-2026-88219 or verify-9f8a3c2b..."
                        required
                        autofocus
                    >
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        Verify Now
                    </button>
                </div>

                <div class="text-secondary small">
                    <i class="bi bi-info-circle me-1"></i> You can find the certificate number or QR code token printed on the physical certificate or digital transfer receipt.
                </div>
            </form>
        </div>
    </div>

    <!-- How Verification Works Cards -->
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="card-body">
                    <i class="bi bi-fingerprint fs-3 text-primary mb-2 d-block"></i>
                    <h6 class="fw-bold">SHA-256 Hashes</h6>
                    <p class="text-secondary small mb-0">
                        Every diagnostic log and condition photo has an unforgeable cryptographic checksum.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="card-body">
                    <i class="bi bi-people-fill fs-3 text-success mb-2 d-block"></i>
                    <h6 class="fw-bold">Dual Confirmation</h6>
                    <p class="text-secondary small mb-0">
                        Certificates are only issued when both seller and buyer authenticate the physical transfer.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="card-body">
                    <i class="bi bi-database-check fs-3 text-purple mb-2 d-block" style="color: #7c3aed;"></i>
                    <h6 class="fw-bold">Zero Fraud Risk</h6>
                    <p class="text-secondary small mb-0">
                        Serial numbers and IMEIs are cross-referenced to eliminate disputed device swaps.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
