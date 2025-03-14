<nav class="navbar navbar-expand-md {{ auth()->check() && auth()->user()->role === 'Customer' ? 'navbar-dark bg-custom-dark' : 'navbar-light bg-custom-light' }} shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <div class="navbar-logo-circle me-2">
                <img src="{{ asset('images/logo.png') }}" alt="KSM SeaPlus+">
            </div>
            <span class="{{ auth()->check() && auth()->user()->role === 'Customer' ? 'text-light' : 'myrtle-text' }} fw-bold">KSM Sand&amp;Gravel</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @if(!auth()->check() || auth()->user()->role !== 'Customer')
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('customer.products.*') ? 'active' : '' }}" href="{{ route('customer.products.index') }}">
                        <i class="fas fa-box me-1"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->is('about') ? 'active' : '' }}" href="{{ url('/about') }}">
                        <i class="fas fa-info-circle me-1"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->is('contact') ? 'active' : '' }}" href="{{ url('/contact') }}">
                        <i class="fas fa-phone me-1"></i> Contact
                    </a>
                </li>
                @endif
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item me-2">
                        <a class="nav-link btn btn-outline-gold px-3" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> {{ __('Login') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-gold px-3" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i> {{ __('Register') }}
                        </a>
                    </li>
                @else
                    @if(auth()->user()->role === 'Customer')
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="{{ route('customer.orders.index') }}">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                            @php
                                $pendingOrders = auth()->user()->customer->orders()->whereIn('order_status', ['Pending', 'Processing'])->count();
                            @endphp
                            @if($pendingOrders > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-gold text-dark">
                                    {{ $pendingOrders }}
                                </span>
                            @endif
                        </a>
                    </li>
                    
                    <li class="nav-item me-3">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                2
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 notification-dropdown p-0" aria-labelledby="notificationsDropdown">
                            <div class="card">
                                <div class="card-header bg-gold text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0">Notifications</h6>
                                        <span class="badge bg-dark text-light">2 New</span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <a href="#" class="list-group-item list-group-item-action p-3 unread">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Order #12345 Confirmed</h6>
                                                <small class="text-muted">3 hours ago</small>
                                            </div>
                                            <p class="mb-1 small">Your order has been confirmed and is being processed.</p>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action p-3 unread">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">New Product Available</h6>
                                                <small class="text-muted">1 day ago</small>
                                            </div>
                                            <p class="mb-1 small">Check out our new premium sand collection!</p>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action p-3">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Welcome to KSM SeaPlus+</h6>
                                                <small class="text-muted">5 days ago</small>
                                            </div>
                                            <p class="mb-1 small">Thank you for creating an account with us.</p>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="#" class="gold-text">View All Notifications</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                    
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <div class="profile-img-small me-2">
                                <img src="{{ asset('images/cud.jpg') }}" alt="Profile" class="rounded-circle">
                            </div>
                            <span>{{ auth()->user()->customer->fname ?? auth()->user()->username }}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                            @if(auth()->user()->role === 'Customer')
                            <h6 class="dropdown-header d-flex align-items-center">
                                <img src="{{ asset('images/cud.jpg') }}" alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <strong>{{ auth()->user()->customer->fname }} {{ auth()->user()->customer->lname }}</strong>
                                    <small class="text-muted d-block">{{ auth()->user()->email }}</small>
                                </div>
                            </h6>
                            <div class="dropdown-divider"></div>
                            
                            <a class="dropdown-item" href="{{ route('customer.dashboard.index') }}">
                                <i class="fas fa-tachometer-alt me-2 gold-text"></i> Dashboard
                            </a>
                            <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                <i class="fas fa-user me-2 gold-text"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('customer.orders.index') }}">
                                <i class="fas fa-shopping-bag me-2 gold-text"></i> My Orders
                            </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar-logo-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px solid var(--gold-color);
    }
    
    .navbar-logo-circle img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
    
    .bg-custom-dark {
        background-image: linear-gradient(135deg, #222222 0%, #333333 100%);
    }
    
    .bg-custom-light {
        background-image: linear-gradient(to right, #ffffff, #f8f9fc, #ffffff);
    }
    
    .navbar .nav-link {
        position: relative;
        font-weight: 500;
    }
    
    .navbar-light .nav-link.active {
        color: var(--myrtle-color) !important;
    }
    
    .navbar-light .nav-link.active:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background-color: var(--gold-color);
    }
    
    .navbar-dark .nav-link.active {
        color: var(--gold-color) !important;
    }
    
    .profile-img-small {
        width: 30px;
        height: 30px;
        overflow: hidden;
        border-radius: 50%;
    }
    
    .profile-img-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .dropdown-header {
        padding: 0.75rem 1rem;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }
    
    .dropdown-item:hover {
        background-color: rgba(212, 175, 55, 0.1);
    }
    
    .dropdown-item:active, .dropdown-item:focus {
        background-color: rgba(26, 83, 92, 0.1);
        color: var(--myrtle-color);
    }
    
    .notification-dropdown {
        width: 320px;
        max-width: 100vw;
    }
    
    .notification-dropdown .card {
        border: none;
    }
    
    .notification-dropdown .list-group-item.unread {
        border-left: 4px solid var(--gold-color);
    }
    
    .notification-dropdown .list-group-item.unread h6 {
        font-weight: 600;
    }
    
    .notification-dropdown .list-group-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    @media (max-width: 767.98px) {
        .navbar {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        
        .notification-dropdown {
            width: 100%;
        }
    }
</style>
