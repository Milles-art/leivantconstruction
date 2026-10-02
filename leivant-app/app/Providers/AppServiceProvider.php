<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('site_settings')) {
            $settings = \App\Models\SiteSetting::values();

            foreach ([
                'company.name' => 'company_name',
                'company.location' => 'company_location',
                'company.phone' => 'company_phone',
                'company.phone_tel' => 'company_phone_tel',
                'company.email' => 'company_email',
                'company.whatsapp' => 'company_whatsapp',
                'analytics.google_id' => 'analytics_google_id',
                'analytics.clarity_id' => 'analytics_clarity_id',
                'analytics.facebook_pixel_id' => 'analytics_facebook_pixel_id',
                'social.facebook' => 'social_facebook',
                'social.instagram' => 'social_instagram',
                'social.linkedin' => 'social_linkedin',
                'social.youtube' => 'social_youtube',
                'social.x' => 'social_x',
            ] as $configKey => $settingKey) {
                if (array_key_exists($settingKey, $settings) && filled($settings[$settingKey])) {
                    Config::set('app.'.$configKey, $settings[$settingKey]);
                }
            }
        }

        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(4)->by($request->ip());
        });

        RateLimiter::for('construction-planner', function (Request $request) {
            return Limit::perMinute(12)->by($request->ip());
        });

        View::composer('*', function ($view): void {
            $cartCount = collect(session('cart.items', []))->sum('quantity');

            if (auth()->check() && auth()->user()->cart) {
                $cartCount = auth()->user()->cart->items()->sum('quantity');
            }

            $view->with('globalCartCount', $cartCount);
        });
    }
}

