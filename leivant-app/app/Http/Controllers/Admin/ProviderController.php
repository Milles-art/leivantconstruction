<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Provider;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function index(Request $request): View
    {
        $providers = Provider::query()
            ->with('region')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%"));
            })
            ->when($request->filled('region_id'), fn ($query) => $query->where('region_id', $request->integer('region_id')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('status'), function ($query) use ($request) {
                match ($request->string('status')->toString()) {
                    'approved' => $query->where('is_active', true)->where('is_verified', true),
                    'inactive' => $query->where('is_active', false),
                    default => $query->where(fn ($query) => $query->where('is_active', false)->orWhere('is_verified', false)),
                };
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.providers.index', [
            'providers' => $providers,
            'regions' => Region::query()->orderBy('name')->get(),
            'categories' => $this->categories(),
        ]);
    }

    public function create(): View
    {
        return view('admin.providers.create', [
            'regions' => Region::query()->orderBy('name')->get(),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['is_active'] = $request->boolean('is_active', true);

        $provider = Provider::query()->create($validated);
        ActivityLog::record('provider.created', 'Created provider '.$provider->name, $provider);

        return redirect()->route('admin.providers.index')->with('success', 'Provider created.');
    }

    public function edit(Provider $provider): View
    {
        return view('admin.providers.edit', [
            'provider' => $provider,
            'regions' => Region::query()->orderBy('name')->get(),
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, Provider $provider): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['is_active'] = $request->boolean('is_active', true);

        $provider->update($validated);
        ActivityLog::record('provider.updated', 'Updated provider '.$provider->name, $provider);

        return redirect()->route('admin.providers.index')->with('success', 'Provider updated.');
    }

    public function deactivate(Provider $provider): RedirectResponse
    {
        $provider->update(['is_active' => false]);
        ActivityLog::record('provider.deactivated', 'Deactivated provider '.$provider->name, $provider);

        return redirect()->route('admin.providers.index')->with('success', 'Provider hidden from public discovery.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,deactivate,verify,unverify'],
            'providers' => ['required', 'array'],
            'providers.*' => ['integer', 'exists:providers,id'],
        ]);

        $updates = match ($validated['action']) {
            'approve' => ['is_active' => true, 'is_verified' => true],
            'deactivate' => ['is_active' => false],
            'verify' => ['is_verified' => true],
            default => ['is_verified' => false],
        };

        $count = Provider::query()->whereIn('id', $validated['providers'])->update($updates);
        ActivityLog::record('provider.bulk_updated', 'Updated '.$count.' provider(s)');

        return back()->with('success', $count.' provider(s) updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100', Rule::in($this->categories())],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'location' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:4000'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
        ]);
    }

    private function categories(): array
    {
        return ['Material Suppliers', 'Equipment Rental', 'Architects', 'Engineers', 'Contractors', 'Skilled Labour', 'Site Support', 'Electrical & Plumbing', 'Transport & Logistics'];
    }
}

