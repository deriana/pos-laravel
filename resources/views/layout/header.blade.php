<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('APP_NAME') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img') }}/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('vendor') }}/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor') }}/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vendor') }}/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('css') }}/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vendor') }}/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="{{ asset('vendor') }}/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('vendor') }}/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    {{-- <script src="js/config.js"></script> --}}
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('dashboard') }}" class="app-brand-link">

                        <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ env('APP_NAME') }}</span>
                    </a>


                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">

                    <!-- Dashboard -->
                    <!-- Dashboard (Paling penting & utama) -->
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    <!-- Master Data (Data utama dan sering digunakan) -->
                    <li class="menu-header">Master Data</li>

                    <li class="menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cube"></i>
                            <div data-i18n="Products">Products</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-category"></i>
                            <div data-i18n="Categories">Categories</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-store-alt"></i>
                            <div data-i18n="Suppliers">Suppliers</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li>

                    <!-- Transaksi (Operasi harian penting) -->
                    <li class="menu-header">Transaksi</li>

                    <li class="menu-item {{ request()->routeIs('purchases.index') ? 'active' : '' }}">
                        <a href="{{ route('purchases.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-list-ul"></i>
                            <div data-i18n="Purchases Index">List Purchases</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <a href="{{ route('purchases.create') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cart"></i>
                            <div data-i18n="Purchases">Add Product From Suppliers</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                        <a href="{{ route('sales.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-list-ul"></i>
                            <div data-i18n="Sales Index">List Sales</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <a href="{{ route('sales.create') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cart"></i>
                            <div data-i18n="Sales">Cashiers</div>
                        </a>
                    </li>

                    <!-- Management (Admin level) -->
                    <li class="menu-header">Management</li>

                    <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-check"></i>
                            <div data-i18n="Users">Management Users</div>
                        </a>
                    </li>

                    <!-- Reports Group -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Reports</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        <a href="{{ route('reports.sales') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                            <div data-i18n="Sales Report">Sales Report</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
                        <a href="{{ route('reports.purchases') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                            <div data-i18n="Purchase Report">Purchase Report</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                        <a href="{{ route('reports.inventory') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-package"></i>
                            <div data-i18n="Inventory Report">Inventory Report</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('reports.customers') ? 'active' : '' }}">
                        <a href="{{ route('reports.customers') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-voice"></i>
                            <div data-i18n="Customer Report">Customer Report</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('reports.suppliers') ? 'active' : '' }}">
                        <a href="{{ route('reports.suppliers') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-store"></i>
                            <div data-i18n="Supplier Report">Supplier Report</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('reports.profit') ? 'active' : '' }}">
                        <a href="{{ route('reports.profit') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-store"></i>
                            <div data-i18n="Supplier Report">Profit Report</div>
                        </a>
                    </li>

                </ul>


            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none"
                                    placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->


                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('img/avatars/' . session('selectedAvatar', '1.png')) }}"
                                            alt class="w-px-70 h-px-70 rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('img/avatars/' . session('selectedAvatar', '1.png')) }}"
                                                            alt class="w-px-70 h-px-70 rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                                    <small class="text-muted">{{ Auth::user()->role }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('auth.profile') }}">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>


                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>

                                        <a href="#" class="dropdown-item" id="logout-button">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->
                <div class="content-wrapper">
