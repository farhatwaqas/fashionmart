<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;

class DashboardService
{
    public function stats(): array
    {
        return [
            'total_products' => Product::query()->count(),
            'total_orders' => Order::query()->count(),
            'revenue' => (float) Order::query()
                ->where('status', '!=', OrderStatus::Cancelled)
                ->sum('total'),
            'pending_orders' => Order::query()->where('status', OrderStatus::Pending)->count(),
            'completed_orders' => Order::query()->where('status', OrderStatus::Delivered)->count(),
            'low_stock' => Product::query()
                ->where('status', ProductStatus::Active)
                ->lowStock(5)
                ->count(),
        ];
    }

    public function latestOrders(int $limit = 8)
    {
        return Order::query()
            ->withCount('items')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function lowStockProducts(int $limit = 8)
    {
        return Product::query()
            ->with('category')
            ->where('status', ProductStatus::Active)
            ->lowStock(5)
            ->orderBy('quantity')
            ->limit($limit)
            ->get();
    }
}
