@extends('layouts.app')

@section('title', 'Evidence Vault')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="tc-page-title mb-1">
                Evidence Vault
            </h1>
            <p class="tc-page-subtitle mb-0">
                Cryptographically hashed files, photos, and inspection manifests securing your device transfers.
            </p>
        </div>

        <a href="{{ route('transactions.create') }}" class="btn btn-outline-primary d-inline-flex align-items-center">
            <i class="bi bi-plus-circle me-2"></i>
            New Transaction
        </a>
    </div>

    <!-- Explainer Alert -->
    <div class="alert alert-light border shadow-sm d-flex align-items-center gap-3 mb-4">
        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px; height:48px; flex-shrink:0;">
            <i class="bi bi-shield-check fs-4"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">Immutable Digital Chain of Custody</div>
            <div class="text-secondary small">
                Each evidence file is processed with the SHA-256 algorithm at upload time. The cryptographic checksum guarantees that the file inspected at purchase cannot be altered, replaced, or repudiated.
            </div>
        </div>
    </div>

    @if($evidenceItems->isNotEmpty())
        <div class="row g-4">
            @foreach($evidenceItems as $item)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Top: Type & Transaction -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ ucwords(str_replace('_', ' ', $item->evidence_type)) }}
                                    </span>
                                    @if($item->device && $item->device->transaction)
                                        <a href="{{ route('transactions.show', $item->device->transaction) }}" class="badge bg-light text-secondary border font-monospace text-decoration-none">
                                            {{ $item->device->transaction->transaction_code }}
                                        </a>
                                    @endif
                                </div>

                                <!-- File Title -->
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-file-earmark-check fs-4 text-primary"></i>
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" title="{{ $item->file_name }}">
                                        {{ $item->file_name }}
                                    </h6>
                                </div>

                                <!-- Device Meta -->
                                @if($item->device)
                                    <div class="text-muted small mb-2">
                                        <strong>Device:</strong> {{ $item->device->brand }} {{ $item->device->model }}
                                    </div>
                                @endif

                                <!-- Description -->
                                @if($item->description)
                                    <p class="text-secondary small mb-3">
                                        {{ Str::limit($item->description, 110) }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <!-- Cryptographic Hash -->
                                <div class="bg-light p-2 rounded border small mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-dark fw-bold" style="font-size: 11px;">SHA-256 FINGERPRINT</span>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-primary" onclick="navigator.clipboard.writeText('{{ $item->file_hash }}'); alert('SHA-256 hash copied to clipboard!');" title="Copy Checksum">
                                            <i class="bi bi-clipboard me-1"></i>Copy
                                        </button>
                                    </div>
                                    <div class="font-monospace text-truncate text-secondary" style="font-size: 11px;" title="{{ $item->file_hash }}">
                                        {{ $item->file_hash ?: 'Uncalculated' }}
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        {{ $item->created_at->format('M d, Y') }}
                                    </span>

                                    @if(Storage::disk(config('filesystems.default'))->exists($item->file_path))
                                        <a href="{{ route('evidence.download', $item) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Open File
                                        </a>
                                    @elseif($item->device && $item->device->transaction)
                                        <a href="{{ route('transactions.show', $item->device->transaction) }}" class="btn btn-sm btn-light border">
                                            View Dossier
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($evidenceItems->hasPages())
            <div class="mt-4">
                {{ $evidenceItems->links() }}
            </div>
        @endif
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-shield-slash fs-1 text-secondary opacity-50 mb-3"></i>
                <h5 class="fw-semibold">No Evidence Records Found</h5>
                <p class="text-secondary small mb-3">
                    Evidence files uploaded during transaction creation will appear here with cryptographic hashes.
                </p>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Create Transaction with Evidence
                </a>
            </div>
        </div>
    @endif

</div>

@endsection
