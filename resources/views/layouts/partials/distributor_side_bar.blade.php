<aside style="background-color: #54595F" class="main-sidebar sidebar-dark-primary elevation-4">
    <a style="text-decoration: none" href="{{ route('retailer.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">
            Retailer Dashboard
        </span>
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
                            <p>Supplier Info</p>
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
                        <a href="{{ route('offers.index') }}" class="nav-link {{ (Route::currentRouteName() == 'offers.index' && !request()->get('from_lp_show')) ? 'active' : '' }}">
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
            </ul>
        </nav>
    </div>
</aside>
