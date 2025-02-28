<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $payment->invoice_number }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .invoice-header { margin-bottom: 20px; }
        .invoice-details { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="invoice-header">
                    <h1 class="text-center">Invoice #{{ $payment->invoice_number }}</h1>
                    <p class="text-center">Date: {{ $payment->pay_date }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="invoice-details">
                    <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                    <p><strong>Payment Method:</strong> {{ $payment->pay_method }}</p>
                    <p><strong>Amount Paid:</strong> ₱{{ number_format($payment->amount_paid, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->product->unit_of_measurement }}</td>
                            <td>₱{{ number_format($detail->product->price, 2) }}</td>
                            <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total:</th>
                            <th>₱{{ number_format($order->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
