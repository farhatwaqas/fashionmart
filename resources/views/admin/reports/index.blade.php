@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="admin-card">
                <div class="admin-card-header">Sales by Status</div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesByStatus as $row)
                                <tr>
                                    <td>
                                        {{-- $row->status is already cast to OrderStatus via Order model --}}
                                        <span class="badge {{ $row->status->badgeClass() }}">{{ $row->status->label() }}</span>
                                    </td>
                                    <td>{{ number_format($row->total) }}</td>
                                    <td>PKR {{ number_format($row->revenue ?? 0, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="admin-card">
                <div class="admin-card-header">Monthly Revenue (Last 6 Months)</div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthly as $row)
                                <tr>
                                    <td>{{ $row->month }}</td>
                                    <td>{{ number_format($row->orders) }}</td>
                                    <td>PKR {{ number_format($row->revenue ?? 0, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">Top Products</div>
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Featured</th>
                        <th>Hot Selling</th>
                        <th>Images</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr>
                            <td>
                                <a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
                            </td>
                            <td>{{ $product->formattedPrice() }}</td>
                            <td>@if($product->featured)<i class="bi bi-check text-success"></i>@else—@endif</td>
                            <td>@if($product->hot_selling)<i class="bi bi-check text-success"></i>@else—@endif</td>
                            <td>{{ $product->images_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
