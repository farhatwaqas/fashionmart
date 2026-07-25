<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 14px; color: #111; padding: 40px; max-width: 800px; margin: 0 auto; }
        h1 { font-size: 24px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 32px; }
        .section { margin-bottom: 24px; }
        .section h2 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.06em; color: #888; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { padding: 10px 8px; text-align: left; border-bottom: 1px solid #e8e8e8; }
        th { font-size: 11px; text-transform: uppercase; color: #888; font-weight: 600; }
        .text-end { text-align: right; }
        .totals td { border: none; padding: 6px 8px; }
        .totals .grand { font-weight: 700; font-size: 16px; border-top: 2px solid #111; padding-top: 12px; }
        @media print { body { padding: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 24px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print</button>
    </div>

    <h1>{{ $storeName ?? 'Fashion Corner' }}</h1>
    <p class="meta">Invoice &mdash; {{ $order->order_number }} &mdash; {{ $order->created_at->format('F d, Y') }}</p>

    <div class="section">
        <h2>Bill To</h2>
        <p><strong>{{ $order->customer_name }}</strong></p>
        <p>{{ $order->customer_phone }}</p>
        @if ($order->customer_email)<p>{{ $order->customer_email }}</p>@endif
        <p>{{ $order->address }}, {{ $order->city }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-end">Price</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->product_sku ?: '—' }}</td>
                    <td class="text-end">PKR {{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">PKR {{ number_format($item->line_total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals" style="width: 280px; margin-left: auto;">
        <tr>
            <td>Subtotal</td>
            <td class="text-end">PKR {{ number_format($order->subtotal, 0) }}</td>
        </tr>
        @if ($order->discount > 0)
            <tr>
                <td>Discount</td>
                <td class="text-end">- PKR {{ number_format($order->discount, 0) }}</td>
            </tr>
        @endif
        @if ($order->shipping > 0)
            <tr>
                <td>Shipping</td>
                <td class="text-end">PKR {{ number_format($order->shipping, 0) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td>Total</td>
            <td class="text-end">{{ $order->formattedTotal() }}</td>
        </tr>
    </table>

    <div class="section" style="margin-top: 40px;">
        <p style="color: #888; font-size: 12px;">Payment: {{ strtoupper($order->payment_method ?? 'Cash on Delivery') }} &mdash; Status: {{ $order->status->label() }}</p>
        @if ($order->notes)
            <p style="margin-top: 8px;"><strong>Notes:</strong> {{ $order->notes }}</p>
        @endif
    </div>
</body>
</html>
