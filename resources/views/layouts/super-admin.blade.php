<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Super Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #f8fafc;
            --sidebar-bg: #1e293b;
            --navbar-bg: #ffffff;
            --card-bg: #ffffff;
            --accent-color: #6366f1;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        [data-bs-theme="dark"] {
            --primary-bg: #0f172a;
            --sidebar-bg: #1e293b;
            --navbar-bg: #1e293b;
            --card-bg: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.3);
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
            width: 280px;
            background-color: var(--sidebar-bg);
            color: white;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 2rem 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: block;
        }

        .sidebar-link {
            padding: 1rem 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left-color: var(--accent-color);
        }

        .sidebar-link i {
            margin-right: 1rem;
            width: 20px;
        }

        #page-content-wrapper {
            width: 100%;
            overflow-x: hidden;
        }

        .navbar {
            background: var(--navbar-bg);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .content-area {
            padding: 2rem;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #4f46e5;
            border-color: #4f46e5;
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
    </style>
    @yield('styles')
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <a href="#" class="sidebar-brand">
                <i class="fas fa-bolt me-2"></i>BillingPro
            </a>
            <div class="list-group list-group-flush">
                <a href="{{ route('super-admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('super-admin.businesses.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.businesses.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase"></i> Businesses
                </a>
                <a href="{{ route('super-admin.subscriptions.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.subscriptions.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> Subscriptions
                </a>
                <a href="#" class="sidebar-link">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button class="btn btn-link d-lg-none" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="theme-toggle me-3" id="themeToggle" title="Toggle Dark/Light Mode">
                            <i class="fas fa-moon"></i>
                        </div>
                        <span class="me-3 d-none d-md-inline">Super Admin</span>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                @if(auth()->user()->profile_photo)
                                    <img src="{{ asset(auth()->user()->profile_photo) }}" alt="Admin" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name=Super+Admin" alt="Admin" class="rounded-circle" width="35">
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item {{ request()->routeIs('super-admin.profile.*') ? 'active' : '' }}" href="{{ route('super-admin.profile.index') }}"><i class="fas fa-user-circle me-2 text-muted"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2 text-muted"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Theme Logic
            const themeToggle = $('#themeToggle');
            const htmlElement = $('html');
            const themeIcon = themeToggle.find('i');

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

            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#sidebar-wrapper").toggleClass("d-none");
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
