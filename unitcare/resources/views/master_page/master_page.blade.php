@inject('navbar', 'App\Http\Controllers\utilities\NavbarController')
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Spruha -  Admin Panel HTML Dashboard Template">
    <meta name="author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,dashboard,panel,bootstrap admin template,bootstrap dashboard,dashboard,themeforest admin dashboard,themeforest admin,themeforest dashboard,themeforest admin panel,themeforest admin template,themeforest admin dashboard,cool admin,it dashboard,admin design,dash templates,saas dashboard,dmin ui design">

    <!-- Favicon -->
    <link rel="icon" href="../assets/img/brand/favicon.ico" type="image/x-icon" />

    <!-- Title -->
    <title>@yield('page_title', 'Maintenance Residence System')</title>

    <!-- Bootstrap css-->
    <link id="style" href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Icons css-->
    <link href="../assets/plugins/web-fonts/icons.css" rel="stylesheet" />
    <link href="../assets/plugins/web-fonts/font-awesome/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/plugins/web-fonts/plugin.css" rel="stylesheet" />

    <!-- Style css-->
    <link href="../assets/css/style.css" rel="stylesheet">

    <!-- Select2 css -->
    <link href="../assets/plugins/select2/css/select2.min.css" rel="stylesheet">

    @stack('styles')

</head>

<body class="ltr main-body leftmenu">

    <!-- Loader -->
    <div id="global-loader">
        <img src="../assets/img/loader.svg" class="loader-img" alt="Loader">
    </div>
    <!-- End Loader -->

    <!-- Page -->
    <div class="page">

        <!-- Main Header-->
        <div class="main-header side-header sticky">
            <div class="main-container container-fluid">
                <div class="main-header-left">
                    <a class="main-header-menu-icon" href="javascript:void(0)" id="mainSidebarToggle"><span></span></a>
                    <div class="hor-logo">
                        <a class="main-logo" href="{{ route('dashboard_utama') }}">
                            <img src="../assets/img/brand/logo.png" class="header-brand-img desktop-logo" alt="logo">
                            <img src="../assets/img/brand/logo-light.png" class="header-brand-img desktop-logo-dark"
                                alt="logo">
                        </a>
                    </div>
                </div>
                <!-- <div class="main-header-center">
                    <div class="responsive-logo">
                        <a href="{{ route('dashboard_utama') }}"><img src="../assets/img/brand/logo.png" class="mobile-logo" alt="logo"></a>
                        <a href="{{ route('dashboard_utama') }}"><img src="../assets/img/brand/logo-light.png" class="mobile-logo-dark"
                                alt="logo"></a>
                    </div>
                    <div class="input-group">
                        <div class="input-group-btn search-panel">
                            <select class="form-control select2">
                                <option label="All categories">
                                </option>
                                <option value="IT Projects">
                                    IT Projects
                                </option>
                                <option value="Business Case">
                                    Business Case
                                </option>
                                <option value="Microsoft Project">
                                    Microsoft Project
                                </option>
                                <option value="Risk Management">
                                    Risk Management
                                </option>
                                <option value="Team Building">
                                    Team Building
                                </option>
                            </select>
                        </div>
                        <input type="search" class="form-control rounded-0" placeholder="Search for anything...">
                        <button class="btn search-btn"><i class="fe fe-search"></i></button>
                    </div>
                </div> -->
                <div class="main-header-right">
                    <button class="navbar-toggler navresponsive-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
                    </button><!-- Navresponsive closed -->
                    <div
                        class="navbar navbar-expand-lg  nav nav-item  navbar-nav-right responsive-navbar navbar-dark  ">
                        <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                            <div class="d-flex order-lg-2 ms-auto">
                                <!-- Search -->
                                <!-- <div class="dropdown header-search">
                                    <a class="nav-link icon header-search">
                                        <i class="fe fe-search header-icons"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <div class="main-form-search p-2">
                                            <div class="input-group">
                                                <div class="input-group-btn search-panel">
                                                    <select class="form-control select2">
                                                        <option label="All categories">
                                                        </option>
                                                        <option value="IT Projects">
                                                            IT Projects
                                                        </option>
                                                        <option value="Business Case">
                                                            Business Case
                                                        </option>
                                                        <option value="Microsoft Project">
                                                            Microsoft Project
                                                        </option>
                                                        <option value="Risk Management">
                                                            Risk Management
                                                        </option>
                                                        <option value="Team Building">
                                                            Team Building
                                                        </option>
                                                    </select>
                                                </div>
                                                <input type="search" class="form-control"
                                                    placeholder="Search for anything...">
                                                <button class="btn search-btn"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="feather feather-search">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                    </svg></button>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- Search -->
                                <!-- Theme-Layout -->
                                <div class="dropdown d-flex main-header-theme">
                                    <a class="nav-link icon layout-setting">
                                        <span class="dark-layout">
                                            <i class="fe fe-sun header-icons"></i>
                                        </span>
                                        <span class="light-layout">
                                            <i class="fe fe-moon header-icons"></i>
                                        </span>
                                    </a>
                                </div>
                                <!-- Theme-Layout -->

                                <!-- Full screen -->
                                <div class="dropdown ">
                                    <a class="nav-link icon full-screen-link">
                                        <i class="fe fe-maximize fullscreen-button fullscreen header-icons"></i>
                                        <i class="fe fe-minimize fullscreen-button exit-fullscreen header-icons"></i>
                                    </a>
                                </div>
                                <!-- Full screen -->

                                <!-- Notifications -->
                                <div class="dropdown main-header-notification">
                                    <a class="nav-link icon" href="javascript:void(0)" id="notifBell">
                                        <i class="fe fe-bell header-icons"></i>
                                        <span class="badge bg-danger rounded-pill position-absolute" id="notifBadge" style="top:8px;right:8px;font-size:10px;display:none;"></span>
                                    </a>
                                    <div class="dropdown-menu" style="min-width:320px;max-height:400px;overflow-y:auto;">
                                        <div class="header-navheading d-flex justify-content-between align-items-center px-3 py-2">
                                            <h6 class="mb-0">Notifications</h6>
                                            <a href="javascript:void(0)" id="markAllRead" class="text-muted small">Mark all read</a>
                                        </div>
                                        <div id="notifList" class="px-1">
                                            <p class="text-center text-muted small py-3" id="notifEmpty">No notifications</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Notifications -->

                                <!-- Profile -->
                                <div class="dropdown main-profile-menu">
                                    <a class="d-flex" href="javascript:void(0)">
                                        <span class="main-img-user"><img alt="avatar"
                                                src="../assets/img/users/1.jpg"></span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <div class="header-navheading">
                                            <h6 class="main-notification-title">{{ auth()->user()->name }}</h6>
                                            <p class="main-notification-text">{{ ucfirst(auth()->user()->role) }}</p>
                                        </div>
                                        <a class="dropdown-item border-top" href="{{ route('profile.show') }}">
                                            <i class="fe fe-user"></i> My Profile
                                        </a>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fe fe-edit-2"></i> Edit Profile
                                        </a>
                                        <a class="dropdown-item" href="{{ route('profile.settings') }}">
                                            <i class="fe fe-settings"></i> Account Settings
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fe fe-power"></i> Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <!-- Profile -->
                                <!-- Sidebar -->
                                <div class="dropdown  header-settings">
                                    <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="sidebar-right"
                                        data-bs-target=".sidebar-right">
                                        <i class="fe fe-align-right header-icons"></i>
                                    </a>
                                </div>
                                <!-- Sidebar -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Header-->

        <!-- Sidemenu -->
        <div class="sticky">
            <div class="main-menu main-sidebar main-sidebar-sticky side-menu">
                <div class="main-sidebar-header main-container-1 active">
                    <div class="sidemenu-logo">
                        <a class="main-logo" href="{{ route('dashboard_utama') }}">
                            <img src="../assets/img/brand/logo-light.png" class="header-brand-img desktop-logo" alt="logo">
                            <img src="../assets/img/brand/icon-light.png" class="header-brand-img icon-logo" alt="logo">
                            <img src="../assets/img/brand/logo.png" class="header-brand-img desktop-logo theme-logo" alt="logo">
                            <img src="../assets/img/brand/icon.png" class="header-brand-img icon-logo theme-logo" alt="logo">
                        </a>
                    </div>
                    <div class="main-sidebar-body main-body-1">
                        <div class="slide-left disabled" id="slide-left"><i class="fe fe-chevron-left"></i></div>
                        <ul class="menu-nav nav">

                            {{-- ===== DASHBOARD — all roles ===== --}}
                            <li class="nav-item {{ $navbar->dashboardIsActive() }}">
                                <a class="nav-link {{ $navbar->dashboardIsActive() }}" href="{{ route('dashboard_utama') }}">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-home sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Dashboard</span>
                                </a>
                            </li>

                            {{-- ===== MANAGE — admin only ===== --}}
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <li class="nav-item {{ $navbar->manageIsExpanded() }}">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-shield sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Manage</span>
                                    <i class="angle ti-angle-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Manage</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('manageUsers') }}"><a class="nav-sub-link {{ $navbar->isActive('manageUsers') }}" href="{{ route('manageUsers') }}">Users</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('manageResidents') }}"><a class="nav-sub-link {{ $navbar->isActive('manageResidents') }}" href="{{ route('manageResidents') }}">Residents</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('manageFacilities') }}"><a class="nav-sub-link {{ $navbar->isActive('manageFacilities') }}" href="{{ route('manageFacilities') }}">Facilities</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('manageStaff') }}"><a class="nav-sub-link {{ $navbar->isActive('manageStaff') }}" href="{{ route('manageStaff') }}">Staff</a></li>
                                </ul>
                            </li>
                            @endif

                            {{-- ===== VISITORS — admin: all 4 | resident: Register + My | security: Check-In + Today ===== --}}
                            @if(auth()->check() && in_array(auth()->user()->role, ['resident', 'security', 'admin']))
                            <li class="nav-item {{ $navbar->visitorsIsExpanded() }}">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-id-badge sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Visitors</span>
                                    <i class="angle ti-angle-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Visitors</a></li>
                                    @if(auth()->user()->role === 'admin')
                                    <li class="nav-sub-item {{ $navbar->isActive('registerVisitor') }}"><a class="nav-sub-link {{ $navbar->isActive('registerVisitor') }}" href="{{ route('registerVisitor') }}">Register Visitor</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('myVisitors') }}"><a class="nav-sub-link {{ $navbar->isActive('myVisitors') }}" href="{{ route('myVisitors') }}">My Visitors</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('checkIn') }}"><a class="nav-sub-link {{ $navbar->isActive('checkIn') }}" href="{{ route('checkIn') }}">Check-In / Out</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('todayVisitors') }}"><a class="nav-sub-link {{ $navbar->isActive('todayVisitors') }}" href="{{ route('todayVisitors') }}">Today's Visitors</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'resident')
                                    <li class="nav-sub-item {{ $navbar->isActive('registerVisitor') }}"><a class="nav-sub-link {{ $navbar->isActive('registerVisitor') }}" href="{{ route('registerVisitor') }}">Register Visitor</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('myVisitors') }}"><a class="nav-sub-link {{ $navbar->isActive('myVisitors') }}" href="{{ route('myVisitors') }}">My Visitors</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'security')
                                    <li class="nav-sub-item {{ $navbar->isActive('checkIn') }}"><a class="nav-sub-link {{ $navbar->isActive('checkIn') }}" href="{{ route('checkIn') }}">Check-In / Out</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('todayVisitors') }}"><a class="nav-sub-link {{ $navbar->isActive('todayVisitors') }}" href="{{ route('todayVisitors') }}">Today's Visitors</a></li>
                                    @endif
                                </ul>
                            </li>
                            @endif

                            {{-- ===== MAINTENANCE — admin: All Requests | resident: My Requests | technician: My Tasks ===== --}}
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'resident', 'technician']))
                            <li class="nav-item {{ $navbar->maintenanceIsExpanded() }}">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-clipboard sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Maintenance</span>
                                    <i class="angle ti-angle-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Maintenance</a></li>
                                    @if(auth()->user()->role === 'admin')
                                    <li class="nav-sub-item {{ $navbar->isActive('manageAllRequests') }}"><a class="nav-sub-link {{ $navbar->isActive('manageAllRequests') }}" href="{{ route('manageAllRequests') }}">All Requests</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'resident')
                                    <li class="nav-sub-item {{ $navbar->isActive('manageMyRequests') }}"><a class="nav-sub-link {{ $navbar->isActive('manageMyRequests') }}" href="{{ route('manageMyRequests') }}">My Requests</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'technician')
                                    <li class="nav-sub-item {{ $navbar->isActive('myTasks') }}"><a class="nav-sub-link {{ $navbar->isActive('myTasks') }}" href="{{ route('myTasks') }}">My Tasks</a></li>
                                    @endif
                                </ul>
                            </li>
                            @endif

                            {{-- ===== BOOKINGS — admin: Manage Bookings | resident: Book + My Bookings ===== --}}
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'resident']))
                            <li class="nav-item {{ $navbar->bookingsIsExpanded() }}">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-calendar sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Bookings</span>
                                    <i class="angle ti-angle-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Bookings</a></li>
                                    @if(auth()->user()->role === 'admin')
                                    <li class="nav-sub-item {{ $navbar->isActive('manageAllBookings') }}"><a class="nav-sub-link {{ $navbar->isActive('manageAllBookings') }}" href="{{ route('manageAllBookings') }}">Manage Bookings</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'resident')
                                    <li class="nav-sub-item {{ $navbar->isActive('bookFacility') }}"><a class="nav-sub-link {{ $navbar->isActive('bookFacility') }}" href="{{ route('bookFacility') }}">Book Facility</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('myBookings') }}"><a class="nav-sub-link {{ $navbar->isActive('myBookings') }}" href="{{ route('myBookings') }}">My Bookings</a></li>
                                    @endif
                                </ul>
                            </li>
                            @endif

                            {{-- ===== ANNOUNCEMENTS — admin: Manage | resident + security: View ===== --}}
                            @if(auth()->check())
                            <li class="nav-item {{ $navbar->announcementsIsExpanded() }}">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-announcement sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Announcements</span>
                                    <i class="angle ti-angle-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Announcements</a></li>
                                    @if(auth()->user()->role === 'admin')
                                    <li class="nav-sub-item {{ $navbar->isActive('manageAnnouncements') }}"><a class="nav-sub-link {{ $navbar->isActive('manageAnnouncements') }}" href="{{ route('manageAnnouncements') }}">Manage</a></li>
                                    @else
                                    <li class="nav-sub-item {{ $navbar->isActive('viewAnnouncements') }}"><a class="nav-sub-link {{ $navbar->isActive('viewAnnouncements') }}" href="{{ route('viewAnnouncements') }}">View Announcements</a></li>
                                    @endif
                                </ul>
                            </li>
                            @endif

                            {{-- ===== PARCEL PICKUPS — admin: Manage | security+admin: Log + Pending | resident: My Parcels ===== --}}
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'security', 'resident']))
                            <li class="nav-item {{ $navbar->parcelsIsExpanded() }}">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-package sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Parcel Pickup</span>
                                    <i class="angle ti-angle-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Parcel Pickup</a></li>
                                    @if(auth()->user()->role === 'admin')
                                    <li class="nav-sub-item {{ $navbar->isActive('manageParcels') }}"><a class="nav-sub-link {{ $navbar->isActive('manageParcels') }}" href="{{ route('manageParcels') }}">Manage Parcels</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('parcels.log') }}"><a class="nav-sub-link {{ $navbar->isActive('parcels.log') }}" href="{{ route('parcels.log') }}">Log Parcel</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('parcels.pending') }}"><a class="nav-sub-link {{ $navbar->isActive('parcels.pending') }}" href="{{ route('parcels.pending') }}">Pending Pickups</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'security')
                                    <li class="nav-sub-item {{ $navbar->isActive('parcels.log') }}"><a class="nav-sub-link {{ $navbar->isActive('parcels.log') }}" href="{{ route('parcels.log') }}">Log Parcel</a></li>
                                    <li class="nav-sub-item {{ $navbar->isActive('parcels.pending') }}"><a class="nav-sub-link {{ $navbar->isActive('parcels.pending') }}" href="{{ route('parcels.pending') }}">Pending Pickups</a></li>
                                    @endif
                                    @if(auth()->user()->role === 'resident')
                                    <li class="nav-sub-item {{ $navbar->isActive('myParcels') }}"><a class="nav-sub-link {{ $navbar->isActive('myParcels') }}" href="{{ route('myParcels') }}">My Parcels</a></li>
                                    @endif
                                </ul>
                            </li>
                            @endif

                            {{-- ===== REPORTS — admin only ===== --}}
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <li class="nav-item {{ $navbar->isActive('manageReports') }}">
                                <a class="nav-link {{ $navbar->isActive('manageReports') }}" href="{{ route('manageReports') }}">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-bar-chart sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Reports</span>
                                </a>
                            </li>
                            @endif


                        </ul>
                        <div class="slide-right" id="slide-right"><i class="fe fe-chevron-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sidemenu -->

        <!-- Main Content-->
        <div class="main-content side-content pt-0">

            <div class="main-container container-fluid">
                <div class="inner-body">
                    @yield('content')
                </div>
            </div>
        </div>
        <!-- End Main Content-->

        <!-- Main Footer-->
        <div class="main-footer text-center">
            <div class="container">
                <div class="row row-sm">
                    <div class="col-md-12">
                        <span>Copyright © 2026 <a href="#">UNitCAre</a>. Designed by <a href="https://www.spruko.com/">Spruko</a> All rights reserved.</span>
                    </div>
                </div>
            </div>
        </div>
        <!--End Footer-->

        

    </div>
    <!-- End Page -->

    <!-- Back-to-top -->
    <a href="#top" id="back-to-top"><i class="fe fe-arrow-up"></i></a>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Jquery js-->
    <script src="../assets/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap js-->
    <script src="../assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- Perfect-scrollbar js -->
    <script src="../assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <!-- Sidemenu js -->
    <script src="../assets/plugins/sidemenu/sidemenu.js" id="leftmenu"></script>

    <!-- Sidebar js -->
    <script src="../assets/plugins/sidebar/sidebar.js"></script>

    <!-- Select2 js-->
    <script src="../assets/plugins/select2/js/select2.min.js"></script>
    <script src="../assets/js/select2.js"></script>

    <!-- Color Theme js -->
    <script src="../assets/js/themeColors.js"></script>

    <!-- Sticky js -->
    <script src="../assets/js/sticky.js"></script>

    <!-- Custom js -->
    <script src="../assets/js/custom.js"></script>

    @stack('scripts')

    <script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {
        // sidemenu.js uses indexOf(lastUrlSegment) which causes false matches when multiple
        // routes share the same last segment (e.g. /bookings/my AND /visitors/my → both match "my").
        // Fix: stop running slide animations, reset inline styles, and re-apply active/show
        // using full-path exact matching.
        var fullPath = window.location.pathname;

        // Stop animations and clear inline display styles sidemenu.js may have set
        $('.main-sidebar .nav-sub').stop(true, true).css('display', '');

        // Clear all sidemenu.js active/show state
        $('.main-sidebar .nav-item').removeClass('show active');
        $('.main-sidebar .nav-sub-item').removeClass('active');
        $('.main-sidebar .nav-sub-link').removeClass('active');

        // Re-apply correct state using exact full-path match
        $('.main-sidebar .nav-sub-link[href]').each(function () {
            var linkPath;
            try { linkPath = new URL(this.href).pathname; } catch (e) { linkPath = this.getAttribute('href'); }
            if (linkPath === fullPath) {
                $(this).addClass('active');
                $(this).closest('.main-sidebar .nav-item').addClass('show active');
                $(this).parent('.nav-sub-item').addClass('active');
            }
        });

        // Re-apply active for direct nav links (no sub-menu) e.g. Dashboard, Reports
        $('.main-sidebar .nav-link:not(.with-sub)[href]').each(function () {
            var linkPath;
            try { linkPath = new URL(this.href).pathname; } catch (e) { linkPath = this.getAttribute('href'); }
            if (linkPath === fullPath) {
                $(this).addClass('active');
                $(this).closest('.main-sidebar .nav-item').addClass('active');
            }
        });
    });
    </script>

    <script>
    (function () {
        var countUrl    = '{{ route("notifications.count") }}';
        var listUrl     = '{{ route("notifications.list") }}';
        var markReadUrl = '{{ route("notifications.readAll") }}';

        function loadCount() {
            $.get(countUrl, function (res) {
                var n = res.count || 0;
                if (n > 0) {
                    $('#notifBadge').text(n > 99 ? '99+' : n).show();
                } else {
                    $('#notifBadge').hide();
                }
            });
        }

        function loadList() {
            $.get(listUrl, function (res) {
                var items = res.data || [];
                $('#notifList').empty();
                if (items.length === 0) {
                    $('#notifList').append('<p class="text-center text-muted small py-3">No notifications</p>');
                    return;
                }
                items.forEach(function (n) {
                    var unreadClass = n.read_at ? '' : 'fw-bold';
                    var html = '<div class="dropdown-item border-bottom py-2 px-3 ' + unreadClass + '" style="white-space:normal;cursor:default;">' +
                        '<div class="small text-primary">' + $('<div>').text(n.title).html() + '</div>' +
                        '<div class="small">' + $('<div>').text(n.message).html() + '</div>' +
                        '<div class="text-muted" style="font-size:11px;">' + (n.created_at || '') + '</div>' +
                        '</div>';
                    $('#notifList').append(html);
                });
            });
        }

        // Open dropdown: load list & mark as read
        $(document).on('click', '#notifBell', function (e) {
            e.stopPropagation();
            var $menu = $(this).closest('.main-header-notification').find('.dropdown-menu');
            var isOpen = $menu.hasClass('show');
            $('.dropdown-menu.show').removeClass('show');
            if (!isOpen) {
                loadList();
                $menu.addClass('show');
            }
        });

        // Mark all read
        $(document).on('click', '#markAllRead', function () {
            $.post(markReadUrl, {}, function () {
                $('#notifBadge').hide();
                loadList();
            });
        });

        // Close on outside click
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.main-header-notification').length) {
                $('.main-header-notification .dropdown-menu').removeClass('show');
            }
        });

        // Poll count every 60 seconds
        loadCount();
        setInterval(loadCount, 60000);
    })();
    </script>

</body>

</html>