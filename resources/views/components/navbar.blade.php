<header class="tc-navbar">

    <div class="d-flex align-items-center gap-3">

        @auth
            <button
                class="tc-mobile-menu"
                type="button"
                onclick="openSidebar()"
                aria-label="Open navigation"
            >
                <i class="bi bi-list"></i>
            </button>
        @else
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark fw-bold fs-5">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width:32px; height:32px; background:#2563eb;">
                    <i class="bi bi-shield-check"></i>
                </div>
                TrustChain
            </a>
        @endauth

        <form action="{{ route('transactions.index') }}" method="GET" class="tc-search m-0">
            <i class="bi bi-search"></i>
            <input
                type="search"
                name="search"
                placeholder="Search transactions..."
                value="{{ request('search') }}"
            >
        </form>

    </div>


    <div class="tc-navbar-right">

        <a href="{{ route('certificates.verify') }}" class="btn btn-sm btn-light border d-none d-md-inline-flex align-items-center text-secondary">
            <i class="bi bi-qr-code-scan me-1 text-primary"></i>
            Verify Portal
        </a>

        @auth
            <div class="dropdown">
                <div class="tc-user dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                    <div class="tc-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="d-none d-sm-block">
                        <div class="tc-user-name">
                            {{ auth()->user()->name ?? 'User' }}
                        </div>
                        <div class="tc-user-role">
                            Account Holder
                        </div>
                    </div>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-semibold text-dark small">{{ auth()->user()->name ?? '' }}</div>
                        <div class="text-muted small">{{ auth()->user()->email ?? '' }}</div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2 me-2 text-secondary"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('transactions.index') }}">
                            <i class="bi bi-arrow-left-right me-2 text-secondary"></i>My Transactions
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2 text-secondary"></i>Profile Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">
                    Register
                </a>
            </div>
        @endauth

    </div>

</header>