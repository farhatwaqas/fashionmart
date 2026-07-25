<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Setting;
use App\Models\User;
use App\Services\LegacyImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@fashioncorner.test'],
            [
                'name' => 'Fashion Corner Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $defaults = [
            'store_name' => 'Fashion Corner',
            'store_tagline' => 'Jewellery & accessories for every look',
            'store_phone' => '',
            'store_email' => 'hello@fashioncorner.test',
            'store_address' => '',
            'currency' => 'PKR',
            'meta_title' => 'Fashion Corner — Jewellery & Accessories',
            'meta_description' => 'Shop pins, bracelets, earrings, gift sets and exclusive deals at Fashion Corner.',
            'low_stock_threshold' => '5',
            'free_shipping_note' => 'Cash on Delivery available nationwide',
        ];

        foreach ($defaults as $key => $value) {
            Setting::setValue($key, $value);
        }

        if (! Banner::query()->exists()) {
            Banner::query()->create([
                'title' => 'New Season Essentials',
                'subtitle' => 'Pins, bracelets and gift sets — crafted for everyday style',
                'image' => 'banners/hero-placeholder.svg',
                'link' => '/shop',
                'button_text' => 'Shop Collection',
                'sort_order' => 1,
                'is_active' => true,
            ]);
        }

        // Import legacy catalogue when available
        $legacyPath = base_path('_legacy');
        if (is_dir($legacyPath) && is_file($legacyPath.'/data/products.json')) {
            app(LegacyImportService::class)->import($legacyPath);
        }
    }
}
