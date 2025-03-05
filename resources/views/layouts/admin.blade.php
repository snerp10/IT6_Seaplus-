@if(auth()->check() && auth()->user()->role !== 'Admin')
    <script>window.location = "{{ route('dashboard.index') }}";</script>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSM SeaPlus+ Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            /* Custom Color Palette */
            --dark-color: #39393A;      /* 50% usage - Main dark color */
            --light-color: #E6E6E6;     /* 30% usage - Light background color */
            --accent-color: #FF8552;    /* 10% usage - Accent/highlight color */
            --secondary-color: #297373; /* 10% usage - Secondary accent */
            --hover-color:rgb(247, 223, 41);     /* Hover state color */
            
            /* Bootstrap overrides */
            --bs-primary: var(--secondary-color);
            --bs-secondary: var(--dark-color);
            --bs-success: #2E8B57;
            --bs-info: #5BC0DE;
            --bs-warning: var(--accent-color);
            --bs-danger: #D9534F;
        }
        
        body {
            font-family: 'Montserrat', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            margin: 0;
            min-height: 100vh;
            color: var(--dark-color);
        }
        
        /* Sidebar Styling */
        #sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            width: 250px;
            background: var(--dark-color);
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0) 100%);
        }
        
        #sidebar.active {
            margin-left: -250px;
            box-shadow: none;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 12px 15px;
            font-size: 1em;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        #sidebar ul li a:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(233, 215, 88, 0.1);
            transform: translateX(-100%);
            transition: all 0.3s ease;
            z-index: -1;
        }
        
        #sidebar ul li a:hover:before {
            transform: translateX(0);
        }
        
        #sidebar ul li a:hover {
            color: #fff;
            text-decoration: none;
            border-left: 3px solid var(--hover-color);
        }
        
        #sidebar ul li.active > a {
            color: #fff;
            background: rgba(41, 115, 115, 0.4);
            border-left: 3px solid var(--hover-color);
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }
        
        #sidebar a[data-bs-toggle="collapse"] {
            position: relative;
        }
        
        .dropdown-toggle::after {
            display: block;
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
        }
        
        /* Main Content Area */
        #content {
            width: calc(100% - 250px);
            min-height: 100vh;
            position: absolute;
            top: 0;
            right: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            padding: 20px;
            padding-top: 80px;
        }
        
        #content.active {
            width: 100%;
        }
        
        /* Top Navbar */
        .main-navbar {
            padding: 10px 20px;
            background: white;
            border: none;
            border-radius: 0;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: calc(100% - 250px);
            right: 0;
            top: 0;
            z-index: 999;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .main-navbar.active {
            width: 100%;
        }
        
        .navbar-btn {
            box-shadow: none;
            outline: none !important;
            border: none;
        }
        
        /* UI Components */
        .sidebar-icon {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .card {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: var(--dark-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }
        
        .card-header:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(120deg, rgba(255,255,255,0) 30%, rgba(255,255,255,0.1) 38%, rgba(255,255,255,0.1) 40%, rgba(255,255,255,0) 48%);
            background-size: 200% 100%;
            animation: shine 2s infinite;
        }
        
        @keyframes shine {
            to {
                background-position: 200% 0;
            }
        }
        
        /* Button Styling with Texture */
        .btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 1;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            font-weight: 500;
            border-radius: 5px;
        }
        
        .btn:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(to bottom, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
            z-index: -2;
        }
        
        .btn:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background-color: rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: -1;
        }
        
        .btn:hover:before {
            width: 100%;
        }
        
        .btn:active {
            transform: scale(0.97);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            box-shadow: 0 4px 6px rgba(41, 115, 115, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #236363;
            border-color: #236363;
            box-shadow: 0 6px 10px rgba(41, 115, 115, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
            box-shadow: 0 4px 6px rgba(57, 57, 58, 0.2);
        }

        .btn-secondary:hover {
            background-color: #2a2a2b;
            border-color: #2a2a2b;
            box-shadow: 0 6px 10px rgba(57, 57, 58, 0.3);
        }
        
        .btn-accent {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
            box-shadow: 0 4px 6px rgba(255, 133, 82, 0.2);
        }
        
        .btn-accent:hover {
            background-color: #E97747;
            border-color: #E97747;
            color: white;
            box-shadow: 0 6px 10px rgba(255, 133, 82, 0.3);
        }
        
        /* Hamburger Menu Button with Animation */
        .hamburger-btn {
            width: 35px;
            height: 35px;
            position: relative;
            cursor: pointer;
            display: inline-block;
            padding: 0;
            background: transparent;
            border: none;
            outline: none;
        }
        
        .hamburger-btn span {
            width: 25px;
            height: 2px;
            background: var(--dark-color);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .hamburger-btn span:before,
        .hamburger-btn span:after {
            content: '';
            position: absolute;
            width: 25px;
            height: 2px;
            background: var(--dark-color);
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .hamburger-btn span:before {
            top: -8px;
        }
        
        .hamburger-btn span:after {
            top: 8px;
        }
        
        .hamburger-btn.active span {
            background: transparent;
        }
        
        .hamburger-btn.active span:before {
            top: 0;
            transform: rotate(45deg);
        }
        
        .hamburger-btn.active span:after {
            top: 0;
            transform: rotate(-45deg);
        }
        
        /* Rest of the styles remain unchanged */
        .badge.bg-accent {
            background-color: var(--accent-color) !important;
        }
        
        /* User Profile Elements */
        .profile-img {
            height: 36px;
            width: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary-color);
        }
        
        /* Notification Indicator */
        .notification-indicator {
            position: relative;
        }
        
        .notification-indicator .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                padding-top: 70px;
            }
            #content.active {
                width: calc(100% - 250px);
            }
            .main-navbar {
                width: 100%;
            }
            .main-navbar.active {
                width: calc(100% - 250px);
            }
        }
        
        /* Data Tables */
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            color: var(--dark-color);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(233, 215, 88, 0.1);
        }
        
        /* Custom Borders & Accents */
        .border-accent {
            border-color: var(--accent-color) !important;
        }
        
        .border-secondary {
            border-color: var(--secondary-color) !important;
        }
        
        .border-left-accent {
            border-left: 4px solid var(--accent-color) !important;
        }
        
        .border-left-secondary {
            border-left: 4px solid var(--secondary-color) !important;
        }
        
        /* Text Colors */
        .text-accent {
            color: var(--accent-color) !important;
        }
        
        .text-secondary-color {
            color: var(--secondary-color) !important;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4>KSM SeaPlus+</h4>
                <p class="text-light mb-0"><small>Sand & Gravel Admin</small></p>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <span class="sidebar-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-cubes"></i></span> Products
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/orders*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-shopping-cart"></i></span> Orders
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/employees*') ? 'active' : '' }}">
                    <a href="{{ route('admin.employees.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-users"></i></span> Employees
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/deliveries*') ? 'active' : '' }}">
                    <a href="{{ route('admin.deliveries.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-truck"></i></span> Deliveries
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/inventories*') ? 'active' : '' }}">
                    <a href="{{ route('admin.inventories.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-warehouse"></i></span> Inventory
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/payments*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payments.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-money-bill-wave"></i></span> Payments
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/sales_reports*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sales_reports.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-chart-line"></i></span> Sales
                    </a>
                </li>
                
                <li class="{{ request()->is('admin/suppliers*') ? 'active' : '' }}">
                    <a href="{{ route('admin.suppliers.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-truck-loading"></i></span> Suppliers
                    </a>
                </li>
                
                <li class="border-top mt-3 pt-2">
                    <a href="#settingsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <span class="sidebar-icon"><i class="fas fa-cog"></i></span> Settings
                    </a>
                    <ul class="collapse list-unstyled" id="settingsSubmenu">
                        <li>
                            <a href="#" class="ps-4">
                                <span class="sidebar-icon"><i class="fas fa-user-cog"></i></span> Profile
                            </a>
                        </li>
                        <li>
                            <a href="#" class="ps-4">
                                <span class="sidebar-icon"><i class="fas fa-tools"></i></span> System
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="mt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn w-100 text-start ps-3" style="background: transparent; color: white; border: none;">
                            <span class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></span> Logout
                        </button>
                    </form>
                </li>
            </ul>

            <div class="p-4 text-center text-light">
                <small>KSM SeaPlus+ &copy; {{ date('Y') }}</small>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="main-navbar d-flex align-items-center">
                <div class="container-fluid px-0">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <!-- Improved Hamburger Button -->
                            <button type="button" id="sidebarCollapse" class="hamburger-btn">
                                <span></span>
                            </button>
                            
                            <!-- Current Page Title - Show on larger screens -->
                            <span class="d-none d-md-inline-block ms-3 fw-bold text-uppercase">
                                @if(request()->is('admin/dashboard*'))
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                @elseif(request()->is('admin/products*'))
                                    <i class="fas fa-cubes me-2"></i> Products Management
                                @elseif(request()->is('admin/orders*'))
                                    <i class="fas fa-shopping-cart me-2"></i> Orders Management
                                @elseif(request()->is('admin/payments*'))
                                    <i class="fas fa-money-bill-wave me-2"></i> Payments Management
                                @elseif(request()->is('admin/deliveries*'))
                                    <i class="fas fa-truck me-2"></i> Deliveries Management
                                @elseif(request()->is('admin/inventories*'))
                                    <i class="fas fa-warehouse me-2"></i> Inventory Management
                                @elseif(request()->is('admin/employees*'))
                                    <i class="fas fa-users me-2"></i> Employees Management
                                @elseif(request()->is('admin/sales_reports*'))
                                    <i class="fas fa-chart-line me-2"></i> Sales Reports
                                @elseif(request()->is('admin/suppliers*'))
                                    <i class="fas fa-truck-loading me-2"></i> Suppliers Management
                                @else
                                    KSM SeaPlus+ Admin
                                @endif
                            </span>
                        </div>
                        
                        <!-- Right Side Elements -->
                        <div class="d-flex align-items-center">
                            <!-- Quick Actions Dropdown -->
                            <div class="dropdown me-3 d-none d-md-block">
                                <button class="btn btn-accent btn-sm dropdown-toggle" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bolt me-1"></i> Quick Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="quickActionsDropdown">
                                    <li><a class="dropdown-item" href="{{ route('admin.orders.create') }}"><i class="fas fa-plus-circle me-2"></i> New Order</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.create') }}"><i class="fas fa-plus-circle me-2"></i> New Product</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.payments.create') }}"><i class="fas fa-plus-circle me-2"></i> Record Payment</a></li>
                                </ul>
                            </div>
                            
                            <!-- Notifications -->
                            <div class="dropdown me-3">
                                <button class="btn notification-indicator" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--light-color);">
                                    <i class="fas fa-bell"></i>
                                    <span class="badge rounded-pill bg-accent">3</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsDropdown" style="width: 280px; max-height: 400px; overflow-y: auto;">
                                    <li>
                                        <div class="dropdown-header" style="background-color: var(--secondary-color); color: white;">
                                            <h6 class="mb-0">Notifications</h6>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                            <div class="me-3">
                                                <div class="bg-accent text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold">New order received</p>
                                                <p class="small text-muted mb-0">Order #12345 - 30 min ago</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                            <div class="me-3">
                                                <div class="bg-warning text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold">Low inventory alert</p>
                                                <p class="small text-muted mb-0">Sand G1 - 2 hours ago</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                            <div class="me-3">
                                                <div class="bg-secondary-color text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 38px; height: 38px; background-color: var(--secondary-color);">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold">Payment confirmed</p>
                                                <p class="small text-muted mb-0">Order #12340 - Yesterday</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-center small" href="#">
                                            View All Notifications
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- User Profile -->
                            <div class="dropdown">
                                <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--light-color);">
                                    <img src="https://via.placeholder.com/150" alt="Admin User" class="profile-img">
                                    <span class="ms-2 d-none d-md-inline-block">Admin User</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                    <li>
                                        <div class="dropdown-header text-center">
                                            <img src="https://via.placeholder.com/150" alt="Admin User" class="rounded-circle mb-2" width="60" height="60">
                                            <h6 class="mb-0">Admin User</h6>
                                            <p class="small text-muted mb-0">Administrator</p>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Account Settings</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-question-circle me-2"></i> Help Center</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="px-4 py-1">
                                            @csrf
                                            <button type="submit" class="btn btn-accent w-100">
                                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                @yield('admin.content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function () {
            // Enhanced Toggle sidebar with animation
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
                $('.main-navbar').toggleClass('active');
                $(this).toggleClass('active');
                
                // Store sidebar state in localStorage
                localStorage.setItem('sidebarState', $('#sidebar').hasClass('active') ? 'collapsed' : 'expanded');
            });
            
            // Restore sidebar state from localStorage
            var sidebarState = localStorage.getItem('sidebarState');
            if(sidebarState === 'collapsed') {
                $('#sidebar').addClass('active');
                $('#content').addClass('active');
                $('.main-navbar').addClass('active');
                $('#sidebarCollapse').addClass('active');
            }
            
            // Make current dropdown menu item active
            var url = window.location.href;
            $('.components li a').filter(function() {
                return this.href == url;
            }).parent().addClass('active');
            
            // Add custom class to dropdown toggles
            $('.components li').has('ul').find('a.dropdown-toggle').addClass('has-submenu');
            
            // Auto-collapse sidebar on small screens, but respect user preference
            if ($(window).width() < 768 && sidebarState !== 'expanded') {
                $('#sidebar').addClass('active');
                $('#content').addClass('active');
                $('.main-navbar').addClass('active');
                $('#sidebarCollapse').addClass('active');
            }
            
            // Add ripple effect to buttons
            $('.btn').on('click', function(e) {
                var x = e.clientX - e.target.getBoundingClientRect().left;
                var y = e.clientY - e.target.getBoundingClientRect().top;
                
                var ripple = document.createElement('span');
                ripple.classList.add('ripple');
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                this.appendChild(ripple);
                
                setTimeout(function() {
                    ripple.remove();
                }, 600);
            });
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
        
        // Prevent page reload on empty "#" href links
        document.addEventListener('click', function(e) {
            var target = e.target;
            if (target.tagName === 'A' && target.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
