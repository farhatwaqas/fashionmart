@extends('layouts.admin')

@section('title', 'Backup')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-body text-center">
                    <i class="bi bi-box-seam fs-2 mb-3 d-block text-muted"></i>
                    <h3 class="h6 mb-2">Export Products</h3>
                    <p class="small text-muted mb-3">Download all products as CSV.</p>
                    <a href="{{ route('admin.backup.products') }}" class="btn btn-outline-dark btn-sm">Download CSV</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-body text-center">
                    <i class="bi bi-tags fs-2 mb-3 d-block text-muted"></i>
                    <h3 class="h6 mb-2">Export Categories</h3>
                    <p class="small text-muted mb-3">Download all categories as CSV.</p>
                    <a href="{{ route('admin.backup.categories') }}" class="btn btn-outline-dark btn-sm">Download CSV</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-body text-center">
                    <i class="bi bi-receipt fs-2 mb-3 d-block text-muted"></i>
                    <h3 class="h6 mb-2">Export Orders</h3>
                    <p class="small text-muted mb-3">Download all orders as CSV.</p>
                    <a href="{{ route('admin.backup.orders') }}" class="btn btn-outline-dark btn-sm">Download CSV</a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header">MySQL Dump</div>
        <div class="admin-card-body">
            <p class="small text-muted mb-3">Create a full database backup. Requires mysqldump on the server.</p>
            <form method="POST" action="{{ route('admin.backup.mysql') }}">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm" data-confirm="Create a new MySQL backup?">
                    <i class="bi bi-database me-1"></i> Create Backup
                </button>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">Backup Files</div>
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($files as $file)
                        <tr>
                            <td class="font-monospace small">{{ basename($file) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.backup.download', basename($file)) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">No backup files yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
