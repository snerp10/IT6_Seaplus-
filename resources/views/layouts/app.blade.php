<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'KSM SeaPlus+') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }
        
        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }
        
        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')
    
    @auth
        {{-- Show authenticated user content --}}
        @if(auth()->user()->role === 'Customer')
            @include('layouts.partials.customer_sidebar')
        @elseif(auth()->user()->role === 'Admin')
            {{-- Redirect to admin layout if they somehow ended up here --}}
            <script>window.location = "{{ route('admin.dashboard') }}";</script>
        @endif
        
        <main class="py-4">
            @yield('content')
        </main>
    @else
        {{-- Guest content --}}
        <div class="welcome-section">
            @yield('welcome')
        </div>
        
        <main class="py-4">
            @yield('content')
        </main>
    @endauth
    
    @include('layouts.partials.footer')
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>

