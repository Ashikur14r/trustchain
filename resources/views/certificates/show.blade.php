@extends('layouts.app')

@section('title', 'Certificate ' . $certificate->certificate_number)

@section('content')

<div class="container-fluid" style="max-width: 950px;">

    <!-- Top Action Bar (hidden when printing) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Certificates
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('certificates.verify.show', $certificate->verification_token) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-shield-check me-1"></i>Verify Public Link
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print / Save PDF
            </button>
        </div>
    </div>

    <!-- Official Certificate Document -->
    <div class="card border-0 shadow certificate-document p-4 p-md-5 bg-white position-relative overflow-hidden" id="certificateToPrint">

        <!-- Ornamental Border -->
        <div class="certificate-inner-border p-4 p-md-5 border border-3 rounded-4" style="border-color: #cbd5e1 !important; border-style: double !important;">

            <!-- Certificate Header -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 72px; height: 72px; background: #eff6ff; color: #2563eb; border: 3px solid #bfdbfe;">
                    <i class="bi bi-shield-check" style="font-size: 38px;"></i>
                </div>

                <div class="text-uppercase tracking-wider fw-bold small text-primary" style="letter-spacing: 2px;">
                    TrustChain Digital Verification Network
                </div>

                <h1 class="fw-bold text-dark mt-2 mb-1" style="font-family: 'Times New Roman', serif; font-size: 30px;">
                    Certificate of Ownership & Chain of Custody
                </h1>

                <p class="text-secondary small mb-0">
                    This official certificate verifies the immutable transfer of title, custody, and physical condition of the hardware specified below.
                </p>
            </div>

            <!-- Certificate Meta Badges -->
            <div class="row g-2 text-center py-3 my-3 bg-light rounded-3 border">
                <div class="col-12 col-md-4">
                    <span class="text-secondary small d-block">Certificate Number</span>
                    <strong class="font-monospace text-primary fs-6">{{ $certificate->certificate_number }}</strong>
                </div>
                <div class="col-12 col-md-4">
                    <span class="text-secondary small d-block">Issuance Timestamp</span>
                    <strong class="text-dark fs-6">{{ $certificate->issued_at->format('F d, Y · H:i:s T') }}</strong>
                </div>
                <div class="col-12 col-md-4">
                    <span class="text-secondary small d-block">Verification Status</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i>VERIFIED AUTHENTIC
                    </span>
                </div>
            </div>

            <!-- Transfer Parties -->
            <div class="row g-4 my-3">
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-3 h-100 bg-white">
                        <span class="badge bg-secondary-subtle text-secondary mb-2">Original Transferor / Seller</span>
                        <div class="fw-bold fs-5 text-dark">{{ $certificate->transaction->seller->name ?? 'User' }}</div>
                        <div class="text-muted small">{{ $certificate->transaction->seller->email ?? 'N/A' }}</div>
                        <div class="text-success small mt-2">
                            <i class="bi bi-check2-circle me-1"></i>Identity Authenticated
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-3 h-100 bg-white">
                        <span class="badge bg-primary-subtle text-primary mb-2">Verified Acquirer / Owner</span>
                        <div class="fw-bold fs-5 text-dark">{{ $certificate->transaction->buyer->name ?? 'User' }}</div>
                        <div class="text-muted small">{{ $certificate->transaction->buyer->email ?? 'N/A' }}</div>
                        <div class="text-success small mt-2">
                            <i class="bi bi-check2-circle me-1"></i>Title Transfer Confirmed
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Dossier -->
            <div class="my-4">
                <h6 class="fw-bold text-dark text-uppercase small border-bottom pb-2" style="letter-spacing: 1px;">
                    Hardware Specification
                </h6>

                <div class="row g-3 py-2">
                    <div class="col-6 col-md-4">
                        <span class="text-secondary small d-block">Brand & Model</span>
                        <span class="fw-bold text-dark">
                            {{ $certificate->transaction->device->brand ?? '' }} {{ $certificate->transaction->device->model ?? '' }}
                        </span>
                    </div>

                    <div class="col-6 col-md-4">
                        <span class="text-secondary small d-block">Category</span>
                        <span class="fw-semibold text-dark">{{ $certificate->transaction->device->device_type ?? 'Hardware' }}</span>
                    </div>

                    <div class="col-6 col-md-4">
                        <span class="text-secondary small d-block">Agreed Transfer Value</span>
                        <span class="fw-bold text-success">${{ number_format($certificate->transaction->device->agreed_price ?? 0, 2) }}</span>
                    </div>

                    <div class="col-6 col-md-4">
                        <span class="text-secondary small d-block">Serial Number (S/N)</span>
                        <span class="fw-bold font-monospace text-dark">{{ $certificate->transaction->device->serial_number ?: 'N/A' }}</span>
                    </div>

                    <div class="col-6 col-md-4">
                        <span class="text-secondary small d-block">IMEI</span>
                        <span class="fw-bold font-monospace text-dark">{{ $certificate->transaction->device->imei ?: 'N/A' }}</span>
                    </div>

                    <div class="col-6 col-md-4">
                        <span class="text-secondary small d-block">Transaction Code</span>
                        <span class="font-monospace text-primary fw-semibold">{{ $certificate->transaction->transaction_code }}</span>
                    </div>

                    <div class="col-12">
                        <span class="text-secondary small d-block">Condition Audit Record:</span>
                        <div class="small text-secondary bg-light p-2 rounded border mt-1">
                            {{ $certificate->transaction->device->condition_description ?: 'No specific physical condition notes.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cryptographic Evidence Manifest -->
            <div class="my-4">
                <h6 class="fw-bold text-dark text-uppercase small border-bottom pb-2" style="letter-spacing: 1px;">
                    Cryptographic Evidence Manifest (SHA-256)
                </h6>

                @if($certificate->transaction->device && $certificate->transaction->device->evidence->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">File Name</th>
                                    <th style="width: 20%;">Category</th>
                                    <th style="width: 55%;">SHA-256 Checksum Fingerprint</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($certificate->transaction->device->evidence as $ev)
                                    <tr>
                                        <td class="fw-medium text-dark">{{ $ev->file_name }}</td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">
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
                @else
                    <div class="text-muted small">No evidence files attached to this transaction.</div>
                @endif
            </div>

            <!-- Footer: Security Seal & Verification QR Code -->
            <div class="row align-items-center pt-4 mt-4 border-top">
                <div class="col-8">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Official Embossed Stamp -->
                        <div class="rounded-circle border border-2 border-primary p-2 text-center d-flex flex-column align-items-center justify-content-center" style="width: 95px; height: 95px; border-style: dashed !important;">
                            <i class="bi bi-patch-check-fill text-primary fs-3"></i>
                            <span class="text-uppercase fw-bold text-primary" style="font-size: 8px; letter-spacing: 1px;">TRUSTCHAIN<br>VERIFIED</span>
                        </div>

                        <div>
                            <div class="fw-bold text-dark small">Public Verification Key:</div>
                            <div class="font-monospace text-primary small text-truncate" style="max-width: 320px;">
                                {{ $certificate->verification_token }}
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                Scan QR code or visit <strong class="text-dark">{{ route('certificates.verify') }}</strong> to confirm authenticity.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-4 text-end">
                    <!-- SVG QR Code Representation -->
                    <div class="d-inline-block p-2 bg-white border rounded shadow-sm text-center">
                        <svg width="76" height="76" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- QR Pattern Elements -->
                            <rect width="100" height="100" fill="white"/>
                            <!-- Position Finder Top Left -->
                            <rect x="5" y="5" width="28" height="28" fill="#1e293b"/>
                            <rect x="9" y="9" width="20" height="20" fill="white"/>
                            <rect x="13" y="13" width="12" height="12" fill="#2563eb"/>
                            <!-- Position Finder Top Right -->
                            <rect x="67" y="5" width="28" height="28" fill="#1e293b"/>
                            <rect x="71" y="9" width="20" height="20" fill="white"/>
                            <rect x="75" y="13" width="12" height="12" fill="#2563eb"/>
                            <!-- Position Finder Bottom Left -->
                            <rect x="5" y="67" width="28" height="28" fill="#1e293b"/>
                            <rect x="9" y="71" width="20" height="20" fill="white"/>
                            <rect x="13" y="75" width="12" height="12" fill="#2563eb"/>
                            <!-- QR Matrix Data Dots -->
                            <rect x="40" y="8" width="6" height="6" fill="#1e293b"/>
                            <rect x="52" y="8" width="6" height="6" fill="#1e293b"/>
                            <rect x="40" y="20" width="6" height="6" fill="#1e293b"/>
                            <rect x="46" y="26" width="6" height="6" fill="#1e293b"/>
                            <rect x="52" y="20" width="6" height="6" fill="#1e293b"/>
                            <rect x="8" y="40" width="6" height="6" fill="#1e293b"/>
                            <rect x="20" y="46" width="6" height="6" fill="#1e293b"/>
                            <rect x="26" y="40" width="6" height="6" fill="#1e293b"/>
                            <rect x="40" y="40" width="8" height="8" fill="#2563eb"/>
                            <rect x="54" y="46" width="6" height="6" fill="#1e293b"/>
                            <rect x="40" y="58" width="6" height="6" fill="#1e293b"/>
                            <rect x="52" y="58" width="6" height="6" fill="#1e293b"/>
                            <rect x="68" y="40" width="6" height="6" fill="#1e293b"/>
                            <rect x="80" y="46" width="6" height="6" fill="#1e293b"/>
                            <rect x="86" y="40" width="6" height="6" fill="#1e293b"/>
                            <rect x="40" y="76" width="6" height="6" fill="#1e293b"/>
                            <rect x="52" y="82" width="6" height="6" fill="#1e293b"/>
                            <rect x="46" y="88" width="6" height="6" fill="#1e293b"/>
                            <rect x="68" y="70" width="6" height="6" fill="#1e293b"/>
                            <rect x="80" y="76" width="6" height="6" fill="#1e293b"/>
                            <rect x="74" y="86" width="6" height="6" fill="#1e293b"/>
                            <rect x="86" y="86" width="6" height="6" fill="#1e293b"/>
                        </svg>
                        <div class="text-secondary" style="font-size: 9px; font-weight: 600;">SCAN TO VERIFY</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Print Styles -->
<style>
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    .tc-sidebar, .tc-navbar, .d-print-none {
        display: none !important;
    }
    .tc-main {
        margin-left: 0 !important;
    }
    .tc-content {
        padding: 0 !important;
    }
    .certificate-document {
        box-shadow: none !important;
        border: none !important;
    }
}
</style>

@endsection
