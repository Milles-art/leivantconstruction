<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => SiteSetting::values(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:180'],
            'company_location' => ['required', 'string', 'max:220'],
            'company_phone' => ['required', 'string', 'max:60'],
            'company_phone_tel' => ['required', 'string', 'max:60'],
            'company_email' => ['required', 'email', 'max:180'],
            'company_whatsapp' => ['required', 'url', 'max:220'],
            'analytics_google_id' => ['nullable', 'string', 'max:80'],
            'analytics_clarity_id' => ['nullable', 'string', 'max:80'],
            'analytics_facebook_pixel_id' => ['nullable', 'string', 'max:80'],
            'social_facebook' => ['nullable', 'url', 'max:220'],
            'social_instagram' => ['nullable', 'url', 'max:220'],
            'social_linkedin' => ['nullable', 'url', 'max:220'],
            'social_youtube' => ['nullable', 'url', 'max:220'],
            'social_x' => ['nullable', 'url', 'max:220'],
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue($key, $value, str_starts_with($key, 'company_') ? 'company' : (str_starts_with($key, 'analytics_') ? 'analytics' : 'social'));
        }

        ActivityLog::record('settings.updated', 'Updated site settings');

        return back()->with('success', 'Site settings updated.');
    }
}
