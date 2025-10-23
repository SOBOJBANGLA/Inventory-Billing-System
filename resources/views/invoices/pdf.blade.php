<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'Kanit', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .customer-info, .invoice-info {
            flex: 1;
        }
        .customer-info {
            margin-right: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <h2>#{{ $invoice->id }}</h2>
    </div>

    <div class="company-info">
        <h3>Inventory & Billing System</h3>
        <p>Your Business Address<br>
        City, State, ZIP<br>
        Phone: (555) 123-4567<br>
        Email: info@company.com</p>
    </div>

    <div class="invoice-details">
        <div class="customer-info">
            <h4>Bill To:</h4>
            <p><strong>{{ $invoice->customer->name }}</strong></p>
            @if($invoice->customer->phone)
                <p>Phone: {{ $invoice->customer->phone }}</p>
            @endif
            @if($invoice->customer->email)
                <p>Email: {{ $invoice->customer->email }}</p>
            @endif
            @if($invoice->customer->address)
                <p>{{ $invoice->customer->address }}</p>
            @endif
        </div>

        <div class="invoice-info">
            <h4>Invoice Details:</h4>
            <p><strong>Invoice #:</strong> {{ $invoice->id }}</p>
            <p><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
            <p><strong>Created By:</strong> {{ $invoice->creator->name }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoiceItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-amount">
            Total: ${{ number_format($invoice->total, 2) }}
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This invoice was generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
