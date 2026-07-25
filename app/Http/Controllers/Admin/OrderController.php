<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orders
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('order_number', 'like', $term)
                        ->orWhere('customer_name', 'like', $term)
                        ->orWhere('customer_phone', 'like', $term);
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load('items.product');

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(OrderStatus::cases(), 'value'))],
        ]);

        $this->orders->updateStatus($order, OrderStatus::from($validated['status']));

        return back()->with('success', 'Order status updated.');
    }

    public function invoice(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load('items');

        return view('admin.orders.invoice', compact('order'));
    }
}
