@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="tc-page-title">
            Dashboard
        </h1>
        <p class="tc-page-subtitle mb-0">
            Welcome back, {{ auth()->user()->name }}. Here's an overview of your TrustChain activity.
        </p>
    </div>

    <!-- Statistics -->
    <div class="row g-4">

        <!-- Transactions -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm tc-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-2 tc-stat-label">
                                Total Transactions
                            </p>
                            <h2 class="fw-bold mb-0 tc-stat-value">
                                {{ $totalTransactions }}
                            </h2>
                        </div>
                        <div class="tc-stat-icon" style="background:#eff6ff; color:#2563eb;">
                            <i class="bi bi-arrow-left-right fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm tc-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-2 tc-stat-label">
                                Completed
                            </p>
                            <h2 class="fw-bold mb-0 tc-stat-value">
                                {{ $confirmedTransactions }}
                            </h2>
                        </div>
                        <div class="tc-stat-icon" style="background:#ecfdf5; color:#059669;">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm tc-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-2 tc-stat-label">
                                Pending
                            </p>
                            <h2 class="fw-bold mb-0 tc-stat-value">
                                {{ $pendingTransactions }}
                            </h2>
                        </div>
                        <div class="tc-stat-icon" style="background:#fffbeb; color:#d97706;">
                            <i class="bi bi-clock fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificates -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm tc-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-2 tc-stat-label">
                                Certificates
                            </p>
                            <h2 class="fw-bold mb-0 tc-stat-value">
                                {{ $certificatesCount }}
                            </h2>
                        </div>
                        <div class="tc-stat-icon" style="background:#f5f3ff; color:#7c3aed;">
                            <i class="bi bi-patch-check fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Actions & Trust Overview -->
    <div class="row g-4 mt-1">

        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">
                            Quick Actions
                        </h5>
                        <p class="text-secondary small mb-4">
                            Start managing your TrustChain transactions and verification records.
                        </p>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary d-inline-flex align-items-center px-4 py-2">
                            <i class="bi bi-plus-circle me-2"></i>
                            Create Transaction
                        </a>
                        <a href="{{ route('certificates.verify') }}" class="btn btn-outline-secondary d-inline-flex align-items-center px-4 py-2">
                            <i class="bi bi-qr-code-scan me-2"></i>
                            Verify Certificate
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-light d-inline-flex align-items-center px-4 py-2 border">
                            <i class="bi bi-list-ul me-2"></i>
                            View All Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">
                        Trust Overview
                    </h5>
                    <p class="text-secondary small mb-3">
                        Your verifiable activity recorded on TrustChain.
                    </p>
                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary">
                            <i class="bi bi-file-earmark-lock me-2 text-primary"></i>Evidence Files Stored
                        </span>
                        <span class="badge bg-light text-dark fs-6 px-3 py-2 border">{{ $evidenceCount }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary">
                            <i class="bi bi-shield-check me-2 text-success"></i>Confirmed Transfers
                        </span>
                        <span class="badge bg-light text-dark fs-6 px-3 py-2 border">{{ $verifiedTransactionsCount }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary">
                            <i class="bi bi-patch-check me-2 text-purple" style="color: #7c3aed;"></i>Certificates Issued
                        </span>
                        <span class="badge bg-light text-dark fs-6 px-3 py-2 border">{{ $certificatesCount }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Transactions -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">
                        Recent Transactions
                    </h5>
                    <p class="text-secondary small mb-0">
                        Your latest TrustChain transfers and exchange records.
                    </p>
                </div>
                @if($recentTransactions->isNotEmpty())
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                @endif
            </div>

            @if($recentTransactions->isNotEmpty())
                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="border-0">Code</th>
                                <th scope="col" class="border-0">Device</th>
                                <th scope="col" class="border-0">Your Role</th>
                                <th scope="col" class="border-0">Agreed Price</th>
                                <th scope="col" class="border-0">Status</th>
                                <th scope="col" class="border-0">Date</th>
                                <th scope="col" class="border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            {{ $transaction->transaction_code }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $transaction->device->brand ?? 'Device' }} {{ $transaction->device->model ?? '' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $transaction->device->device_type ?? 'Hardware' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if(auth()->id() === $transaction->seller_id)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Seller
                                            </span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                <i class="bi bi-box-arrow-in-down-left me-1"></i>Buyer
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        @if($transaction->device && $transaction->device->agreed_price)
                                            ${{ number_format($transaction->device->agreed_price, 2) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->status === 'confirmed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle-fill me-1"></i>Confirmed
                                            </span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-clock-history me-1"></i>Pending
                                            </span>
                                        @elseif($transaction->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="bi bi-x-circle me-1"></i>Cancelled
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-secondary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt fs-1 text-secondary opacity-50"></i>
                    <h6 class="mt-3 fw-semibold">
                        No transactions yet
                    </h6>
                    <p class="text-secondary small mb-3">
                        Create your first device transaction to establish a verifiable chain of custody.
                    </p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create First Transaction
                    </a>
                </div>
            @endif

        </div>
    </div>

</div>

@endsection