<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProviderProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if (! $user->provider) {
            return redirect()->route('discovery.index')
                ->with('error', 'Provider profile was not found for this account.');
        }

        return view('profile.provider', [
            'provider' => $user->provider()->with('region')->first(),
            'regions' => Region::query()->orderBy('name')->get(),
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $provider = $user->provider;

        abort_unless($provider instanceof Provider, 404);

        $validated = $request->validate([
            'provider_name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100', Rule::in($this->categories())],
            'region_id' => ['required', 'exists:regions,id'],
            'location' => ['nullable', 'string', 'max:180'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:40'],
            'description' => ['required', 'string', 'max:1500'],
        ]);

        DB::transaction(function () use ($user, $provider, $validated): void {
            $provider->update([
                'region_id' => $validated['region_id'],
                'name' => $validated['provider_name'],
                'slug' => $this->uniqueProviderSlug($validated['provider_name'], $provider->id),
                'category' => $validated['category'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'],
                'is_verified' => false,
                'is_active' => false,
            ]);

            $user->update([
                'name' => $validated['provider_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Provider profile updated. Leivant will review the profile again before public Discovery visibility.');
    }

    private function uniqueProviderSlug(string $name, int $ignoreId): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 2;

        while (Provider::query()->where('slug', $slug)->whereKeyNot($ignoreId)->exists()) {
            $slug = $base.'-'.$count++;
        }

        return $slug;
    }

    private function categories(): array
    {
        return ['Material Suppliers', 'Equipment Rental', 'Architects', 'Engineers', 'Contractors', 'Skilled Labour', 'Site Support', 'Electrical & Plumbing', 'Transport & Logistics'];
    }
}
