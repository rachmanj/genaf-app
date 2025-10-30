<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">GENAF - <b>Enterprise</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

                <!-- Dashboard Section -->
                <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-header">MAIN</li>

                <!-- Office Supplies -->
                @can('view supplies')
                    <li
                        class="nav-item {{ request()->routeIs('supplies.*') || request()->routeIs('stock-opname.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('supplies.*') || request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                Office Supplies
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('supplies.index') }}"
                                    class="nav-link {{ request()->routeIs('supplies.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Supplies Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('supplies.requests.index') }}"
                                    class="nav-link {{ request()->routeIs('supplies.requests.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Supply Requests</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('supplies.transactions.index') }}"
                                    class="nav-link {{ request()->routeIs('supplies.transactions.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Stock Transactions</p>
                                </a>
                            </li>
                            @can('view supply fulfillment')
                                <li class="nav-item">
                                    <a href="{{ route('supplies.fulfillment.index') }}"
                                        class="nav-link {{ request()->routeIs('supplies.fulfillment.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Fulfillment</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view department stock')
                                <li class="nav-item">
                                    <a href="{{ route('supplies.department-stock.index') }}"
                                        class="nav-link {{ request()->routeIs('supplies.department-stock.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Department Stock</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view stock opname')
                                <li class="nav-item">
                                    <a href="{{ route('stock-opname.index') }}"
                                        class="nav-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Opname</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Ticket Reservations -->
                @can('view ticket reservations')
                    <li class="nav-item">
                        <a href="{{ route('ticket-reservations.index') }}"
                            class="nav-link {{ request()->routeIs('ticket-reservations.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-ticket-alt"></i>
                            <p>Ticket Reservations</p>
                        </a>
                    </li>
                @endcan

                <!-- Vehicle Administration -->
                @can('view vehicles')
                    <li class="nav-item {{ request()->routeIs('vehicles.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-car"></i>
                            <p>
                                Vehicle Administration
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Vehicle Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Fuel Records</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Maintenance</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Property Management -->
                @can('view rooms')
                    <li class="nav-item {{ request()->routeIs('rooms.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bed"></i>
                            <p>
                                Property Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Room Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reservations</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Asset Inventory -->
                @can('view assets')
                    <li class="nav-item {{ request()->routeIs('assets.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-laptop"></i>
                            <p>
                                Asset Inventory
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Asset Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Asset Transfers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Reports & Analytics -->
                @can('view reports')
                    <li class="nav-item {{ request()->routeIs('reports.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Reports & Analytics
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Supply Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Vehicle Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Asset Reports</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Admin Section -->
                @canany(['view users', 'view roles', 'view permissions', 'view departments'])
                    <li
                        class="nav-item {{ request()->routeIs('users.*') || request()->routeIs('admin.*') || request()->routeIs('departments.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('users.*') || request()->routeIs('admin.*') || request()->routeIs('departments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>
                                Admin
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view users')
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}"
                                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>User Management</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view departments')
                                <li class="nav-item">
                                    <a href="{{ route('departments.index') }}"
                                        class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Departments</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view roles')
                                <li class="nav-item">
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view permissions')
                                <li class="nav-item">
                                    <a href="{{ route('admin.permissions.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
