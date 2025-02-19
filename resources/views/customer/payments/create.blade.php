@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Payment for Order #{{ $order->order_id }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <input type="hidden" name="payment_method" value="{{ $order->payment_method }}">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Amount Due</label>
                        <input type="text" class="form-control" value="₱{{ number_format($order->total_amount, 2) }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Amount to Pay</label>
                        <input type="number" step="0.01" class="form-control" name="amount_paid" required>
                    </div>
                </div>

                @if($order->payment_method === 'GCash')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Scan QR Code to Pay</h5>
                                    <div id="gcashQRCode">
                                        <!-- QR code will be automatically generated -->
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3" id="gcashRedirectBtn">
                                        <img src="{{ asset('images/GCash_Logo.png') }}" height="20" alt="GCash">
                                        Pay with GCash
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <button type="submit" class="btn btn-primary">Process Cash Payment</button>
                @endif
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($order->payment_method === 'GCash')
        // Generate QR Code immediately for GCash payments
        generateQRCode();
        
        const gcashRedirectBtn = document.getElementById('gcashRedirectBtn');
        gcashRedirectBtn.addEventListener('click', function() {
            const formData = new FormData(document.getElementById('paymentForm'));
            const amountPaid = parseFloat(formData.get('amount_paid'));
            const totalAmount = parseFloat('{{ $order->total_amount }}');
            
            if (amountPaid !== totalAmount) {
                alert('Please enter the exact amount to proceed with GCash payment.');
                return;
            }

            const orderId = '{{ $order->order_id }}';
            window.location.href = '{{ route("payments.gcash.redirect") }}' + 
                '?order_id=' + orderId + 
                '&amount_paid=' + amountPaid;
        });
    @endif

    function generateQRCode() {
        const orderId = '{{ $order->order_id }}';
        const amount = '{{ $order->total_amount }}';
        const qrData = `seaplus:pay?order=${orderId}&amount=${amount}`;
        
        const qr = qrcode(0, 'M');
        qr.addData(qrData);
        qr.make();

        document.getElementById('gcashQRCode').innerHTML = qr.createImgTag(6);
    }
});
</script>
@endpush
@endsection
