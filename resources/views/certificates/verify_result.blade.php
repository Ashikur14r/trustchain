@extends('layouts.app')

@section('title', $certificate ? 'Verified: ' . $certificate->certificate_number : 'Verification Result')

@section('content')

<div class="container-fluid" style="max-width: 900px;">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('certificates.verify') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>New Verification Search
        </a>
    </div>

    @if($certificate)
        <!-- Valid Verified Record -->
        <div class="card border-0 shadow-sm border-success border-top border-4 mb-4" style="border-top-width: 5px !important;">
            <div class="card-body p-4 p-md-5">

                <!-- Verification Header Banner -->
                <div class="d-flex align-items-center gap-3 p-3 bg-success-subtle rounded-3 border border-success-subtle mb-4">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; flex-shrink: 0;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-success mb-0">VERIFIED AUTHENTIC RECORD</h4>
                        <p class="text-secondary small mb-0">
                            This device and transfer record exists in the TrustChain registry and has undergone complete cryptographic verification.
                        </p>
                    </div>
                </div>

                <!-- Certificate Metadata -->
                <div class="row g-3 py-3 border-bottom mb-4">
                    <div class="col-12 col-md-4">
                        <span class="text-secondary small d-block">Certificate Number</span>
                        <strong class="font-monospace text-primary fs-5">{{ $certificate->certificate_number }}</strong>
                    </div>

                    <div class="col-12 col-md-4">
                        <span class="text-secondary small d-block">Issued On</span>
                        <strong class="text-dark">{{ $certificate->issued_at->format('F d, Y · H:i T') }}</strong>
                    </div>

                    <div class="col-12 col-md-4">
                        <span class="text-secondary small d-block">Chain of Custody Code</span>
                        <span class="font-monospace text-dark fw-bold">{{ $certificate->transaction->transaction_code }}</span>
                    </div>
                </div>

                <!-- Verified Hardware Specifications -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-laptop me-2 text-primary"></i>Hardware Identity
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-secondary small d-block">Brand & Model</span>
                                <div class="fw-bold fs-5 text-dark">
                                    {{ $certificate->transaction->device->brand }} {{ $certificate->transaction->device->model }}
                                </div>
                                <span class="badge bg-secondary-subtle text-secondary mt-1">
                                    {{ $certificate->transaction->device->device_type }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-secondary small d-block">Serial Number & IMEI</span>
                                <div class="fw-semibold font-monospace text-dark">
                                    S/N: {{ $certificate->transaction->device->serial_number ?: 'Not specified' }}
                                </div>
                                <div class="fw-semibold font-monospace text-dark mt-1">
                                    IMEI: {{ $certificate->transaction->device->imei ?: 'Not applicable' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-secondary small d-block">Condition Audited at Transfer:</span>
                                <div class="text-secondary small mt-1">
                                    {{ $certificate->transaction->device->condition_description ?: 'No specific physical condition notes.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verified Participants -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-people me-2 text-primary"></i>Verified Transfer Parties
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="p-3 border rounded">
                                <span class="badge bg-secondary-subtle text-secondary mb-1">Transferor / Seller</span>
                                <div class="fw-bold text-dark">{{ $certificate->transaction->seller->name ?? 'User' }}</div>
                                <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Verified & Confirmed</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="p-3 border rounded">
                                <span class="badge bg-primary-subtle text-primary mb-1">Acquirer / Current Owner</span>
                                <div class="fw-bold text-dark">{{ $certificate->transaction->buyer->name ?? 'User' }}</div>
                                <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Verified & Confirmed</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evidence Manifest -->
                @if($certificate->transaction->device && $certificate->transaction->device->evidence->isNotEmpty())
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="bi bi-shield-check me-2 text-primary"></i>Cryptographic Evidence Manifest
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>File</th>
                                        <th>Category</th>
                                        <th>SHA-256 Checksum Fingerprint</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificate->transaction->device->evidence as $ev)
                                        <tr>
                                            <td class="fw-semibold">{{ $ev->file_name }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ ucwords(str_replace('_', ' ', $ev->evidence_type)) }}
                                                </span>
                                            </td>
                                            <td class="font-monospace text-muted" style="font-size: 11px;">
                                                {{ $ev->file_hash }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="text-center pt-3 border-top">
                    <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-primary px-4">
                        <i class="bi bi-file-earmark-pdf me-2"></i>View Official Printable Certificate
                    </a>
                </div>

            </div>
        </div>
    @else
        <!-- Not Found / Invalid -->
        <div class="card border-0 shadow-sm border-danger border-top border-4 mb-4" style="border-top-width: 5px !important;">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-shield-x fs-1"></i>
                </div>

                <h4 class="fw-bold text-danger mb-2">RECORD NOT FOUND</h4>
                <p class="text-secondary small mb-4">
                    No verified certificate matching the query <strong class="font-monospace text-dark">"{{ $token }}"</strong> could be located in the TrustChain registry.
                </p>

                <div class="alert alert-light border small text-start mx-auto mb-4" style="max-width: 600px;">
                    <h6 class="fw-bold mb-2">Possible Reasons:</h6>
                    <ul class="mb-0 ps-3 text-secondary">
                        <li>The certificate number or token was mistyped.</li>
                        <li>The transaction is still pending buyer acceptance and the certificate has not yet been issued.</li>
                        <li>The certificate may have been invalidated or cancelled.</li>
                    </ul>
                </div>

                <a href="{{ route('certificates.verify') }}" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Try Another Query
                </a>
            </div>
        </div>
    @endif

</div>

@endsection
