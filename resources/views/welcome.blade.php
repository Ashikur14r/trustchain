<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TrustChain — Verifiable Device Ownership & Chain of Custody</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        :root {
            --tc-primary: #2563eb;
            --tc-primary-dark: #1d4ed8;
            --tc-bg: #f8fafc;
            --tc-text: #0f172a;
        }
        body {
            background-color: var(--tc-bg);
            color: var(--tc-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .hero-section {
            padding: 100px 0 80px 0;
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }
        .tc-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .tc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08) !important;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark fs-4" href="{{ url('/') }}">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width:36px; height:36px; background:#2563eb;">
                    <i class="bi bi-shield-check"></i>
                </div>
                TrustChain
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ route('certificates.verify') }}">
                            <i class="bi bi-qr-code-scan me-1"></i>Verify Certificate
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-primary px-4" href="{{ route('dashboard') }}">
                                <i class="bi bi-grid-1x2 me-1"></i>Go to Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold" href="{{ route('login') }}">Log in</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary px-4" href="{{ route('register') }}">Get Started</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container" style="max-width: 900px;">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 mb-3">
                <i class="bi bi-patch-check me-1"></i>Next-Gen Hardware Chain of Custody
            </span>

            <h1 class="display-4 fw-bold text-dark mb-3" style="letter-spacing: -1px;">
                Eliminate Fraud in Used Electronics Transfers
            </h1>

            <p class="lead text-secondary mb-4 mx-auto" style="max-width: 720px;">
                TrustChain establishes immutable proof of device condition, accessories, serial numbers, and two-sided owner confirmation — backed by cryptographic SHA-256 evidence.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm">
                        <i class="bi bi-speedometer2 me-2"></i>Access Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm">
                        <i class="bi bi-shield-plus me-2"></i>Create Free Account
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 fw-semibold">
                        Sign In
                    </a>
                @endauth
                <a href="{{ route('certificates.verify') }}" class="btn btn-light border btn-lg px-4 fw-semibold text-secondary">
                    <i class="bi bi-qr-code-scan me-2 text-primary"></i>Verify Certificate
                </a>
            </div>

            <!-- Demo Login Box -->
            <div class="card border-0 shadow-sm mt-5 text-start mx-auto p-3" style="max-width: 520px; background: #ffffff;">
                <div class="small fw-bold text-dark mb-2"><i class="bi bi-key-fill text-primary me-1"></i>Quick Demo Credentials:</div>
                <div class="d-flex justify-content-between small text-secondary py-1 border-bottom">
                    <span>Seller Account:</span>
                    <span class="font-monospace text-dark">seller@trustchain.com / password</span>
                </div>
                <div class="d-flex justify-content-between small text-secondary py-1">
                    <span>Buyer Account:</span>
                    <span class="font-monospace text-dark">buyer@trustchain.com / password</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Key Pillars Section -->
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">How TrustChain Secures Transfers</h2>
                <p class="text-secondary small">Four layers of tamper-proof verification built for peace of mind.</p>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm tc-card h-100 p-3 bg-white">
                        <div class="card-body">
                            <div class="rounded-3 bg-primary-subtle text-primary d-inline-flex p-3 mb-3">
                                <i class="bi bi-fingerprint fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark">SHA-256 Evidence</h5>
                            <p class="text-secondary small mb-0">
                                Condition photos, receipt scans, and diagnostic logs are fingerprinted with cryptographic hashes upon upload.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm tc-card h-100 p-3 bg-white">
                        <div class="card-body">
                            <div class="rounded-3 bg-success-subtle text-success d-inline-flex p-3 mb-3">
                                <i class="bi bi-check2-all fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Dual Sign-Off</h5>
                            <p class="text-secondary small mb-0">
                                Seller documents the device condition, and the buyer inspects and approves before ownership certificate issues.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm tc-card h-100 p-3 bg-white">
                        <div class="card-body">
                            <div class="rounded-3 bg-purple-subtle text-purple d-inline-flex p-3 mb-3" style="background: #f5f3ff; color: #7c3aed;">
                                <i class="bi bi-patch-check fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Digital Certificates</h5>
                            <p class="text-secondary small mb-0">
                                Unique certificate numbers and verification tokens provide permanent, printable proof of title transfer.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm tc-card h-100 p-3 bg-white">
                        <div class="card-body">
                            <div class="rounded-3 bg-warning-subtle text-warning-emphasis d-inline-flex p-3 mb-3">
                                <i class="bi bi-qr-code-scan fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Public Verification</h5>
                            <p class="text-secondary small mb-0">
                                Anyone can scan the QR code or look up the certificate ID in the public verification portal to confirm validity.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 text-center text-secondary small">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} TrustChain. Secure Hardware Verification Network. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
