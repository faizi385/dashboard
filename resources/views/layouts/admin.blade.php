<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Ivy+Mode:wght@400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
    <style>
   

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
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a style="text-decoration: none" href="{{ auth()->user()->hasRole('Super Admin') ? route('dashboard') : (auth()->user()->hasRole('LP') ? route('lp.dashboard') : route('retailer.dashboard')) }}" class="brand-link">
                <span class="brand-text font-weight-light">Novatore</span>
            </a>
        
            <div class="sidebar">
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
        
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
                        <!-- Manage Users Dropdown (Visible to both Super Admin and Retailers) -->
                        <li class="nav-item has-treeview {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Users
                                    <i class="right fas fa-angle-left"></i>
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
                        <li class="nav-item has-treeview 
                        {{ request()->is('logs*') || request()->is('province-logs*') || request()->is('retailer-logs*') || request()->is('lp-logs*') || request()->is('offer-logs*') || request()->is('carveout-logs*') || request()->is('report-logs*') || 
                          Route::currentRouteName() == 'retailer.logs' || Route::currentRouteName() == 'lp.logs.index' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link 
                            {{ request()->is('logs*') || request()->is('province-logs*') || request()->is('retailer-logs*') || request()->is('lp-logs*') || request()->is('offer-logs*') || request()->is('carveout-logs*') || request()->is('report-logs*') ? 'active' : '' }}">
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
                            <li class="nav-item">
                                <a href="{{ route('offer.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'offer.logs.index' ? 'active' : '' }}">
                                    <i class="nav-icon"></i>
                                    <p>Deal Logs</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('carveout.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'carveout.logs.index' ? 'active' : '' }}">
                                    <i class="nav-icon"></i>
                                    <p>Carveout Logs</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'report.logs.index' ? 'active' : '' }}">
                                    <i class="nav-icon"></i>
                                    <p>Report Logs</p>
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
                            <a href="{{ route('lp.management') }}" 
                               class="nav-link {{ (request()->is('lp/management*') || Route::currentRouteName() == 'lp.management' || Route::currentRouteName() == 'lp.show') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    LP Management
                                    {{-- <span class="right badge badge-danger">New</span> --}}
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
        
                        <!-- Offers Tab (Visible to Super Admin and LPs) -->
                      
                    
                    <!-- Offers Tab (Visible to Super Admin and LPs) -->
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('LP'))
                    <li class="nav-item">
                        <a href="{{ route('offers.index') }}"
                           class="nav-link {{ (Route::currentRouteName() == 'offers.index' && !request()->get('from_lp_show')) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tag"></i>
                            <p>Deals</p>
                        </a>
                    </li>
                    @endif
                    
                        
                        
                    
                    
                    
                    
        
                        <!-- Carveouts Tab (Visible to Super Admin and LPs) -->
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('LP'))
                        <li class="nav-item">
                            <a href="{{ route('carveouts.index', ['lp_id' => auth()->user()->hasRole('Super Admin') ? 0 : auth()->user()->id, 'from' => 'sidebar']) }}" 
                               class="nav-link {{ request()->has('from') && request('from') === 'sidebar' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cut"></i>
                                <p>Carveouts</p>
                            </a>
                        </li>
                        
                        @endif
        
                        <!-- Products Tab (Visible only to LPs) -->
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('LP'))
    <li class="nav-item">
        <a href="{{ route('lp.products', ['from_sidebar' => true]) }}" 
           class="nav-link {{ request()->routeIs('lp.products.index') || request()->get('from_sidebar') ? 'active' : '' }}">
            <i class="nav-icon fas fa-box"></i>
            <p>Products</p>
        </a>
    </li>
@endif

@if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Retailer'))
<li class="nav-item">
    <a href="{{ route('super_admin.reports.index') }}" class="nav-link {{ request()->routeIs('super_admin.reports.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>Reports</p>
    </a>
</li>

@endif

     
                    </ul>
                </nav>
            </div>
        </aside>
        
        

        
        <!-- Content Wrapper -->
        <div style="background-color: #54595F" class="content-wrapper">
            <div style="background-color: #54595F" class="content-header">
                <div class="container-fluid">
     
                </div>
            </div>
            <div style="background-color: #54595F" class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; {{ date('Y') }} <a href="#">Novatore Solutions</a>.</strong> All rights reserved.
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Latest DataTables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
