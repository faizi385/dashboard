<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <!-- FontAwesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Other plugins -->
    
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
          <!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
    <!-- User Dropdown -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user"></i> {{ Auth::user()->first_name }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user-circle mr-2"></i> Profile</a></li>
            <li><a class="dropdown-item" href="{{ route('settings') }}"><i class="fas fa-cogs mr-2"></i> Settings</a></li>
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

        </nav><aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Dynamically assign the dashboard route based on the user's role -->
            <a style="text-decoration: none" href="{{ auth()->user()->hasRole('Super Admin') ? route('dashboard') : (auth()->user()->hasRole('Retailer') ? route('retailer.dashboard') : route('lp.dashboard')) }}" class="brand-link">
                <span class="brand-text font-weight-light">Novatore</span>
            </a>
        
            <!-- Sidebar content -->
            <div class="sidebar">
                <!-- SidebarSearch Form -->
                <div class="form-inline mt-3">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>
        
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        
                        <!-- Manage Users Dropdown (Visible to both Super Admin and Retailers) -->
                        <li class="nav-item has-treeview {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Users
                                    <i class="right fas fa-angle-down"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}" class="nav-link {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>Users</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link {{ Route::currentRouteName() == 'roles.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
        
                                <!-- Permissions (Visible only to Super Admin) -->
                                @if(auth()->user()->hasRole('Super Admin'))
                                <li class="nav-item">
                                    <a href="{{ route('permissions.index') }}" class="nav-link {{ Route::currentRouteName() == 'permissions.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
        
                        <!-- Manage Provinces (Visible only to Super Admin) -->
                        @if(auth()->user()->hasRole('Super Admin'))
                        <li class="nav-item">
                            <a href="{{ route('provinces.index') }}" class="nav-link {{ Route::currentRouteName() == 'provinces.index' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-map"></i>
                                <p>Provinces</p>
                            </a>
                        </li>
        
                        <!-- Logs Dropdown (Visible only to Super Admin) -->
                        <li class="nav-item has-treeview {{ request()->is('logs*') || request()->is('province-logs*') || request()->is('retailer-logs*') || request()->is('lp-logs*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('logs*') || request()->is('province-logs*') || request()->is('retailer-logs*') || request()->is('lp-logs*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Logs
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'logs.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>User Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('province-logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'province-logs.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>Province Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('retailer.logs') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.logs' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>Retailer Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('lp.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'lp.logs.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>LP Logs</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
        
                        <!-- Retailer Management (Visible only to Super Admin) -->
                        <li class="nav-item">
                            <a href="{{ route('retailer.index') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.index' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Retailer Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lp.management') }}" class="nav-link {{ request()->is('lp/management*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    LP Management
                                    <span class="right badge badge-danger">New</span>
                                </p>
                            </a>
                        </li>
                        @endif
        
                        <!-- Manage Info (Visible only to LPs) -->
                        @if(auth()->user()->hasRole('LP'))
                        <li class="nav-item {{ request()->is('manage-info*') ? 'menu-open' : '' }}">
                            <a href="{{ route('manage-info.index') }}" class="nav-link {{ request()->is('manage-info*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-info-circle"></i>
                                <p>Manage Info</p>
                            </a>
                        </li>
                        @endif
        
                        <!-- LP Management (Visible to all roles) -->
                        
                    </ul>
                </nav>
            </div>
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
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
  
    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <!-- Latest jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Latest DataTables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>


    
   
    <!-- Other plugins -->
    @stack('scripts')
</body>
</html>
