<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- FontAwesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
    
    @stack('styles')
    <style>
        /* Hide the menu by default */
        .nav-treeview {
            display: none;
        }
        
        /* Show the menu when the checkbox is checked */
        .nav-item input:checked ~ .nav-treeview {
            display: block;
        }
        
        /* Optional: Adjust the dropdown arrow */
        .nav-link .right.fas.fa-angle-down {
            transition: transform 0.3s;
        }
        
        .nav-item input:checked ~ .nav-link .right.fas.fa-angle-down {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> {{ Auth::user()->first_name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle mr-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cogs mr-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Dynamically assign the dashboard route based on the user's role -->
            <a href="{{ auth()->user()->hasRole('Super Admin') ? route('dashboard') : (auth()->user()->hasRole('Retailer') ? route('retailer.dashboard') : route('lp.dashboard')) }}" class="brand-link">
                <span class="brand-text font-weight-light">Novatore</span>
            </a>
            <!-- Sidebar content -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Other menu items -->
        
                        <!-- Manage Users Dropdown -->
                        <li class="nav-item has-treeview">
                            <input type="checkbox" id="manageUsersToggle" class="d-none">
                            <a href="#" class="nav-link" onclick="document.getElementById('manageUsersToggle').checked = !document.getElementById('manageUsersToggle').checked;">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Users
                                    <i class="right fas fa-angle-down"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <!-- Manage Users -->
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>Users</p>
                                    </a>
                                </li>
                                <!-- Manage Roles -->
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                                <!-- Manage Permissions -->
                                <li class="nav-item">
                                    <a href="{{ route('permissions.index') }}" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
        
                        <!-- Manage Provinces Dropdown -->
                        <li class="nav-item">
                            <a href="{{ route('provinces.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-map"></i>
                                <p>
                                    Provinces
                                </p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview">
                            <input type="checkbox" id="logsToggle" class="d-none">
                            <a href="#" class="nav-link" onclick="document.getElementById('logsToggle').checked = !document.getElementById('logsToggle').checked;">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Logs
                                    <i class="right fas fa-angle-down"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('logs.index') }}" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>User Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('province-logs.index') }}" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>Province Logs</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('retailers.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Retailer Management</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; {{ date('Y') }} <a href="#">Your Company</a>.</strong> All rights reserved.
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
