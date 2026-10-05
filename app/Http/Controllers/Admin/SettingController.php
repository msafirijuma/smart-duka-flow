<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit(Request $request)
    {
        $tab = $request->get('tab', 'general');

        $settings = [
            'platform_name'         => PlatformSetting::get('platform_name', 'DukaFlow'),
            'support_email'         => PlatformSetting::get('support_email', ''),
            'support_phone'         => PlatformSetting::get('support_phone', ''),
            'whatsapp'              => PlatformSetting::get('whatsapp', ''),
            'default_currency'      => PlatformSetting::get('default_currency', 'TZS'),
            'timezone'              => PlatformSetting::get('timezone', 'Africa/Dar_es_Salaam'),
            'large_sale_threshold'  => PlatformSetting::get('large_sale_threshold', '500000'),
            'notify_email'          => PlatformSetting::get('notify_email', '0'),
            'registration_open'     => PlatformSetting::get('registration_open', '1'),
            'platform_logo'         => PlatformSetting::get('platform_logo', ''),
        ];

        return view('admin.settings.edit', compact('settings', 'tab'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');

        if ($tab === 'general') {
            $data = $request->validate([
                'platform_name'    => 'required|string|max:120',
                'support_email'    => 'nullable|email|max:120',
                'support_phone'    => 'nullable|string|max:30',
                'whatsapp'         => 'nullable|string|max:30',
                'default_currency' => 'required|in:TZS,USD,KES,UGX',
                'timezone'         => 'required|string|max:60',
            ]);
            foreach ($data as $k => $v) {
                PlatformSetting::set($k, $v);
            }
        }

        if ($tab === 'notifications') {
            $data = $request->validate([
                'large_sale_threshold' => 'nullable|numeric|min:0',
                'notify_email'         => 'nullable|boolean',
            ]);
            PlatformSetting::set('large_sale_threshold', $data['large_sale_threshold'] ?? '0');
            PlatformSetting::set('notify_email', $request->boolean('notify_email') ? '1' : '0');
        }

        if ($tab === 'security') {
            PlatformSetting::set(
                'registration_open',
                $request->boolean('registration_open') ? '1' : '0'
            );
        }

        if ($tab === 'appearance') {
            $request->validate([
                'platform_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            ]);

            if ($request->hasFile('platform_logo')) {
                $old = PlatformSetting::get('platform_logo');
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                $path = $request->file('platform_logo')->store('settings', 'public');
                PlatformSetting::set('platform_logo', $path);
            }
        }

        ActivityLogger::log('admin.settings.updated', "Admin settings updated ({$tab})");

        return redirect()
            ->route('admin.settings.edit', ['tab' => $tab])
            ->with('success', 'Settings saved.');
    }
}