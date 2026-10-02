<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(): View
    {
        return view('admin.regions.index', [
            'regions' => Region::query()->withCount('providers')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.regions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);

        $region = Region::query()->create($validated);
        ActivityLog::record('region.created', 'Created region '.$region->name, $region);

        return redirect()->route('admin.regions.index')->with('success', 'Region created.');
    }

    public function edit(Region $region): View
    {
        return view('admin.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        $validated = $this->validated($request, $region);
        $validated['slug'] = Str::slug($validated['name']);

        $region->update($validated);
        ActivityLog::record('region.updated', 'Updated region '.$region->name, $region);

        return redirect()->route('admin.regions.index')->with('success', 'Region updated.');
    }

    private function validated(Request $request, ?Region $region = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('regions', 'name')->ignore($region?->id)],
        ]);
    }
}
