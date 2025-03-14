<footer class="bg-black-color text-white py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="KSM SeaPlus+" height="50" class="mb-2">
                    <h5 class="gold-text">KSM Sand&Gravel</h5>
                </div>
                <p class="opacity-75">Your trusted provider of quality sand, gravel, and construction materials.</p>
                <div class="d-flex mt-3">
                    <a href="#" class="me-3 btn btn-sm btn-outline-light rounded-circle">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="me-3 btn btn-sm btn-outline-light rounded-circle">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="gold-text mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('customer.products.index') }}" class="text-white">
                            <i class="fas fa-chevron-right me-2 small gold-text"></i> Products
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('/about') }}" class="text-white">
                            <i class="fas fa-chevron-right me-2 small gold-text"></i> About Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('/contact') }}" class="text-white">
                            <i class="fas fa-chevron-right me-2 small gold-text"></i> Contact Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('terms') }}" class="text-white">
                            <i class="fas fa-chevron-right me-2 small gold-text"></i> Terms & Conditions
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-5 col-md-12">
                <h5 class="gold-text mb-4">Contact Us</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-3">
                            <i class="fas fa-map-marker-alt me-2 gold-text"></i> 123 Main Street, City
                        </p>
                        <p class="mb-3">
                            <i class="fas fa-phone me-2 gold-text"></i> (123) 456-7890
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-3">
                            <i class="fas fa-envelope me-2 gold-text"></i> info@ksmseaplus.com
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-clock me-2 gold-text"></i> Mon-Fri: 9AM - 5PM
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-4 opacity-25">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 opacity-75">&copy; {{ date('Y') }} KSM SeaPlus+. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="{{ route('privacy') }}" class="text-white">Privacy Policy</a>
                    </li>
                    <li class="list-inline-item">
                        <span class="opacity-50 mx-1">|</span>
                    </li>
                    <li class="list-inline-item">
                        <a href="{{ route('terms') }}" class="text-white">Terms of Service</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<style>
    footer a {
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    footer a:hover {
        color: var(--gold-color) !important;
    }
    
    footer .btn-outline-light {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    footer .btn-outline-light:hover {
        background-color: var(--gold-color);
        border-color: var(--gold-color);
        color: var(--black-color) !important;
    }
</style>
