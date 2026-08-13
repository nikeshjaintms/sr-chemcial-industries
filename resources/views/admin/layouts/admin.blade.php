<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SRCIL ERP Admin Portal — SR Chemical Industries Limited')</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome & Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    
    <style>
        :root {
            --sidebar-width: 270px;
            --brand-blue: #0F5286;
            --brand-green: #67B346;
            --dark-navy: #0F172A;
            --sidebar-bg: #1E293B;
            --body-bg: #F1F5F9;
            --card-border: #E2E8F0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--body-bg);
            color: #334155;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            color: #F8FAFC;
            z-index: 1040;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand-box {
            padding: 22px 16px;
            background: rgba(15, 23, 42, 0.4);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
        }

        .sidebar-brand-logo {
            background: transparent !important;
            padding: 0 !important;
            border-radius: 0 !important;
            display: inline-block;
            box-shadow: none !important;
            transition: transform 0.2s ease;
        }

        .sidebar-brand-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-brand-logo img {
            height: 70px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(255, 255, 255, 0.35));
        }

        .sidebar-menu {
            padding: 20px 14px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-category-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            font-weight: 700;
            margin: 16px 12px 8px 12px;
        }

        .sidebar-menu .nav-link {
            color: #94A3B8;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .sidebar-menu .nav-link i {
            font-size: 18px;
            width: 28px;
            transition: transform 0.2s ease;
        }

        .sidebar-menu .nav-link:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.07);
        }

        .sidebar-menu .nav-link:hover i {
            transform: translateX(3px);
            color: var(--brand-green);
        }

        .sidebar-menu .nav-link.active {
            color: #FFFFFF;
            background: linear-gradient(135deg, var(--brand-blue) 0%, #1D4ED8 100%);
            box-shadow: 0 4px 14px rgba(15, 82, 134, 0.4);
            font-weight: 600;
        }

        .sidebar-menu .nav-link.active i {
            color: #FFFFFF;
        }

        /* Main Section Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Topbar Header */
        .topbar {
            background-color: #FFFFFF;
            height: 72px;
            border-bottom: 1px solid var(--card-border);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .page-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--dark-navy);
            margin: 0;
        }

        .search-topbar {
            position: relative;
            width: 300px;
        }

        .search-topbar input {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 8px 16px 8px 40px;
            font-size: 13px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .search-topbar input:focus {
            background-color: #FFFFFF;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(15, 82, 134, 0.12);
            outline: none;
        }

        .search-topbar i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 14px;
        }

        /* Content Body */
        .content-body {
            padding: 30px;
            flex-grow: 1;
        }

        /* Cards & Components */
        .card-custom {
            background: #FFFFFF;
            border-radius: 14px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        /* Stat Cards */
        .stat-card-gradient {
            border: none;
            border-radius: 16px;
            color: #FFFFFF;
            padding: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card-gradient:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.14);
        }

        .stat-card-blue { background: linear-gradient(135deg, #0F5286 0%, #1D4ED8 100%); }
        .stat-card-green { background: linear-gradient(135deg, #2E7D32 0%, #67B346 100%); }
        .stat-card-purple { background: linear-gradient(135deg, #6A1B9A 0%, #AB47BC 100%); }
        .stat-card-orange { background: linear-gradient(135deg, #E65100 0%, #FB8C00 100%); }
        .stat-card-cyan { background: linear-gradient(135deg, #00838F 0%, #00ACC1 100%); }

        .stat-icon-wrap {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 700;
            margin-top: 12px;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Buttons */
        .btn-brand-primary {
            background-color: var(--brand-blue);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 9px 18px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-brand-primary:hover {
            background-color: #0c436d;
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(15, 82, 134, 0.3);
        }

        .btn-brand-green {
            background-color: var(--brand-green);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 9px 18px;
            font-size: 14px;
        }

        .btn-brand-green:hover {
            background-color: #589b3c;
            color: #FFFFFF;
        }

        /* Tables */
        .table-custom th {
            background-color: #F8FAFC;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            padding: 14px 20px;
            border-bottom: 1px solid var(--card-border);
        }

        .table-custom td {
            padding: 14px 20px;
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #F1F5F9;
        }

        .table-custom tr:hover td {
            background-color: #F8FAFC;
        }

        /* Footer */
        .admin-footer {
            background: #FFFFFF;
            border-top: 1px solid var(--card-border);
            padding: 18px 30px;
            text-align: center;
            font-size: 13px;
            color: #64748B;
        }

        /* Responsive Mobile Toggle */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Container -->
    <div class="sidebar" id="adminSidebar">
        <div class="sidebar-brand-box">
            <div class="sidebar-brand-logo">
                <img src="{{ asset('assets/img/added/blue-logo.png') }}" alt="SR Chemical Logo">
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-category-title">Core Management</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-chart-pie me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.bulk-images') && !request()->routeIs('admin.products.duplicate-images') && !request()->routeIs('admin.products.bulk-pdf') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="fa-solid fa-vials me-2"></i> Product Catalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.bulk-pdf') ? 'active' : '' }}" href="{{ route('admin.products.bulk-pdf') }}">
                        <i class="fa-solid fa-file-pdf me-2"></i> Bulk PDF Upload
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.bulk-images') ? 'active' : '' }}" href="{{ route('admin.products.bulk-images') }}">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Bulk Image Manager
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.duplicate-images') ? 'active' : '' }}" href="{{ route('admin.products.duplicate-images') }}">
                        <i class="fa-solid fa-images me-2"></i> Duplicate Images Replace
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
                        <i class="fa-solid fa-photo-film me-2"></i> Media Library
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="fa-solid fa-boxes-stacked me-2"></i> Categories
                    </a>
                </li>
            </ul>



            <div class="menu-category-title mt-3">System</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank">
                        <i class="fa-solid fa-globe me-2"></i> Visit Live Website
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-power-off me-2"></i> Sign Out
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none border" id="sidebarToggle">
                    <i class="fa-solid fa-bars text-18"></i>
                </button>
                <h1 class="page-header-title">@yield('page_title', 'Dashboard')</h1>
            </div>

            <!-- Right Actions & Profile -->
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.products.create') }}" class="btn btn-brand-primary btn-sm d-none d-sm-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add New Product
                </a>
                
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Open Public Website">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Website
                </a>

                <div class="vr mx-1 d-none d-sm-block"></div>

                <!-- Admin Profile Info -->
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center font-bold" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="font-semibold text-dark text-13 leading-1">{{ Auth::user()->name ?? 'Administrator' }}</div>
                        <div class="text-11 text-muted">{{ Auth::user()->email ?? 'admin@srchemical.com' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Content -->
        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="admin-footer">
            © {{ date('Y') }} <strong>SR Chemical Industries Limited</strong>. All rights reserved. Enterprise ERP Portal.
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3-7-1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarToggle').click(function() {
                $('#adminSidebar').toggleClass('mobile-open');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
