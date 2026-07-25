<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupService
{
    public function exportProducts(): StreamedResponse
    {
        $products = Product::query()->with(['category', 'images'])->orderBy('id')->get();

        return $this->jsonDownload('fashion-corner-products-'.now()->format('Y-m-d').'.json', [
            'exported_at' => now()->toIso8601String(),
            'type' => 'products',
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    public function exportCategories(): StreamedResponse
    {
        $categories = Category::query()->withCount('products')->orderBy('sort_order')->get();

        return $this->jsonDownload('fashion-corner-categories-'.now()->format('Y-m-d').'.json', [
            'exported_at' => now()->toIso8601String(),
            'type' => 'categories',
            'count' => $categories->count(),
            'data' => $categories,
        ]);
    }

    public function exportOrders(): StreamedResponse
    {
        $orders = Order::query()->with('items')->orderByDesc('id')->get();

        return $this->jsonDownload('fashion-corner-orders-'.now()->format('Y-m-d').'.json', [
            'exported_at' => now()->toIso8601String(),
            'type' => 'orders',
            'count' => $orders->count(),
            'data' => $orders,
        ]);
    }

    public function createMysqlDump(): string
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $filename = 'mysql-backup-'.now()->format('Y-m-d-His').'.sql';
        $relative = 'backups/'.$filename;
        $absolute = storage_path('app/'.$relative);

        File::ensureDirectoryExists(dirname($absolute));

        $mysqldump = $this->findMysqldump();
        $passwordPart = $password !== null && $password !== '' ? '-p'.escapeshellarg($password) : '';

        $command = sprintf(
            '%s -h%s -P%s -u%s %s %s > %s',
            escapeshellarg($mysqldump),
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($username),
            $passwordPart,
            escapeshellarg($database),
            escapeshellarg($absolute)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || ! File::exists($absolute) || File::size($absolute) === 0) {
            // Fallback: export core tables as JSON archive when mysqldump is unavailable
            $fallback = [
                'note' => 'mysqldump unavailable — JSON fallback',
                'exported_at' => now()->toIso8601String(),
                'categories' => Category::query()->get(),
                'products' => Product::query()->with('images')->get(),
                'orders' => Order::query()->with('items')->get(),
            ];
            $relative = 'backups/fallback-backup-'.now()->format('Y-m-d-His').'.json';
            Storage::disk('local')->put($relative, json_encode($fallback, JSON_PRETTY_PRINT));

            return $relative;
        }

        return $relative;
    }

    protected function findMysqldump(): string
    {
        $candidates = [
            'D:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'mysqldump',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === 'mysqldump' || File::exists($candidate)) {
                return $candidate;
            }
        }

        return 'mysqldump';
    }

    protected function jsonDownload(string $filename, array $payload): StreamedResponse
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }
}
