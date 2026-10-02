<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'provider', 'images'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%"));
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->string('status')->toString() === 'active'))
            ->when($request->filled('mode'), function ($query) use ($request) {
                $request->string('mode')->toString() === 'rent'
                    ? $query->where('is_for_rent', true)
                    : $query->where('is_for_sale', true);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['provider_id'] = $validated['provider_id'] ?? $this->equipmentDesk()->id;
        $validated['is_for_sale'] = $request->boolean('is_for_sale', true);
        $validated['is_for_rent'] = $request->boolean('is_for_rent');
        $validated['rental_price_per_day'] = $validated['is_for_rent'] ? ($validated['rental_price_per_day'] ?? null) : null;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);

        $product = Product::query()->create($validated);
        $this->storeImages($request, $product);
        ActivityLog::record('product.created', 'Created product '.$product->name, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', array_merge($this->formData(), ['product' => $product->load('images')]));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validated($request, $product);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['provider_id'] = $validated['provider_id'] ?? $this->equipmentDesk()->id;
        $validated['is_for_sale'] = $request->boolean('is_for_sale', true);
        $validated['is_for_rent'] = $request->boolean('is_for_rent');
        $validated['rental_price_per_day'] = $validated['is_for_rent'] ? ($validated['rental_price_per_day'] ?? null) : null;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);
        $this->storeImages($request, $product);
        ActivityLog::record('product.updated', 'Updated product '.$product->name, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function deactivate(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);
        ActivityLog::record('product.deactivated', 'Deactivated product '.$product->name, $product);

        return redirect()->route('admin.products.index')->with('success', 'Equipment item hidden from public listings.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,featured,unfeatured'],
            'products' => ['required', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        $updates = match ($validated['action']) {
            'activate' => ['is_active' => true],
            'deactivate' => ['is_active' => false],
            'featured' => ['is_featured' => true],
            default => ['is_featured' => false],
        };

        $count = Product::query()->whereIn('id', $validated['products'])->update($updates);
        ActivityLog::record('product.bulk_updated', 'Updated '.$count.' product(s)');

        return back()->with('success', $count.' product(s) updated.');
    }

    private function formData(): array
    {
        $equipmentDesk = $this->equipmentDesk();

        return [
            'categories' => Category::query()->orderBy('name')->get(),
            'providers' => Provider::query()->orderBy('name')->get(),
            'equipmentDesk' => $equipmentDesk,
        ];
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'name' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:40'],
            'stock' => ['required', 'integer', 'min:0'],
            'region' => ['required', 'string', 'max:100'],
            'rental_price_per_day' => ['nullable', 'required_if:is_for_rent,1', 'integer', 'min:1'],
            'availability_status' => ['required', 'in:available,limited,booked,maintenance'],
            'equipment_condition' => ['required', 'string', 'max:160'],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'is_primary' => ! $product->images()->exists() && $index === 0,
                'sort_order' => $product->images()->count() + 1,
            ]);
        }
    }

    private function equipmentDesk(): Provider
    {
        $region = Region::query()->firstOrCreate(
            ['slug' => 'dar-es-salaam'],
            ['name' => 'Dar es Salaam']
        );

        return Provider::query()->firstOrCreate(
            ['slug' => 'vant-equipment-desk'],
            [
                'region_id' => $region->id,
                'name' => 'Leivant Equipment Desk',
                'category' => 'Company Equipment',
                'phone' => config('app.company.phone'),
                'email' => config('app.company.email'),
                'location' => 'Dar es Salaam',
                'description' => 'Internal Leivant-owned tools and equipment inventory desk.',
                'rating' => 5.0,
                'is_verified' => true,
                'is_active' => true,
            ]
        );
    }
}

