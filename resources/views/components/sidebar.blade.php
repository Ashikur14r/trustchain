<aside class="tc-sidebar">

    <div class="tc-brand">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-dark">
            <div class="tc-brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <span class="tc-brand-name">
                TrustChain
            </span>
        </a>
    </div>

    <nav class="tc-nav">

        <div class="tc-nav-label">
            Main
        </div>

        <a
            href="{{ route('dashboard') }}"
            class="tc-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>

        <a
            href="{{ route('transactions.index') }}"
            class="tc-nav-link {{ request()->routeIs('transactions.index', 'transactions.show') ? 'active' : '' }}"
        >
            <i class="bi bi-arrow-left-right"></i>
            <span>Transactions</span>
        </a>

        <a
            href="{{ route('transactions.create') }}"
            class="tc-nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}"
        >
            <i class="bi bi-plus-circle"></i>
            <span>Create Transaction</span>
        </a>

        <div class="tc-nav-label mt-4">
            Trust & Verification
        </div>

        <a
            href="{{ route('evidence.index') }}"
            class="tc-nav-link {{ request()->routeIs('evidence.*') ? 'active' : '' }}"
        >
            <i class="bi bi-images"></i>
            <span>Evidence</span>
        </a>

        <a
            href="{{ route('certificates.index') }}"
            class="tc-nav-link {{ request()->routeIs('certificates.index', 'certificates.show') ? 'active' : '' }}"
        >
            <i class="bi bi-patch-check"></i>
            <span>Certificates</span>
        </a>

        <a
            href="{{ route('certificates.verify') }}"
            class="tc-nav-link {{ request()->routeIs('certificates.verify*') ? 'active' : '' }}"
        >
            <i class="bi bi-qr-code-scan"></i>
            <span>Verify Certificate</span>
        </a>

        <div class="tc-nav-label mt-4">
            Account
        </div>

        <a
            href="{{ route('profile.edit') }}"
            class="tc-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
        >
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>

    </nav>

    <div class="tc-sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
            @csrf
            <button type="submit" class="tc-nav-link text-start w-100 bg-transparent border-0" style="cursor: pointer;">
                <i class="bi bi-box-arrow-right text-danger"></i>
                <span class="text-danger">Logout</span>
            </button>
        </form>
    </div>

</aside>