<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>@yield('title', 'Dashboard') — TrustChain</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <style>

        :root {
            --tc-primary: #2563eb;
            --tc-primary-dark: #1d4ed8;
            --tc-bg: #f6f8fc;
            --tc-sidebar: #ffffff;
            --tc-text: #172033;
            --tc-muted: #6b7280;
            --tc-border: #e7eaf0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--tc-bg);
            color: var(--tc-text);
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        /* =========================
           Sidebar
        ========================= */

        .tc-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: var(--tc-sidebar);
            border-right: 1px solid var(--tc-border);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease;
        }

        .tc-brand {
            height: 72px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--tc-border);
        }

        .tc-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--tc-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-right: 11px;
        }

        .tc-brand-name {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.4px;
        }

        .tc-nav {
            padding: 22px 14px;
            flex: 1;
        }

        .tc-nav-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0 12px;
            margin-bottom: 9px;
        }

        .tc-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            margin-bottom: 4px;
            color: #5f6775;
            text-decoration: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .tc-nav-link i {
            width: 20px;
            font-size: 17px;
        }

        .tc-nav-link:hover {
            color: var(--tc-primary);
            background: #eff6ff;
        }

        .tc-nav-link.active {
            color: var(--tc-primary);
            background: #eff6ff;
            font-weight: 600;
        }

        .tc-sidebar-bottom {
            padding: 16px 14px;
            border-top: 1px solid var(--tc-border);
        }

        /* =========================
           Main
        ========================= */

        .tc-main {
            margin-left: 250px;
            min-height: 100vh;
        }

        @guest
        .tc-main {
            margin-left: 0 !important;
        }
        @endguest

        /* =========================
           Navbar
        ========================= */

        .tc-navbar {
            height: 72px;
            background: white;
            border-bottom: 1px solid var(--tc-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .tc-mobile-menu {
            display: none;
            border: 0;
            background: transparent;
            font-size: 24px;
            color: var(--tc-text);
        }

        .tc-search {
            width: 280px;
            position: relative;
        }

        .tc-search i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .tc-search input {
            width: 100%;
            height: 40px;
            border: 1px solid var(--tc-border);
            border-radius: 9px;
            padding-left: 39px;
            font-size: 13px;
            outline: none;
            background: #fafbfc;
        }

        .tc-search input:focus {
            border-color: #93c5fd;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .tc-navbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .tc-icon-btn {
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: #606978;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .tc-icon-btn:hover {
            background: #f3f4f6;
        }

        .tc-notification-dot {
            position: absolute;
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
            top: 8px;
            right: 8px;
            border: 2px solid white;
        }

        .tc-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 6px;
        }

        .tc-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dbeafe;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .tc-user-name {
            font-size: 13px;
            font-weight: 600;
        }

        .tc-user-role {
            font-size: 11px;
            color: var(--tc-muted);
        }

        /* =========================
           Content
        ========================= */

        .tc-content {
            padding: 30px;
        }

        .tc-page-title {
            font-size: 25px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
        }

        .tc-page-subtitle {
            color: var(--tc-muted);
            font-size: 14px;
            margin-bottom: 28px;
        }

        /* =========================
           Mobile Overlay
        ========================= */

        .tc-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1040;
        }

        /* =========================
           Responsive
        ========================= */

        @media (max-width: 991.98px) {

            .tc-sidebar {
                transform: translateX(-100%);
            }

            .tc-sidebar.show {
                transform: translateX(0);
            }

            .tc-main {
                margin-left: 0;
            }

            .tc-mobile-menu {
                display: block;
            }

            .tc-overlay.show {
                display: block;
            }

            .tc-navbar {
                padding: 0 20px;
            }

            .tc-search {
                width: 220px;
            }

        }

        @media (max-width: 767.98px) {

            .tc-navbar {
                height: 64px;
            }

            .tc-search {
                display: none;
            }

            .tc-user-name,
            .tc-user-role {
                display: none;
            }

            .tc-navbar-right {
                gap: 8px;
            }

            .tc-content {
                padding: 20px 16px;
            }

            .tc-page-title {
                font-size: 22px;
            }

        }
        /* Dashboard Statistics */

.tc-stat-card {
    border: 1px solid var(--tc-border) !important;
    border-radius: 14px;
    background: #ffffff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.tc-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08) !important;
}

.tc-stat-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--tc-muted);
    margin-bottom: 8px;
}

.tc-stat-value {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.6px;
    line-height: 1;
}

.tc-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tc-stat-icon i {
    font-size: 19px;
}

    </style>

    @stack('styles')

</head>

<body>

    <!-- Sidebar -->
    @auth
        @include('components.sidebar')

        <!-- Mobile Overlay -->
        <div
            id="tcOverlay"
            class="tc-overlay"
            onclick="closeSidebar()"
        ></div>
    @endauth

    <!-- Main Application -->
    <main class="tc-main">

        <!-- Navbar -->
        @include('components.navbar')

        <!-- Page Content -->
        <section class="tc-content">

            @yield('content')

        </section>

    </main>

    <!-- Bootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

    <script>

        function openSidebar() {
            document
                .querySelector('.tc-sidebar')
                .classList.add('show');

            document
                .getElementById('tcOverlay')
                .classList.add('show');
        }

        function closeSidebar() {
            document
                .querySelector('.tc-sidebar')
                .classList.remove('show');

            document
                .getElementById('tcOverlay')
                .classList.remove('show');
        }

    </script>

    @stack('scripts')

</body>

</html>