<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="KSM SeaPlus+ - Your trusted provider of quality sand, gravel, and construction materials">
    <meta name="author" content="KSM SeaPlus+">
    <title>{{ config('app.name', 'KSM SeaPlus+') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --gold-color: #D4AF37;     /* Primary accent color */
            --black-color: rgba(34, 34, 34, 0.94);    /* Dark elements color */
            --myrtle-color: #1A535C;   /* Secondary accent color */
            --white-color: rgba(249, 249, 249, 0.08);    /* Background color */
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
            
            /* Legacy variables for compatibility */
            --primary: var(--myrtle-color);
            --secondary: var(--black-color);
            --success: #2E8B57;
            --info: #5BC0DE;
            --warning: var(--gold-color);
            --danger: #D9534F;
            --light: var(--light-gray);
            --dark: var(--black-color);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-dark);
        }
        
        .bg-gradient-primary {
            background-color: var(--myrtle-color);
            background-image: linear-gradient(180deg, var(--myrtle-color) 10%, #0d3338 100%);
            color: var(--text-light);
        }
        
        .bg-gradient-gold {
            background-color: var(--gold-color);
            background-image: linear-gradient(180deg, var(--gold-color) 10%, #b3941f 100%);
            color: var(--text-dark);
        }
        
        .bg-gradient-dark {
            background-color: var(--black-color);
            background-image: linear-gradient(180deg, var(--black-color) 10%, #111111 100%);
            color: var(--text-light);
        }
        
        .border-left-primary {
            border-left: 4px solid var(--myrtle-color) !important;
        }
        
        .border-left-warning {
            border-left: 4px solid var(--gold-color) !important;
        }
        
        .border-left-secondary {
            border-left: 4px solid var(--black-color) !important;
        }
        
        .btn-primary {
            background-color: var(--myrtle-color);
            border-color: var(--myrtle-color);
            color: var(--text-light);
        }
        
        .btn-primary:hover {
            background-color: #14424a;
            border-color: #14424a;
        }
        
        .btn-warning {
            background-color: var(--gold-color);
            border-color: var(--gold-color);
            color: var(--text-dark);
        }
        
        .btn-warning:hover {
            background-color: #c9a633;
            border-color: #c9a633;
            color: var(--text-dark);
        }
        
        .btn-secondary {
            background-color: var(--black-color);
            border-color: var(--black-color);
        }
        
        .btn-secondary:hover {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: var(--myrtle-color);
        }
        
        .navbar-light {
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .navbar-dark {
            background-color: var(--black-color);
        }
        
        .nav-link.active {
            color: var(--gold-color) !important;
            font-weight: 600;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--black-color);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var (--gold-color);
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--gold-color);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.35rem;
        }
        
        .welcome-section {
            min-height: 70vh;
            background-image: linear-gradient(rgba(26, 83, 92, 0.85), rgba(26, 83, 92, 0.85)), url('/images/background.jpg');
            background-size: cover;
            background-position: center;
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .welcome-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20%;
            background: linear-gradient(to bottom, rgba(0,0,0,0), var(--light-gray));
        }
        
        .auth-card {
            margin-top: 2rem;
            margin-bottom: 2rem;
            border-top: 4px solid var(--gold-color);
        }
        
        .page-title {
            color: var(--myrtle-color);
            font-weight: 600;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--gold-color);
        }
        
        footer {
            background-color: var(--black-color);
            color: var(--text-light);
        }
        
        footer a {
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none;
        }
        
        footer a:hover {
            color: var(--gold-color) !important;
            text-decoration: none;
        }
        
        .gold-text {
            color: var(--gold-color);
        }
        
        .myrtle-text {
            color: var(--myrtle-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        /* Custom form styling */
        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            padding: 0.6rem 1rem;
        }
        
        .form-control:focus {
            border-color: var(--gold-color);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        
        /* Card hover effects */
        .hover-card {
            transition: all 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(0, 0, 0, 0.2);
        }

        /* Button styles with gold */
        .btn-gold {
            background-color: var(--gold-color);
            border-color: var(--gold-color);
            color: var(--black-color);
            font-weight: 500;
        }
        
        .btn-gold:hover, .btn-gold:focus {
            background-color: #c9a633;
            border-color: #c9a633;
            color: var(--black-color);
        }
        
        .btn-outline-gold {
            background-color: transparent;
            border-color: var(--gold-color);
            color: var(--gold-color);
        }
        
        .btn-outline-gold:hover, .btn-outline-gold:focus {
            background-color: var(--gold-color);
            color: var(--black-color);
        }
        
        .bg-gold {
            background-color: var(--gold-color) !important;
        }
        
        .border-gold {
            border-color: var(--gold-color) !important;
        }
        /* Image styling */
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        .img-thumbnail {
            padding: 0.25rem;
            background-color: var(--white-color);
            border: 1px solid var(--light-gray);
            border-radius: 0.25rem;
            max-width: 100%;
            height: auto;
        }
        
        .img-rounded {
            border-radius: 0.5rem;
        }
        
        .img-circle {
            border-radius: 50%;
            object-fit: cover;
        }
        
        .logo {
            border-radius: 50%;
            object-fit: cover;
        }

    </style>
    
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')
    
    <div class="container-fluid">
        <div class="row">
            @auth                                          
                @if(auth()->user()->role === 'Customer')
                    <div class="col-md-3 col-lg-2 d-none d-md-block sidebar">
                        @include('layouts.partials.customer_sidebar')
                    </div>
                    
                    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @yield('content')
                    </main>
                @elseif(auth()->user()->role === 'Admin')
                    {{-- Redirect to admin layout if they somehow ended up here --}}
                    <script>window.location = "{{ route('admin.dashboard') }}";</script>
                @endif
            @else
                {{-- Guest content --}}
                <div class="col-12">
                    @hasSection('welcome')
                        <div class="welcome-section">
                            @yield('welcome')
                            <div class="welcome-overlay"></div>
                        </div>
                    @endif
                    
                    <main class="py-4">
                        @yield('content')
                    </main>
                </div>
            @endauth
        </div>
    </div>
    
    @include('layouts.partials.footer')
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>

