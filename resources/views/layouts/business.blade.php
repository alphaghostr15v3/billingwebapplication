<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Business Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-bg: #f3f4f6;
            --sidebar-bg: #ffffff;
            --navbar-bg: #ffffff;
            --card-bg: #ffffff;
            --accent-color: #4f46e5;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --sidebar-width: 260px;
            --card-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        [data-bs-theme="dark"] {
            --primary-bg: #111827;
            --sidebar-bg: #1f2937;
            --navbar-bg: #1f2937;
            --card-bg: #1f2937;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border-color: #374151;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.3);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-color);
            text-decoration: none;
            display: block;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-link {
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            margin: 0.25rem 1rem;
            border-radius: 0.5rem;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--accent-color);
        }

        .sidebar-link i {
            margin-right: 0.75rem;
            width: 20px;
            font-size: 1.1rem;
        }

        #page-content-wrapper {
            flex: 1;
            overflow-x: hidden;
        }

        .navbar {
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
        }

        .content-area {
            padding: 2rem;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .theme-toggle {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            color: var(--text-muted);
        }

        .theme-toggle:hover {
            background: rgba(0,0,0,0.05);
            color: var(--accent-color);
        }

        [data-bs-theme="dark"] .theme-toggle:hover {
            background: rgba(255,255,255,0.05);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <a href="#" class="sidebar-brand">
                <i class="fas fa-file-invoice-dollar me-2"></i>BillingCore
            </a>
            <div class="mt-3">
                <a href="{{ route('business.dashboard') }}" class="sidebar-link {{ request()->routeIs('business.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('business.invoices.index') }}" class="sidebar-link {{ request()->routeIs('business.invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> Invoices
                </a>
                <a href="{{ route('business.customers.index') }}" class="sidebar-link {{ request()->routeIs('business.customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Customers
                </a>
                <a href="{{ route('business.products.index') }}" class="sidebar-link {{ request()->routeIs('business.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="{{ route('business.expenses.index') }}" class="sidebar-link {{ request()->routeIs('business.expenses.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i> Expenses
                </a>
                <a href="{{ route('business.reports.index') }}" class="sidebar-link {{ request()->routeIs('business.reports.index') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="{{ route('business.reports.gst') }}" class="sidebar-link {{ request()->routeIs('business.reports.gst') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i> GST Reports
                </a>
                <a href="{{ route('business.reports.gstr1') }}" class="sidebar-link {{ request()->routeIs('business.reports.gstr1') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i> GSTR-1 Report
                </a>
                <a href="{{ route('business.subscriptions.index') }}" class="sidebar-link {{ request()->routeIs('business.subscriptions.*') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i> Subscriptions
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button class="btn btn-link d-lg-none" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="theme-toggle me-3" id="themeToggle" title="Toggle Dark/Light Mode">
                            <i class="fas fa-moon"></i>
                        </div>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                @if(auth()->user()->profile_photo)
                                    <img src="{{ asset(auth()->user()->profile_photo) }}" alt="User" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" alt="User" class="rounded-circle me-2" width="32">
                                @endif
                                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item {{ request()->routeIs('business.profile.*') ? 'active' : '' }}" href="{{ route('business.profile.index') }}"><i class="fas fa-user-circle me-2 text-muted"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2 text-muted"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Theme Logic
            const themeToggle = $('#themeToggle');
            const htmlElement = $('html');
            const themeIcon = themeToggle.find('i');

            // Initialize theme
            const currentTheme = localStorage.getItem('theme') || 'light';
            htmlElement.attr('data-bs-theme', currentTheme);
            updateIcon(currentTheme);

            themeToggle.click(function() {
                const newTheme = htmlElement.attr('data-bs-theme') === 'dark' ? 'light' : 'dark';
                htmlElement.attr('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });

            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.removeClass('fa-moon').addClass('fa-sun');
                } else {
                    themeIcon.removeClass('fa-sun').addClass('fa-moon');
                }
            }

            // Mobile toggle
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#sidebar-wrapper").toggleClass("d-none");
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
