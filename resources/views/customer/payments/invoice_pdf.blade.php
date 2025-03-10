<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $payment->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 5px;
        }
        .company-details {
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            border-radius: 4px;
            background-color: #f0ad4e;
            color: white;
        }
        .notice {
            padding: 15px;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>INVOICE</h1>
            <p>KSM SeaPlus+ Construction Materials</p>
        </div>
        
        <div class="row">
            <div style="float: left; width: 50%;">
                <div class="company-details">
                    <h3>Billing Information</h3>
                    <p>
                        <strong>{{ $order->customer->fname }} {{ $order->customer->lname }}</strong><br>
                        {{ $order->customer->address }}<br>
                        {{ $order->customer->city }}, {{ $order->customer->province }}<br>
                        Phone: {{ $order->customer->phone }}
                    </p>
                </div>
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                <div class="invoice-details">
                    <h3>Invoice Details</h3>
                    <p>
                        <strong>Invoice Number:</strong> {{ $payment->invoice_number }}<br>
                        <strong>Date:</strong> {{ $payment->created_at->format('M d, Y') }}<br>
                        <strong>Order ID:</strong> {{ $order->order_id }}<br>
                        <strong>Payment Method:</strong> {{ $payment->pay_method }}
                        @if($payment->pay_method === 'Cash on Delivery')
                            <span class="badge">Cash on Delivery</span>
                        @endif
                    </p>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div>
            <h3>Delivery Information</h3>
            <p>
                <strong>Delivery Address:</strong> 
                {{ $order->delivery->street }}, {{ $order->delivery->city }}, {{ $order->delivery->province }}<br>
                <strong>Scheduled Date:</strong> {{ $order->delivery->delivery_date->format('M d, Y') }}
                @if($order->delivery->special_instructions)
                <br><strong>Special Instructions:</strong> {{ $order->delivery->special_instructions }}
                @endif
            </p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $detail)
                <tr>
                    <td>{{ $detail->product->name }}</td>
                    <td>₱{{ number_format($detail->subtotal / $detail->quantity, 2) }}</td>
                    <td>{{ $detail->quantity }} {{ $detail->product->unit }}(s)</td>
                    <td class="text-right">₱{{ number_format($detail->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Delivery Fee:</strong></td>
                    <td class="text-right">₱{{ number_format($order->delivery->delivery_cost, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right">₱{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @if($payment->pay_method === 'GCash')
                <tr>
                    <td colspan="3" class="text-right"><strong>Paid Amount:</strong></td>
                    <td class="text-right">₱{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
                @if($payment->outstanding_balance > 0)
                <tr>
                    <td colspan="3" class="text-right"><strong>Balance Due:</strong></td>
                    <td class="text-right">₱{{ number_format($payment->outstanding_balance, 2) }}</td>
                </tr>
                @endif
                @elseif($payment->pay_method === 'Cash on Delivery')
                <tr>
                    <td colspan="3" class="text-right"><strong>Amount Due (COD):</strong></td>
                    <td class="text-right">₱{{ number_format($payment->outstanding_balance, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        
        @if($payment->pay_method === 'Cash on Delivery')
        <div class="notice">
            <h3>Cash on Delivery</h3>
            <p>Please prepare the exact amount of ₱{{ number_format($payment->outstanding_balance, 2) }} upon delivery. Our delivery personnel will collect the payment.</p>
        </div>
        @endif
        
        @if($payment->pay_method === 'GCash' && $payment->pay_status === 'Paid')
        <div class="notice">
            <h3>Payment Completed</h3>
            <p>Your payment has been received. Thank you for your order!</p>
            @if($payment->reference_number)
            <p><strong>Reference Number:</strong> {{ $payment->reference_number }}</p>
            @endif
        </div>
        @endif
        
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>For any questions, please contact us at info@ksmseaplus.com or call (123) 456-7890</p>
        </div>
    </div>
</body>
</html>
