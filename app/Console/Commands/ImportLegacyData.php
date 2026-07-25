<?php

namespace App\Console\Commands;

use App\Services\LegacyImportService;
use Illuminate\Console\Command;

class ImportLegacyData extends Command
{
    protected $signature = 'fashion:import-legacy
        {--path= : Absolute path to legacy project root (defaults to base_path/_legacy)}';

    protected $description = 'Import categories, products, and images from the legacy JSON store into MySQL';

    public function handle(LegacyImportService $importer): int
    {
        $path = $this->option('path') ?: base_path('_legacy');

        $this->info("Importing legacy data from: {$path}");

        try {
            $stats = $importer->import($path);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Categories', $stats['categories']],
                ['Products', $stats['products']],
                ['Images', $stats['images']],
            ]
        );

        $this->info('Legacy import completed successfully.');

        return self::SUCCESS;
    }
}
