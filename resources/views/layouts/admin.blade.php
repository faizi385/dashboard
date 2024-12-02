<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Ivy+Mode:wght@400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- FontAwesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
    <style>


    </style>
    <style>
select {
    /* background-color: #cbdcff; */
    border: none !important;
    border-radius: 8px;
    font-family: "Poppins";
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 20px;
    text-transform: capitalize;
    color: #3e445e;
    background-image: url(../images/dropdown.svg) !important;
    width: 100%;
    z-index: 99 !important;
    height: 43px !important;
}
.select2-hidden-accessible {
    border: 0 !important;
    clip: rect(0 0 0 0) !important;
    -webkit-clip-path: inset(50%) !important;
    clip-path: inset(50%) !important;
    height: 1px !important;
    overflow: hidden !important;
    padding: 0 !important;
    position: absolute !important;
    width: 1px !important;
    white-space: nowrap !important;
}
.select2 {
    /* background-color: #cbdcff; */
    border: none !important;
    padding: 6px 10px;
    border-radius: 8px;
    font-family: "Poppins";
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 20px;
    text-transform: capitalize;
    color: #3e445e;
    background-image: url(../images/dropdown.svg) !important;
    width: 100%;
    z-index: 99 !important;
    overflow: auto !important;
}
.form-select, select, .select2 {
    -webkit-appearance: none;
    background-image: url(../images/arrow-down.svg);
    background-repeat: no-repeat;
    background-position: calc(100% - 15px) center;
    background-size: 14px;
    line-height: 20px !important;
    z-index: 9999;
}
.select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
}
.select2-selection__clear{
    display: none;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 26px !important;
    position: absolute !important;
    top: 6px !important;
    right: 18px !important;
    width: 20px !important;
}
.select2 {
    /* background-color: #cbdcff; */
    border: none !important;
    padding: 6px 10px;
    border-radius: 8px;
    font-family: "Poppins";
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 20px;
    text-transform: capitalize;
    color: #3e445e;
    background-image: url(../images/dropdown.svg) !important;
    width: 100%;
    z-index: 99 !important;
    overflow: auto !important;
}
.form-select, select, .select2 {
    -webkit-appearance: none;
    background-image: url(../images/arrow-down.svg);
    background-repeat: no-repeat;
    background-position: calc(100% - 15px) center;
    background-size: 14px;
    line-height: 20px !important;
    z-index: 9999;
}
.select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
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
                        <i class="fas fa-user"></i> {{ Auth::user()->first_name ?? null }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('settings') }}"><i class="fas fa-cogs mr-2"></i>Profile Settings</a></li>
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
{{--        <aside style="background-color: #54595F" class="main-sidebar sidebar-dark-primary elevation-4">--}}
{{--            <a style="text-decoration: none" href="{{ auth()->user()->hasRole('Super Admin') ? route('dashboard') : (auth()->user()->hasRole('LP') ? route('lp.dashboard') : route('retailer.dashboard')) }}" class="brand-link">--}}
{{--            <span class="brand-text font-weight-light">--}}
{{--                @if(auth()->user()->hasRole('Super Admin'))--}}
{{--                    Super Admin Dashboard--}}
{{--                @elseif(auth()->user()->hasRole('LP'))--}}
{{--                    Supplier Portal--}}
{{--                @else--}}
{{--                    Retailer Dashboard--}}
{{--                @endif--}}
{{--            </span>--}}
{{--            </a>--}}
{{--        --}}
{{--            <div class="sidebar">--}}
{{--                <div class="form-inline mt-3">--}}
{{--                    <div  class="input-group" data-widget="sidebar-search">--}}
{{--                        <input style="background-color: white" class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">--}}
{{--                        <div class="input-group-append">--}}
{{--                            <button class="btn btn-sidebar">--}}
{{--                                <i class="fas fa-search fa-fw"></i>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--        --}}
{{--                <nav class="mt-2">--}}
{{--                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">--}}
{{--        --}}
{{--<!--                     --}}
{{--                        <li class="nav-item has-treeview {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'menu-open' : '' }}">--}}
{{--                            <a href="#" class="nav-link {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}">--}}
{{--                                <i class="nav-icon fas fa-users"></i>--}}
{{--                                <p>--}}
{{--                                    Manage Users--}}
{{--                                    <i class="right fas fa-angle-left"></i>--}}
{{--                                </p>--}}
{{--                            </a>--}}
{{--                            <ul class="nav nav-treeview">--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="{{ route('users.index') }}" class="nav-link {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}">--}}
{{--                                        <i class="nav-icon"></i>--}}
{{--                                        <p>Users</p>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="{{ route('roles.index') }}" class="nav-link {{ Route::currentRouteName() == 'roles.index' ? 'active' : '' }}">--}}
{{--                                        <i class="nav-icon"></i>--}}
{{--                                        <p>Roles</p>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--        --}}
{{--                               --}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="{{ route('permissions.index') }}" class="nav-link {{ Route::currentRouteName() == 'permissions.index' ? 'active' : '' }}">--}}
{{--                                        <i class="nav-icon"></i>--}}
{{--                                        <p>Permissions</p>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                           --}}
{{--                            </ul>--}}
{{--                        </li> -->--}}
{{--   --}}
{{--                        <!-- Manage Provinces (Visible only to Super Admin) -->--}}
{{--                        @if(in_array('view provinces', $permission))--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('provinces.index') }}" class="nav-link {{ Route::currentRouteName() == 'provinces.index' ? 'active' : '' }}">--}}
{{--                                <i class="nav-icon fas fa-map"></i>--}}
{{--                                <p>Provinces</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        @endif--}}

{{--                        @if(in_array('view logs', $permission))--}}
{{--                        <li class="nav-item has-treeview --}}
{{--    {{ request()->is('logs*') || request()->is('province-logs*') || request()->is('retailer-logs*') || request()->is('lp-logs*') || request()->is('offer-logs*') || request()->is('carveout-logs*') || request()->is('report-logs*') || --}}
{{--      Route::currentRouteName() == 'retailer.logs' || Route::currentRouteName() == 'lp.logs.index' ? 'menu-open' : '' }}">--}}
{{--    <a href="#" class="nav-link --}}
{{--        {{ request()->is('logs*') || request()->is('province-logs*') || request()->is('retailer-logs*') || request()->is('lp-logs*') || request()->is('offer-logs*') || request()->is('carveout-logs*') || request()->is('report-logs*') ? 'active' : '' }}">--}}
{{--        <i class="nav-icon fas fa-book"></i>--}}
{{--        <p>Logs--}}
{{--            <i class="right fas fa-angle-left"></i>--}}
{{--        </p>--}}
{{--    </a>--}}
{{--    <ul class="nav nav-treeview">--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'logs.index' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>User Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('province-logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'province-logs.index' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>Province Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('retailer.logs') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.logs' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>Distributor Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('lp.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'lp.logs.index' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>Supplier Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('offer.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'offer.logs.index' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>Deals Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('carveout.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'carveout.logs.index' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>Carveout Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('report.logs.index') }}" class="nav-link {{ Route::currentRouteName() == 'report.logs.index' ? 'active' : '' }}">--}}
{{--                <i class="nav-icon"></i>--}}
{{--                <p>Report Logs</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--    </ul>--}}
{{--</li>--}}

{{--              @endif                  --}}
{{--              @if(in_array('view supplier', $permission))--}}

{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('lp.management') }}" --}}
{{--                               class="nav-link {{ (request()->is('lp/management*') || Route::currentRouteName() == 'lp.management' || Route::currentRouteName() == 'lp.show') ? 'active' : '' }}">--}}
{{--                                <i class="nav-icon fas fa-building"></i>--}}
{{--                                <p>--}}
{{--                                    Supplier --}}
{{--                                    --}}{{-- <span class="right badge badge-danger">New</span> --}}
{{--                                </p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        --}}
{{--                        --}}
{{--                        --}}
{{--                        @endif--}}
{{--                       --}}
{{--                        <!-- Manage Info (Visible only to LPs) -->--}}
{{--                        @if(in_array('view manage info', $permission))--}}
{{--                        <li class="nav-item {{ request()->is('manage-info*') ? 'menu-open' : '' }}">--}}
{{--                            <a href="{{ route('manage-info.index') }}" class="nav-link {{ request()->is('manage-info*') ? 'active' : '' }}">--}}
{{--                                <i class="nav-icon fas fa-info-circle"></i>--}}
{{--                                <p>Supplier Info</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        @endif--}}
{{--        --}}
{{--                        @if(in_array('view distributor', $permission))--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('retailer.index') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.index' ? 'active' : '' }}">--}}
{{--                                <i class="nav-icon fas fa-user"></i>--}}
{{--                                <p>Distributor </p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        @endif--}}
{{--                    @if(in_array('view deals', $permission))--}}
{{--                   <li class="nav-item">--}}
{{--                       <a href="{{ route('offers.index') }}"--}}
{{--                          class="nav-link {{ (Route::currentRouteName() == 'offers.index' && !request()->get('from_lp_show')) ? 'active' : '' }}">--}}
{{--                           <i class="nav-icon fas fa-tag"></i>--}}
{{--                           <p>Deals</p>--}}
{{--                       </a>--}}
{{--                   </li>--}}
{{--                   @endif--}}
{{--            --}}
{{--                   --}}
{{--                   --}}
{{--                       --}}
{{--                       --}}
{{--                   --}}
{{--                   --}}

{{--        --}}
{{--                   @if(in_array('view carveouts', $permission))--}}
{{--                   <li class="nav-item">--}}
{{--                       <a href="{{ route('carveouts.index', ['lp_id' => auth()->user()->hasRole('Super Admin') ? 0 : auth()->user()->id, 'from' => 'sidebar']) }}" --}}
{{--                          class="nav-link {{ request()->has('from') && request('from') === 'sidebar' ? 'active' : '' }}">--}}
{{--                           <i class="nav-icon fas fa-cut"></i>--}}
{{--                           <p>Carveouts</p>--}}
{{--                       </a>--}}
{{--                   </li>--}}
{{--                   --}}
{{--                   @endif--}}
{{--                     --}}
{{--                   --}}
{{--                   @if(in_array('view products', $permission))--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('lp.products', ['from_sidebar' => true]) }}" --}}
{{--                               class="nav-link {{ request()->routeIs('lp.products.index') || request()->get('from_sidebar') ? 'active' : '' }}">--}}
{{--                                <i class="nav-icon fas fa-box"></i>--}}
{{--                                <p>Products</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
{{--                    @if(in_array('view reports', $permission))--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('super_admin.reports.index') }}" class="nav-link {{ request()->routeIs('super_admin.reports.index') ? 'active' : '' }}">--}}
{{--                            <i class="nav-icon fas fa-file-alt"></i>--}}
{{--                            <p>Reports</p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    --}}
{{--                    @endif--}}
{{--                    --}}{{-- @if(auth()->user()->hasRole('LP'))--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('retailer.create') }}" class="nav-link {{ Route::currentRouteName() == 'retailer.create' ? 'active' : '' }}">--}}
{{--                            <i class="nav-icon fas fa-plus"></i>--}}
{{--                            <p>Create Retailer</p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    @endif --}}
{{--                    @php--}}
{{--                    $lpId = \App\Models\Lp::where('user_id', auth()->id())->value('id');--}}
{{--                @endphp--}}
{{--                --}}
{{--                @if(in_array('view statement', $permission))--}}
{{--                @if($lpId)--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route('lp.statement.view', ['lp_id' => $lpId]) }}"--}}
{{--                       class="nav-link {{ Route::currentRouteName() == 'lp.statement.view' ? 'active' : '' }}">--}}
{{--                        <i class="nav-icon fas fa-file-invoice"></i>--}}
{{--                        <p>Statement</p>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                @endif--}}
{{--                @endif--}}
{{--                    --}}
{{--                    </ul>--}}
{{--                </nav>--}}
{{--            </div>--}}
{{--        </aside>--}}
        @if(auth()->user()->hasRole('Super Admin'))
            @include('layouts.partials.sidebar')
        @elseif(auth()->user()->hasRole('LP'))
            @include('layouts.partials.supplier_side_bar')
        @elseif(auth()->user()->hasRole('Retailer'))
            @include('layouts.partials.distributor_side_bar')
        @endif
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Latest DataTables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Optional: Apply Select2 only if needed
        $('.select2').select2({
            placeholder: "Select Supplier",
            allowClear: true
        });
    });
</script>
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
