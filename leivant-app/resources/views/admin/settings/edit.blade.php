@extends('layouts.admin')

@section('title', 'Site Settings | Leivant Admin')

@section('admin')
    @php
        $value = fn (string $key, mixed $default = '') => old($key, $settings[$key] ?? $default);
    @endphp
    <p class="admin-eyebrow">Site Control</p>
    <h1 class="mt-2">Site Settings</h1>
    <p class="mt-2 max-w-2xl text-sm">Update public company details, analytics IDs, and social links used by the live site.</p>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-card mt-7 grid gap-6 p-6">
        @csrf
        @method('PATCH')
        <section>
            <p class="admin-eyebrow">Company</p>
            <div class="mt-4 grid gap-5 md:grid-cols-2">
                <label><span class="mb-2 block text-sm font-bold">Company Name</span><input class="vant-input min-h-14 w-full" name="company_name" value="{{ $value('company_name', config('app.company.name')) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Location</span><input class="vant-input min-h-14 w-full" name="company_location" value="{{ $value('company_location', config('app.company.location')) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Display Phone</span><input class="vant-input min-h-14 w-full" name="company_phone" value="{{ $value('company_phone', config('app.company.phone')) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Tel Link Phone</span><input class="vant-input min-h-14 w-full" name="company_phone_tel" value="{{ $value('company_phone_tel', config('app.company.phone_tel')) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Email</span><input class="vant-input min-h-14 w-full" type="email" name="company_email" value="{{ $value('company_email', config('app.company.email')) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">WhatsApp URL</span><input class="vant-input min-h-14 w-full" name="company_whatsapp" value="{{ $value('company_whatsapp', config('app.company.whatsapp')) }}" required></label>
            </div>
        </section>
        <section>
            <p class="admin-eyebrow">Analytics</p>
            <div class="mt-4 grid gap-5 md:grid-cols-3">
                <label><span class="mb-2 block text-sm font-bold">Google ID</span><input class="vant-input min-h-14 w-full" name="analytics_google_id" value="{{ $value('analytics_google_id', config('app.analytics.google_id')) }}"></label>
                <label><span class="mb-2 block text-sm font-bold">Clarity ID</span><input class="vant-input min-h-14 w-full" name="analytics_clarity_id" value="{{ $value('analytics_clarity_id', config('app.analytics.clarity_id')) }}"></label>
                <label><span class="mb-2 block text-sm font-bold">Facebook Pixel</span><input class="vant-input min-h-14 w-full" name="analytics_facebook_pixel_id" value="{{ $value('analytics_facebook_pixel_id', config('app.analytics.facebook_pixel_id')) }}"></label>
            </div>
        </section>
        <section>
            <p class="admin-eyebrow">Social Links</p>
            <div class="mt-4 grid gap-5 md:grid-cols-2">
                @foreach (['facebook', 'instagram', 'linkedin', 'youtube', 'x'] as $network)
                    <label><span class="mb-2 block text-sm font-bold">{{ strtoupper($network) }} URL</span><input class="vant-input min-h-14 w-full" name="social_{{ $network }}" value="{{ $value('social_'.$network, config('app.social.'.$network)) }}"></label>
                @endforeach
            </div>
        </section>
        <button class="vant-button w-max" type="submit">Save Settings</button>
    </form>
@endsection
