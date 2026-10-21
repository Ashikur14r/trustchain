@extends('layouts.app')

@section('title', 'Transactions')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="tc-page-title mb-1">
                Transactions
            </h1>
            <p class="tc-page-subtitle mb-0">
                Track, manage, and verify all your TrustChain device transfers.
            </p>
        </div>

        <a href="{{ route('transactions.create') }}" class="btn btn-primary d-inline-flex align-items-center">
            <i class="bi bi-plus-circle me-2"></i>
            Create Transaction
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <!-- Status Filter Pills -->
                <div class="col-12 col-lg-8">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('transactions.index', request()->except('status', 'page')) }}" 
                           class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-light border' }}">
                            All Statuses
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
                           class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-light border' }}">
                            <i class="bi bi-clock me-1"></i>Pending
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('page'), ['status' => 'confirmed'])) }}" 
                           class="btn btn-sm {{ request('status') === 'confirmed' ? 'btn-success' : 'btn-light border' }}">
                            <i class="bi bi-check-circle me-1"></i>Confirmed
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" 
                           class="btn btn-sm {{ request('status') === 'cancelled' ? 'btn-danger' : 'btn-light border' }}">
                            <i class="bi bi-x-circle me-1"></i>Cancelled
                        </a>

                        <div class="vr mx-2 d-none d-sm-block"></div>

                        <!-- Role Filter -->
                        <a href="{{ route('transactions.index', array_merge(request()->except('page'), ['role' => 'seller'])) }}" 
                           class="btn btn-sm {{ request('role') === 'seller' ? 'btn-dark' : 'btn-light border' }}">
                            Selling
                        </a>
                        <a href="{{ route('transactions.index', array_merge(request()->except('page'), ['role' => 'buyer'])) }}" 
                           class="btn btn-sm {{ request('role') === 'buyer' ? 'btn-dark' : 'btn-light border' }}">
                            Buying
                        </a>
                    </div>
                </div>

                <!-- Search -->
                <div class="col-12 col-lg-4">
                    <form action="{{ route('transactions.index') }}" method="GET" class="d-flex gap-2">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif
                        <input type="search" name="search" class="form-control form-control-sm" 
                               placeholder="Search code, device, IMEI..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request()->hasAny(['search', 'status', 'role']))
                            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear Filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($transactions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4 py-3 border-0">Code</th>
                                <th scope="col" class="py-3 border-0">Device Details</th>
                                <th scope="col" class="py-3 border-0">Role & Counterparty</th>
                                <th scope="col" class="py-3 border-0">Agreed Price</th>
                                <th scope="col" class="py-3 border-0">Status</th>
                                <th scope="col" class="py-3 border-0">Created</th>
                                <th scope="col" class="pe-4 py-3 border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $t)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('transactions.show', $t) }}" class="fw-bold text-decoration-none font-monospace text-primary">
                                            {{ $t->transaction_code }}
                                        </a>
                                        @if($t->certificate)
                                            <div>
                                                <span class="badge bg-purple-subtle text-purple border" style="background: #f5f3ff; color: #7c3aed; border-color: #ddd6fe !important;">
                                                    <i class="bi bi-patch-check-fill me-1"></i>Certified
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $t->device->brand ?? 'Unknown' }} {{ $t->device->model ?? 'Device' }}
                                        </div>
                                        <div class="text-muted small">
                                            <span>{{ $t->device->device_type ?? 'Hardware' }}</span>
                                            @if($t->device && $t->device->serial_number)
                                                <span class="ms-1 font-monospace">· S/N: {{ Str::limit($t->device->serial_number, 12) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(auth()->id() === $t->seller_id)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle mb-1">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Seller
                                            </span>
                                            <div class="small text-muted">
                                                Buyer: <span class="text-dark">{{ $t->buyer->name ?? 'Unassigned' }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                                                <i class="bi bi-box-arrow-in-down-left me-1"></i>Buyer
                                            </span>
                                            <div class="small text-muted">
                                                Seller: <span class="text-dark">{{ $t->seller->name ?? 'User' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        @if($t->device && $t->device->agreed_price)
                                            ${{ number_format($t->device->agreed_price, 2) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($t->status === 'confirmed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle-fill me-1"></i>Confirmed
                                            </span>
                                        @elseif($t->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-clock-history me-1"></i>Pending
                                            </span>
                                        @elseif($t->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="bi bi-x-circle me-1"></i>Cancelled
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $t->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-primary">
                                            View Dossier
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="p-3 border-top">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                    <h5 class="mt-3 fw-semibold">No transactions found</h5>
                    <p class="text-secondary small mb-3">
                        @if(request()->hasAny(['search', 'status', 'role']))
                            No records match your active search or filter criteria.
                        @else
                            You haven't initiated or received any device transactions yet.
                        @endif
                    </p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Create New Transaction
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
