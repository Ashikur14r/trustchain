@extends('layouts.app')

@section('title', 'Certificates')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="tc-page-title mb-1">
                Digital Ownership Certificates
            </h1>
            <p class="tc-page-subtitle mb-0">
                Verifiable certificates generated upon successful two-sided device transfer confirmation.
            </p>
        </div>

        <a href="{{ route('certificates.verify') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
            <i class="bi bi-qr-code-scan me-2"></i>
            Public Verification Portal
        </a>
    </div>

    @if($certificates->isNotEmpty())
        <div class="row g-4">
            @foreach($certificates as $cert)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                        <!-- Top decorative bar -->
                        <div style="height: 5px; background: linear-gradient(90deg, #2563eb, #7c3aed);"></div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-purple-subtle text-purple border" style="background: #f5f3ff; color: #7c3aed; border-color: #ddd6fe !important;">
                                        <i class="bi bi-patch-check-fill me-1"></i>Official Certificate
                                    </span>
                                    <span class="text-muted small">
                                        {{ $cert->issued_at->format('M d, Y') }}
                                    </span>
                                </div>

                                <div class="fw-bold font-monospace text-primary fs-5 mb-2">
                                    {{ $cert->certificate_number }}
                                </div>

                                @if($cert->transaction && $cert->transaction->device)
                                    <div class="fw-semibold text-dark mb-1">
                                        {{ $cert->transaction->device->brand }} {{ $cert->transaction->device->model }}
                                    </div>
                                    <div class="text-muted small mb-3">
                                        @if($cert->transaction->device->serial_number)
                                            <span>S/N: <span class="font-monospace">{{ $cert->transaction->device->serial_number }}</span></span>
                                        @endif
                                        @if($cert->transaction->device->imei)
                                            <span class="ms-1">· IMEI: <span class="font-monospace">{{ $cert->transaction->device->imei }}</span></span>
                                        @endif
                                    </div>
                                @endif

                                <div class="p-2 bg-light rounded border small mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary">Seller:</span>
                                        <span class="fw-semibold text-dark">{{ $cert->transaction->seller->name ?? 'User' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary">Buyer / Current Owner:</span>
                                        <span class="fw-semibold text-dark">{{ $cert->transaction->buyer->name ?? 'User' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <a href="{{ route('certificates.show', $cert) }}" class="btn btn-sm btn-primary flex-grow-1">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Open Certificate
                                </a>
                                <a href="{{ route('certificates.verify.show', $cert->verification_token) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Public Verification Link">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($certificates->hasPages())
            <div class="mt-4">
                {{ $certificates->links() }}
            </div>
        @endif
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-patch-question fs-1 text-secondary opacity-50 mb-3"></i>
                <h5 class="fw-semibold">No Certificates Issued Yet</h5>
                <p class="text-secondary small mb-3">
                    Certificates are automatically generated when a buyer confirms a pending device transaction.
                </p>
                <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left-right me-1"></i>View Your Transactions
                </a>
            </div>
        </div>
    @endif

</div>

@endsection
