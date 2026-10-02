<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', ['services' => Service::query()->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $service = Service::query()->create($validated);
        ActivityLog::record('service.created', 'Created service '.$service->name, $service);

        return redirect()->route('admin.services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $service->update($validated);
        ActivityLog::record('service.updated', 'Updated service '.$service->name, $service);

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function hide(Service $service): RedirectResponse
    {
        $service->update(['is_active' => false]);
        ActivityLog::record('service.hidden', 'Hid service '.$service->name, $service);

        return redirect()->route('admin.services.index')->with('success', 'Service hidden from the public website.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

