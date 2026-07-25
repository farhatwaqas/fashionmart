@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label small">Search</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Name, phone, email..." value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-dark btn-sm w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-medium">{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->email ?: '—' }}</td>
                            <td>{{ $customer->city ?: '—' }}</td>
                            <td>{{ $customer->orders_count }}</td>
                            <td class="text-muted small">{{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
@endsection
