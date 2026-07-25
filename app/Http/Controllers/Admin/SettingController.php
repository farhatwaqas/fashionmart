<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $keys = [
            'store_name', 'store_tagline', 'store_phone', 'store_email',
            'store_address', 'currency', 'meta_title', 'meta_description',
            'low_stock_threshold', 'free_shipping_note',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::getValue($key);
        }

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'store_tagline' => ['nullable', 'string', 'max:200'],
            'store_phone' => ['nullable', 'string', 'max:40'],
            'store_email' => ['nullable', 'email', 'max:150'],
            'store_address' => ['nullable', 'string', 'max:300'],
            'currency' => ['required', 'string', 'max:10'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'free_shipping_note' => ['nullable', 'string', 'max:200'],
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
