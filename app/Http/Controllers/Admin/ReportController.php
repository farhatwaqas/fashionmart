<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $salesByStatus = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
            ->groupBy('status')
            ->get();

        $topProducts = Product::query()
            ->withCount('images')
            ->orderByDesc('hot_selling')
            ->orderByDesc('featured')
            ->limit(10)
            ->get();

        $monthly = Order::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('status', '!=', OrderStatus::Cancelled)
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.index', compact('salesByStatus', 'topProducts', 'monthly'));
    }
}
