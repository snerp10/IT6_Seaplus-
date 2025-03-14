<div class="container-fluid">
    <div class="row">
        <nav id="customer-sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-4">
                <div class="text-center mb-4 px-3">
                    <div class="logo-circle mx-auto mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="KSM SeaPlus+" height="60">
                    </div>
                    <h6 class="text-light opacity-75 mb-0">Welcome back,</h6>
                    <h5 class="text-white fw-bold">{{ auth()->user()->customer->fname ?? 'Customer' }}</h5>
                </div>
                
                <hr class="mx-3 bg-light opacity-25">
                
                <ul class="nav flex-column px-3">
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('customer.dashboard.index') ? 'active' : '' }}" href="{{ route('customer.dashboard.index') }}">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle me-3">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>
                    
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('customer.products.*') ? 'active' : '' }}" href="{{ route('customer.products.index') }}">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle me-3">
                                    <i class="fas fa-box"></i>
                                </div>
                                <span>Products</span>
                            </div>
                        </a>
                    </li>
                    
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle me-3">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <span>Orders</span>
                                @php
                                    $pendingOrders = auth()->user()->customer->orders()->whereIn('order_status', ['Pending', 'Processing'])->count();
                                @endphp
                                @if($pendingOrders > 0)
                                    <span class="badge rounded-pill bg-gold text-dark ms-auto">{{ $pendingOrders }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span>My Profile</span>
                            </div>
                        </a>
                    </li>
                </ul>
                
                <hr class="mx-3 bg-light opacity-25 mt-4">
                
                <div class="px-3 mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-gold btn-sm w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</div>

<style>
    .logo-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        padding: 2px;
        border: 2px solid var(--gold-color);
        overflow: hidden;
    }
    
    .logo-circle img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    .icon-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold-color);
        transition: all 0.3s;
    }
    
    .nav-link:hover .icon-circle {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }
    
    .nav-link.active .icon-circle {
        background: var(--gold-color);
        color: var(--black-color);
    }
    
    .sidebar .nav-link {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
    }
    
    .sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--gold-color);
    }
    
    .sidebar .nav-link.active {
        background: rgba(255, 255, 255, 0.1);
    }
    
    
    .btn-gold {
        background-color: var(--gold-color);
        border-color: var(--gold-color);
        color: var(--black-color);
    }
    
    .btn-gold:hover {
        background-color: #c9a633;
        border-color: #c9a633;
        color: var(--black-color);
    }
    
    .bg-gold {
        background-color: var(--gold-color);
    }
</style>
