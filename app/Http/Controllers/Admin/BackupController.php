<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backup
    ) {}

    public function index(): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $files = collect(Storage::disk('local')->files('backups'))
            ->sortDesc()
            ->values();

        return view('admin.backup.index', compact('files'));
    }

    public function exportProducts(): StreamedResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return $this->backup->exportProducts();
    }

    public function exportCategories(): StreamedResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return $this->backup->exportCategories();
    }

    public function exportOrders(): StreamedResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return $this->backup->exportOrders();
    }

    public function mysqlDump(): RedirectResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $path = $this->backup->createMysqlDump();

        return back()->with('success', "Backup created: {$path}");
    }

    public function download(string $file): StreamedResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $path = 'backups/'.basename($file);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
