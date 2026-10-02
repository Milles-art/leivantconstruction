<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Region;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'regions' => Region::query()->orderBy('name')->get(),
            'categories' => ['Material Suppliers', 'Equipment Rental', 'Architects', 'Engineers', 'Contractors', 'Skilled Labour', 'Site Support', 'Electrical & Plumbing', 'Transport & Logistics'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provider_name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'region_id' => ['required', 'exists:regions,id'],
            'location' => ['nullable', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:1500'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $provider = Provider::query()->create([
                'region_id' => $validated['region_id'],
                'name' => $validated['provider_name'],
                'slug' => $this->uniqueProviderSlug($validated['provider_name']),
                'category' => $validated['category'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'],
                'rating' => 0,
                'is_verified' => false,
                'is_active' => false,
            ]);

            return User::query()->create([
                'provider_id' => $provider->id,
                'account_type' => 'provider',
                'name' => $validated['provider_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Your provider account has been created. Leivant will review the business profile before it appears in Discovery.');
    }

    private function uniqueProviderSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 2;

        while (Provider::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$count++;
        }

        return $slug;
    }
}
