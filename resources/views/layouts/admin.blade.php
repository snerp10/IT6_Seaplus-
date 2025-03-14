@if(auth()->check() && auth()->user()->role !== 'Admin')
    <script>window.location = "{{ route('admin.dashboard.index') }}";</script>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSM Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            /* New Color Palette - Gold, Black, Myrtle */
            --gold-color: #D4AF37;     /* Primary accent color */
            --black-color:rgba(34, 34, 34, 0.94);    /* Dark elements color */
            --myrtle-color: #1A535C;   /* Secondary accent color */
            --white-color:rgba(249, 249, 249, 0.08);    /* Background color */
            --light-gray: #F5F5F5;     /* Secondary background color */
            --text-dark: #333333;      /* Main text color */
            --text-light: #FFFFFF;     /* Light text color */
            --text-muted: #777777;     /* Muted text color */
            
            /* Bootstrap overrides */
            --bs-primary: var(--myrtle-color);
            --bs-secondary: var(--black-color);
            --bs-success: #2E8B57;
            --bs-info: #5BC0DE;
            --bs-warning: var(--gold-color);
            --bs-danger: #D9534F;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white-color);
            background-image: linear-gradient(to bottom right, rgba(245,245,245,0.7), rgba(255,255,255,1));
            margin: 0;
            min-height: 100vh;
            color: var(--text-dark);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        /* Sidebar Styling */
        #sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            width: 250px;
            background: var(--black-color);
            color: white;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, var(--black-color) 100%);
        }
        
        #sidebar.active {
            margin-left: -250px;
            box-shadow: none;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        #sidebar .sidebar-header h4 {
            color: var(--gold-color);
            font-size: 1.5rem;
            margin-bottom: 0;
            letter-spacing: 1px;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 12px 15px;
            font-size: 0.80rem;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 2px solid transparent;
            position: relative;
            z-index: 1;
            overflow: hidden;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        #sidebar ul li a:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(212, 175, 55, 0.1);
            transform: translateX(-100%);
            transition: all 0.3s ease;
            z-index: -1;
        }
        
        #sidebar ul li a:hover:before {
            transform: translateX(0);
        }
        
        #sidebar ul li a:hover {
            color: var(--gold-color);
            text-decoration: none;
            border-left: 2px solid var(--gold-color);
        }
        
        #sidebar ul li.active > a {
            color: var(--gold-color);
            background: rgba(26, 83, 92, 0.2);
            border-left: 2px solid var(--gold-color);
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Main Content Area */
        #content {
            width: calc(100% - 250px);
            min-height: 100vh;
            position: absolute;
            top: 0;
            right: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            padding: 20px;
            padding-top: 80px;
            background-color: var(--white-color);
            background-image: radial-gradient(circle at top right, rgba(245,245,245,0.5), transparent 70%);
        }
        
        #content.active {
            width: 100%;
        }
        
        /* Top Navbar */
        .main-navbar {
            padding: 10px 20px;
            background: var(--white-color);
            border: none;
            border-radius: 0;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            width: calc(100% - 250px);
            right: 0;
            top: 0;
            z-index: 999;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        .main-navbar.active {
            width: 100%;
        }
        
        /* Button Styling with Refined Hover Effect */
        .btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 1;
            text-transform: uppercase;
            font-size: 0.70rem;
            letter-spacing: 0.8px;
            font-weight: 500;
            border-radius: 4px;
            padding: 8px 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn:after {
            content: '';
            position: absolute;
            top: 0;
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
            background-color: var(--myrtle-color);
            border-color: var(--myrtle-color);
            box-shadow: 0 2px 4px rgba(26, 83, 92, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #14424a;
            border-color: #14424a;
            box-shadow: 0 4px 6px rgba(26, 83, 92, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--black-color);
            border-color: var(--black-color);
            box-shadow: 0 2px 4px rgba(34, 34, 34, 0.2);
        }

        .btn-secondary:hover {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
            box-shadow: 0 4px 6px rgba(34, 34, 34, 0.3);
        }
        
        .btn-gold {
            background-color: var(--gold-color);
            border-color: var(--gold-color);
            color: var(--black-color);
            box-shadow: 0 2px 4px rgba(212, 175, 55, 0.2);
            font-weight: 600;
        }
        
        .btn-gold:hover {
            background-color: #c4a22f;
            border-color: #c4a22f;
            color: var(--text-dark);
            box-shadow: 0 4px 6px rgba(212, 175, 55, 0.3);
        }
        
        /* Card Styling */
        .card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
            background-image: linear-gradient(to bottom, rgba(164, 164, 164, 0.24), rgba(236, 196, 66, 0.47));
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: var(--black-color);
            color: white;
            border-radius: 6px 6px 0 0 !important;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
        }
        
        .card-header h6 {
            font-size: 0.9rem;
            margin: 0;
            color: var(--gold-color);
            letter-spacing: 0.5px;
        }
        
        /* Table Styling */
        .table {
            color: var(--text-dark);
            font-size: 0.85rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.05);
        }
        
        .table thead th {
            background-color: var(--black-color);
            color: var(--gold-color);
            font-weight: 500;
            border-bottom: none;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        /* Badge Styling */
        .badge {
            font-size: 0.7rem;
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }
        
        .bg-gold {
            background-color: var(--gold-color) !important;
            color: var(--black-color);
        }
        
        .bg-myrtle {
            background-color: var(--myrtle-color) !important;
            color: white;
        }
        
        /* Form Controls */
        .form-control {
            border-radius: 3px;
            border: 1px solid rgba(0,0,0,0.1);
            font-size: 0.85rem;
            box-shadow: none;
            transition: all 0.3s;
            background-color: var(--white-color);
            color: var(--text-dark);
        }
        
        .form-control:focus {
            border-color: var(--gold-color);
            box-shadow: 0 0 0 0.1rem rgba(212, 175, 55, 0.25);
        }
        
        .form-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        /* Hamburger Menu Button */
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
            width: 22px;
            height: 2px;
            background: var(--black-color);
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
            width: 22px;
            height: 2px;
            background: var(--black-color);
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .hamburger-btn span:before {
            top: -7px;
        }
        
        .hamburger-btn span:after {
            top: 7px;
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
        
        /* Profile and User Elements */
        .profile-img {
            height: 36px;
            width: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gold-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 4px;
            font-size: 0.85rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        /* Custom Stats Cards */
        .stats-card {
            border-left: 4px solid;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        
        .border-left-gold {
            border-left-color: var(--gold-color) !important;
        }
        
        .border-left-black {
            border-left-color: var(--black-color) !important;
        }
        
        .border-left-myrtle {
            border-left-color: var(--myrtle-color) !important;
        }
        
        /* Custom Shadows */
        .shadow-sm {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        }
        
        .shadow {
            box-shadow: 0 4px 8px rgba(0,0,0,0.08) !important;
        }
        
        .shadow-lg {
            box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        }
        
        /* Text Colors */
        .text-gold {
            color: var(--gold-color) !important;
        }
        
        .text-myrtle {
            color: var(--myrtle-color) !important;
        }
        
        .text-black {
            color: var(--black-color) !important;
        }
        
        .text-muted {
            color: var(--text-muted) !important;
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
            .btn {
                font-size: 0.6rem;
            }
        }

        /* Custom spacing utilities */
        .gap-2 {
            gap: 0.5rem !important;
        }
        
        /* Override existing primary button colors to gold */
        .btn-primary {
            background-color: var(--gold-color);
            border-color: var(--gold-color);
            color: var(--black-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #c4a22f;
            border-color: #c4a22f;
            color: var(--black-color);
        }
        
        .btn-dark {
            background-color: var(--black-color);
            border-color: var(--black-color);
        }
        
        .btn-dark:hover {
            background-color: #000000;
            border-color: #000000;
        }
        
        /* Custom tooltip styling */
        .tooltip .tooltip-inner {
            background-color: var(--black-color);
            color: var(--gold-color);
            font-size: 0.75rem;
        }
        
        .bs-tooltip-auto[x-placement^=top] .arrow::before, 
        .bs-tooltip-top .arrow::before {
            border-top-color: var(--black-color);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="d-flex justify-content-center">
                    <h4>KSM</h4>
                </div>
                <p class="text-center text-light mb-0"><small>Sand & Gravel Admin</small></p>
            </div>

            <ul class="list-unstyled components" style="background: linear-gradient(to bottom, rgb(0,0,0), rgba(236, 196, 66, 0.21))">
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
                
                <li class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
                    <a href="{{ route('admin.customers.index') }}">
                        <span class="sidebar-icon"><i class="fas fa-users"></i></span> Customers
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
                
                <li class="border-top mt-3 pt-2 d-flex justify-content-center">
                    <a href="#settingsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle w-100">
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
                
                <li class="mt-3 d-flex justify-content-center">
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
            <nav class="main-navbar d-flex align-items-center" style="background: linear-gradient(to right,rgba(74, 74, 74, 0.35),rgba(224, 212, 173, 0.47),rgba(224, 198, 114, 0.53),rgb(236, 196, 66));">
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
                                @elseif(request()->is('admin/customers*'))
                                    <i class="fas fa-users me-2"></i> Customers Management
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
                                <button class="btn btn-primary btn-sm " type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--light-color);">
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
                                @php
                                    $notifications = \App\Models\Notification::orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                    $unreadCount = \App\Models\Notification::where('is_read', false)->count();
                                @endphp
                                <button class="btn btn-primary notification-indicator" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--light-color);">
                                    <i class="fas fa-bell"></i>
                                    @if($unreadCount > 0)
                                        <span class="badge rounded-pill bg-danger">{{ $unreadCount }}</span>
                                    @endif
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsDropdown" style="width: 280px; max-height: 400px; overflow-y: auto;">
                                    <li>
                                        <div class="dropdown-header" style="background-color: var(--secondary-color); color: white;">
                                            <h6 class="mb-0">Notifications</h6>
                                        </div>
                                    </li>
                                    @forelse($notifications as $notification)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center py-2 {{ $notification->is_read ? 'text-muted' : 'fw-bold' }}" 
                                               href="{{ $notification->type == 'low_stock' ? route('admin.inventories.low_stock_alerts') : 
                                                      ($notification->type == 'order_complete' ? route('admin.orders.index') : '#') }}">
                                                <div class="me-3">
                                                    <div class="{{ $notification->type == 'order_complete' ? 'bg-success' : 
                                                              ($notification->type == 'low_stock' ? 'bg-warning' : 'bg-info') }} text-white rounded-circle d-flex justify-content-center align-items-center" 
                                                         style="width: 38px; height: 38px;">
                                                        <i class="fas {{ $notification->type == 'order_complete' ? 'fa-check' : 
                                                                   ($notification->type == 'low_stock' ? 'fa-exclamation-triangle' : 'fa-info-circle') }}"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold">{{ $notification->title }}</p>
                                                    <p class="small text-muted mb-0">{{ $notification->message }}</p>
                                                    <p class="small text-muted mb-0">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li>
                                            <div class="dropdown-item text-center py-3">
                                                <i class="fas fa-bell-slash text-muted mb-2"></i>
                                                <p class="mb-0">No notifications</p>
                                            </div>
                                        </li>
                                    @endforelse
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-center small" href="#" data-bs-toggle="modal" data-bs-target="#allNotificationsModal">
                                            View All Notifications
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- User Profile -->
                            <div class="dropdown">
                                <button class="btn btn-primary  d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--light-color);">
                                    <img src="{{ asset('images/60111.jpg') }}" alt="Admin User" class="profile-img">
                                    <span class="ms-2 d-none d-md-inline-block">Admin User</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                    <li>
                                        <div class="dropdown-header text-center">
                                            <img src="{{ asset('images/60111.jpg') }}" alt="Admin User" class="rounded-circle mb-2" width="60" height="60">
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

    <!-- All Notifications Modal -->
    <div class="modal fade" id="allNotificationsModal" tabindex="-1" aria-labelledby="allNotificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="allNotificationsModalLabel">
                        <i class="fas fa-bell me-2"></i> All Notifications
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            $allNotifications = \App\Models\Notification::orderBy('created_at', 'desc')
                                ->paginate(10);
                        @endphp
                        
                        @forelse($allNotifications as $notification)
                            <div class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ $notification->is_read ? 'text-muted bg-light' : '' }}">
                                <div class="me-3">
                                    <div class="{{ $notification->type == 'order_complete' ? 'bg-success' : 
                                          ($notification->type == 'low_stock' ? 'bg-warning' : 'bg-info') }} text-white rounded-circle d-flex justify-content-center align-items-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas {{ $notification->type == 'order_complete' ? 'fa-check' : 
                                               ($notification->type == 'low_stock' ? 'fa-exclamation-triangle' : 'fa-info-circle') }} fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $notification->is_read ? '' : 'fw-bold' }}">{{ $notification->title }}</h6>
                                    <p class="mb-1">{{ $notification->message }}</p>
                                    <small class="text-muted">{{ $notification->created_at->format('M d, Y h:i A') }} ({{ $notification->created_at->diffForHumans() }})</small>
                                </div>
                                <div>
                                    @if(!$notification->is_read)
                                        <form action="{{ route('admin.dashboard.notifications.mark-read', $notification->notification_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as Read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-secondary border">Read</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <p>You don't have any notifications yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <div>
                        {{ $allNotifications->links() }}
                    </div>
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-chart-line me-1"></i> Go to Dashboard
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call stack scripts only once -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

    <!-- Call stack scripts only once -->
    @stack('scripts')

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
</body>
</html>
