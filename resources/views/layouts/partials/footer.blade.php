<footer class="bg-dark text-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <h5>KSM SeaPlus+</h5>
                <p class="small">Your trusted provider of quality sand, gravel, and construction materials.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('customer.products.index') }}" class="text-white">Products</a></li>
                    <li><a href="{{ url('/about') }}" class="text-white">About Us</a></li>
                    <li><a href="{{ url('/contact') }}" class="text-white">Contact Us</a></li>
                    <li><a href="{{ url('/terms') }}" class="text-white">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <address class="small">
                    <i class="fas fa-map-marker-alt me-2"></i> 123 Main Street, City<br>
                    <i class="fas fa-phone me-2"></i> (123) 456-7890<br>
                    <i class="fas fa-envelope me-2"></i> info@ksmseaplus.com
                </address>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p class="small mb-0">&copy; {{ date('Y') }} KSM SeaPlus+. All rights reserved.</p>
        </div>
    </div>
</footer>
