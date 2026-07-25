@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <div class="d-flex flex-wrap gap-2 mb-4 no-print">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Print Invoice
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-link btn-sm text-muted">Back to Orders</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-header">Order Items</div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="text-muted">{{ $item->product_sku ?: '—' }}</td>
                                    <td>PKR {{ number_format($item->unit_price, 0) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>PKR {{ number_format($item->line_total, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Subtotal</td>
                                <td>PKR {{ number_format($order->subtotal, 0) }}</td>
                            </tr>
                            @if ($order->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end">Discount</td>
                                    <td>- PKR {{ number_format($order->discount, 0) }}</td>
                                </tr>
                            @endif
                            @if ($order->shipping > 0)
                                <tr>
                                    <td colspan="4" class="text-end">Shipping</td>
                                    <td>PKR {{ number_format($order->shipping, 0) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Total</td>
                                <td class="fw-semibold">{{ $order->formattedTotal() }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if ($order->notes)
                <div class="admin-card">
                    <div class="admin-card-header">Customer Notes</div>
                    <div class="admin-card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="admin-card mb-4">
                <div class="admin-card-header">Customer Details</div>
                <div class="admin-card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-4 text-muted">Name</dt>
                        <dd class="col-8">{{ $order->customer_name }}</dd>
                        <dt class="col-4 text-muted">Phone</dt>
                        <dd class="col-8">{{ $order->customer_phone }}</dd>
                        @if ($order->customer_email)
                            <dt class="col-4 text-muted">Email</dt>
                            <dd class="col-8">{{ $order->customer_email }}</dd>
                        @endif
                        <dt class="col-4 text-muted">City</dt>
                        <dd class="col-8">{{ $order->city }}</dd>
                        <dt class="col-4 text-muted">Address</dt>
                        <dd class="col-8">{{ $order->address }}</dd>
                    </dl>
                </div>
            </div>

            <div class="admin-card mb-4">
                <div class="admin-card-header">Order Info</div>
                <div class="admin-card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Order #</dt>
                        <dd class="col-7">{{ $order->order_number }}</dd>
                        <dt class="col-5 text-muted">Date</dt>
                        <dd class="col-7">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                        <dt class="col-5 text-muted">Payment</dt>
                        <dd class="col-7">{{ strtoupper($order->payment_method ?? 'COD') }}</dd>
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7"><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></dd>
                    </dl>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">Update Status</div>
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
