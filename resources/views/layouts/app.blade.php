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
<link href="https://fonts.googleapis.com/css2?family=Ivy+Mode:wght@400&display=swap" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Toastr JS -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}

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
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('styles')
    <style>
    

    
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav  class="main-header navbar navbar-expand navbar-white navbar-light">
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
        <a class="nav-link dropdown-toggle " href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        <?php $permission = auth()->user()->getAllPermissions()->pluck('name')->toArray(); ?>
        <aside style="background-color: #54595F" class="main-sidebar sidebar-dark-primary elevation-4">
            <a style="text-decoration: none" href="{{ auth()->user()->hasRole('Super Admin') ? route('dashboard') : (auth()->user()->hasRole('LP') ? route('lp.dashboard') : route('retailer.dashboard')) }}" class="brand-link">
                <span class="brand-text font-weight-light">Novatore</span>
            </a>
        
            <div class="sidebar">
                <div class="form-inline mt-3">
                    <div  class="input-group" data-widget="sidebar-search">
                        <input style="background-color: white" class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>
        
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
                     @if(auth()->user()->hasRole('Super Admin'))
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
                              
                                <li class="nav-item">
                                    <a href="{{ route('permissions.index') }}" class="nav-link {{ Route::currentRouteName() == 'permissions.index' ? 'active' : '' }}">
                                        <i class="nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                          
                            </ul>
                        </li>
   @endif
                        <!-- Manage Provinces (Visible only to Super Admin) -->
                        @if(in_array('view provinces', $permission))
                        <li class="nav-item">
                            <a href="{{ route('provinces.index') }}" class="nav-link {{ Route::currentRouteName() == 'provinces.index' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-map"></i>
                                <p>Provinces</p>
                            </a>
                        </li>
                        @endif

                        @if(in_array('view logs', $permission))
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
                <p>Distributor Logs</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('lp.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'lp.logs.index' ? 'active' : '' }}">
                <i class="nav-icon"></i>
                <p>Supplier Logs</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('offer.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'offer.logs.index' ? 'active' : '' }}">
                <i class="nav-icon"></i>
                <p>Deals Logs</p>
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

              @endif                  
              @if(in_array('view supplier', $permission))

                        <li class="nav-item">
                            <a href="{{ route('lp.management') }}" 
                               class="nav-link {{ (request()->is('lp/management*') || Route::currentRouteName() == 'lp.management' || Route::currentRouteName() == 'lp.show') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    Supplier 
                                    {{-- <span class="right badge badge-danger">New</span> --}}
                                </p>
                            </a>
                        </li>
                        
                        
                        
                        @endif
                       
                        <!-- Manage Info (Visible only to LPs) -->
                        @if(in_array('view manage info', $permission))
                        <li class="nav-item {{ request()->is('manage-info*') ? 'menu-open' : '' }}">
                            <a href="{{ route('manage-info.index') }}" class="nav-link {{ request()->is('manage-info*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-info-circle"></i>
                                <p>Manage Info</p>
                            </a>
                        </li>
                        @endif
                       @if(auth()->user()->hasRole('LP'))
                        <li class="nav-item">
                            <a href="{{ route('analytics.index') }}" 
                               class="nav-link {{ Route::currentRouteName() == 'analytics.index' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Analytics</p>
                            </a>
                        </li>
                        @endif 
                        
                        @if(auth()->user()->hasRole('Super Admin'))
                        <li class="nav-item">
                            <a href="{{ route('performance.index') }}" 
                               class="nav-link {{ Route::currentRouteName() == 'performance.index' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Analytics</p>
                            </a>
                        </li>
                        @endif 


                        @if(in_array('view distributor', $permission))
                        <li class="nav-item">
                            <a href="{{ route('retailer.index') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.index' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Distributor </p>
                            </a>
                        </li>
                        @endif
                    @if(in_array('view deals', $permission))
                   <li class="nav-item">
                       <a href="{{ route('offers.index') }}"
                          class="nav-link {{ (Route::currentRouteName() == 'offers.index' && !request()->get('from_lp_show')) ? 'active' : '' }}">
                           <i class="nav-icon fas fa-tag"></i>
                           <p>Deals</p>
                       </a>
                   </li>
                   @endif
            
                   
                   
                       
                       
                   
                   

        
                   @if(in_array('view carveouts', $permission))
                   <li class="nav-item">
                       <a href="{{ route('carveouts.index', ['lp_id' => auth()->user()->hasRole('Super Admin') ? 0 : auth()->user()->id, 'from' => 'sidebar']) }}" 
                          class="nav-link {{ request()->has('from') && request('from') === 'sidebar' ? 'active' : '' }}">
                           <i class="nav-icon fas fa-cut"></i>
                           <p>Carveouts</p>
                       </a>
                   </li>
                   
                   @endif
                     
                   
                   @if(in_array('view products', $permission))
                        <li class="nav-item">
                            <a href="{{ route('lp.products', ['from_sidebar' => true]) }}" 
                               class="nav-link {{ request()->routeIs('lp.products.index') || request()->get('from_sidebar') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box"></i>
                                <p>Products</p>
                            </a>
                        </li>
                    @endif
                    @if(in_array('view reports', $permission))
                    <li class="nav-item">
                        <a href="{{ route('super_admin.reports.index') }}" class="nav-link {{ request()->routeIs('super_admin.reports.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                    
                    @endif
                    {{-- @if(auth()->user()->hasRole('LP'))
                    <li class="nav-item">
                        <a href="{{ route('retailer.create') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.create' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Create Retailer</p>
                        </a>
                    </li>
                    @endif --}}
                    @php
                    $lpId = \App\Models\Lp::where('user_id', auth()->id())->value('id');
                @endphp
                
                @if(in_array('view statement', $permission))
                @if($lpId)
                <li class="nav-item">
                    <a href="{{ route('lp.statement.view', ['lp_id' => $lpId]) }}"
                       class="nav-link {{ Route::currentRouteName() == 'lp.statement.view' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Statement</p>
                    </a>
                </li>
                @endif
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Latest DataTables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

 <!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
    @stack('scripts')
</body>
</html>
