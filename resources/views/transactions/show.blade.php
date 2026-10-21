@extends('layouts.app')

@section('title', 'Transaction ' . $transaction->transaction_code)

@section('content')

<div class="container-fluid" style="max-width: 1100px;">

    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}" class="text-decoration-none">Transactions</a></li>
                    <li class="breadcrumb-item active font-monospace" aria-current="page">{{ $transaction->transaction_code }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h1 class="tc-page-title font-monospace mb-0">
                    {{ $transaction->transaction_code }}
                </h1>

                @if($transaction->status === 'confirmed')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                        <i class="bi bi-check-circle-fill me-1"></i>Confirmed Transfer
                    </span>
                @elseif($transaction->status === 'pending')
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 fs-6">
                        <i class="bi bi-clock-history me-1"></i>Awaiting Buyer Acceptance
                    </span>
                @elseif($transaction->status === 'cancelled')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6">
                        <i class="bi bi-x-circle me-1"></i>Cancelled
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>All Transactions
            </a>
            @if($transaction->certificate)
                <a href="{{ route('certificates.show', $transaction->certificate) }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-patch-check-fill me-2"></i>View Certificate
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Certificate Banner if Confirmed -->
    @if($transaction->certificate)
        <div class="card border-0 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width:50px; height:50px; flex-shrink:0;">
                        <i class="bi bi-patch-check-fill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Official Digital Ownership Certificate Issued</h5>
                        <p class="mb-0 text-white-50 small">
                            Certificate ID: <strong class="text-white font-monospace">{{ $transaction->certificate->certificate_number }}</strong> · 
                            Verified on {{ $transaction->certificate->issued_at->format('F d, Y') }}
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('certificates.show', $transaction->certificate) }}" class="btn btn-light text-primary fw-semibold px-4">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Open Certificate
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Prompt for Buyer if Pending -->
    @if($transaction->status === 'pending')
        @if(auth()->id() === $transaction->buyer_id)
            <div class="card border-0 shadow-sm border-warning border-start border-4 mb-4" style="border-left-width: 5px !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <i class="bi bi-bell-fill text-warning me-2"></i>Action Required: Buyer Confirmation
                            </h5>
                            <p class="text-secondary small mb-0">
                                You are the designated buyer for this device. Please inspect the device specification, serial/IMEI, accessories, and evidence below. Once satisfied, click <strong>Confirm & Accept Transfer</strong> to issue the tamper-proof digital certificate.
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <button type="button" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                <i class="bi bi-check2-circle me-1"></i>Confirm & Accept
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                Decline
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(auth()->id() === $transaction->seller_id)
            <div class="card border-0 shadow-sm border-info border-start border-4 mb-4" style="border-left-width: 5px !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h6 class="fw-bold mb-1"><i class="bi bi-hourglass-split text-info me-2"></i>Waiting for Buyer Acceptance</h6>
                        <p class="text-secondary small mb-0">
                            The transfer invitation has been recorded for <strong>{{ $transaction->buyer->email ?? 'buyer' }}</strong>. You will receive an official certificate once the buyer confirms.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            Cancel Transaction
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="row g-4">

        <!-- Left Column: Device & Accessories & Evidence -->
        <div class="col-12 col-lg-8">

            <!-- Device Specifications -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-laptop me-2 text-primary"></i>Device Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="text-secondary small">Brand & Model</label>
                            <div class="fw-bold fs-5 text-dark">
                                {{ $transaction->device->brand ?? '' }} {{ $transaction->device->model ?? '' }}
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="text-secondary small">Agreed Price</label>
                            <div class="fw-bold fs-5 text-success">
                                ${{ number_format($transaction->device->agreed_price ?? 0, 2) }}
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label class="text-secondary small">Device Category</label>
                            <div class="fw-semibold">
                                {{ $transaction->device->device_type ?? 'Hardware' }}
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label class="text-secondary small">Serial Number</label>
                            <div class="fw-semibold font-monospace">
                                {{ $transaction->device->serial_number ?? 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label class="text-secondary small">IMEI</label>
                            <div class="fw-semibold font-monospace">
                                {{ $transaction->device->imei ?? 'Not applicable' }}
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="text-secondary small">Physical & Functional Condition</label>
                            <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                                {{ $transaction->device->condition_description ?? 'No specific notes recorded.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accessories List -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Included Accessories
                    </h5>
                    <span class="badge bg-light text-secondary border">
                        {{ $transaction->device ? $transaction->device->accessories->count() : 0 }} Items
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($transaction->device && $transaction->device->accessories->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4 border-0">Accessory Item</th>
                                        <th scope="col" class="pe-4 border-0 text-end">Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction->device->accessories as $acc)
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark">
                                                <i class="bi bi-check2 text-success me-2"></i>{{ $acc->name }}
                                            </td>
                                            <td class="pe-4 text-end">
                                                <span class="badge bg-light text-dark border">
                                                    {{ $acc->condition ?? 'Good' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">
                            No additional accessories recorded for this transaction.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cryptographic Evidence Vault -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-shield-check me-2 text-primary"></i>Cryptographic Evidence Vault
                        </h5>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        SHA-256 Verified
                    </span>
                </div>
                <div class="card-body p-4">
                    @if($transaction->device && $transaction->device->evidence->isNotEmpty())
                        <div class="row g-3">
                            @foreach($transaction->device->evidence as $ev)
                                <div class="col-12">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-file-earmark-image fs-4 text-primary"></i>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $ev->file_name }}</div>
                                                    <span class="badge bg-secondary-subtle text-secondary small">
                                                        {{ ucwords(str_replace('_', ' ', $ev->evidence_type)) }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if(Storage::disk(config('filesystems.default'))->exists($ev->file_path))
                                                <a href="{{ route('evidence.download', $ev) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>View File
                                                </a>
                                            @endif
                                        </div>

                                        @if($ev->description)
                                            <p class="text-secondary small mb-2">{{ $ev->description }}</p>
                                        @endif

                                        <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded border small">
                                            <div class="font-monospace text-truncate text-secondary me-2" title="{{ $ev->file_hash }}">
                                                <span class="text-dark fw-semibold">SHA-256:</span> {{ $ev->file_hash }}
                                            </div>
                                            <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick="navigator.clipboard.writeText('{{ $ev->file_hash }}'); alert('SHA-256 hash copied to clipboard!');" title="Copy Hash">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-images fs-2 text-secondary opacity-50 mb-2"></i>
                            <div>No photographic or diagnostic evidence files recorded.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: Parties & Audit Trail -->
        <div class="col-12 col-lg-4">

            <!-- Transaction Parties -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Transaction Participants</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Seller -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Seller
                            </span>
                            @if(auth()->id() === $transaction->seller_id)
                                <span class="badge bg-light text-muted border">You</span>
                            @endif
                        </div>
                        <div class="fw-bold text-dark">{{ $transaction->seller->name ?? 'User' }}</div>
                        <div class="text-muted small">{{ $transaction->seller->email ?? 'N/A' }}</div>
                    </div>

                    <hr class="my-3">

                    <!-- Buyer -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="bi bi-box-arrow-in-down-left me-1"></i>Buyer
                            </span>
                            @if(auth()->id() === $transaction->buyer_id)
                                <span class="badge bg-light text-muted border">You</span>
                            @endif
                        </div>
                        <div class="fw-bold text-dark">{{ $transaction->buyer->name ?? 'User' }}</div>
                        <div class="text-muted small">{{ $transaction->buyer->email ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Handover Notes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Handover Notes & Terms</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="fw-semibold small text-dark mb-1">Seller Handover Notes:</div>
                        <div class="text-secondary small bg-light p-2 rounded border">
                            {{ $transaction->seller_notes ?: 'No specific seller notes added.' }}
                        </div>
                    </div>

                    @if($transaction->buyer_notes)
                        <div>
                            <div class="fw-semibold small text-dark mb-1">Buyer Acceptance Notes:</div>
                            <div class="text-secondary small bg-light p-2 rounded border">
                                {{ $transaction->buyer_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Custody Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Custody Timeline</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 position-relative">
                        <li class="mb-3 ps-3 border-start border-2 border-primary">
                            <div class="small fw-bold text-dark">Transaction Initiated</div>
                            <div class="text-muted small">{{ $transaction->created_at->format('M d, Y · h:i A') }}</div>
                        </li>

                        @if($transaction->confirmed_at)
                            <li class="ps-3 border-start border-2 border-success">
                                <div class="small fw-bold text-success">Transfer Confirmed & Certified</div>
                                <div class="text-muted small">{{ $transaction->confirmed_at->format('M d, Y · h:i A') }}</div>
                            </li>
                        @else
                            <li class="ps-3 border-start border-2 border-secondary">
                                <div class="small text-muted">Awaiting Buyer Confirmation</div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Confirm Modal (For Buyer) -->
@if($transaction->status === 'pending' && auth()->id() === $transaction->buyer_id)
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('transactions.confirm', $transaction) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="confirmModalLabel">
                            <i class="bi bi-shield-check text-success me-2"></i>Confirm Device Transfer
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">
                            By confirming, you acknowledge that you have physically inspected this <strong>{{ $transaction->device->brand }} {{ $transaction->device->model }}</strong>, verified its serial/IMEI, and accepted the agreed price of <strong>${{ number_format($transaction->device->agreed_price, 2) }}</strong>.
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Buyer Verification Note (Optional)</label>
                            <textarea name="buyer_notes" rows="3" class="form-control" placeholder="e.g. Device received in full working order as described. Serial number verified."></textarea>
                        </div>

                        <div class="alert alert-light border small text-muted">
                            <i class="bi bi-info-circle me-1"></i> An immutable TrustChain Digital Certificate of Transfer will be generated immediately upon confirmation.
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-lg me-1"></i>Confirm & Generate Certificate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Cancel Modal -->
@if($transaction->status === 'pending')
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('transactions.cancel', $transaction) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-danger" id="cancelModalLabel">
                            <i class="bi bi-x-circle me-2"></i>Cancel Transaction
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">
                            Are you sure you want to cancel transaction <strong>{{ $transaction->transaction_code }}</strong>? This action cannot be undone.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Reason for Cancellation</label>
                            <input type="text" name="cancel_reason" class="form-control" placeholder="e.g. Price dispute, device not available, buyer changed mind" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
