<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartPageController extends Controller
{
    public function index(): View
    {
        return view('cart.index', [
            'items' => collect(session('cart.items', []))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'purchase_type' => ['required', 'in:buy,rent'],
            'rental_start_date' => ['required_if:purchase_type,rent', 'nullable', 'date', 'after_or_equal:today'],
            'rental_end_date' => ['required_if:purchase_type,rent', 'nullable', 'date', 'after_or_equal:rental_start_date'],
        ]);

        $product = $this->catalogProduct($validated['slug']);

        if (! $product) {
            return $this->cartError($request, 'This marketplace item is no longer available.', 404);
        }

        if ($validated['purchase_type'] === 'buy' && ! ($product['is_for_sale'] ?? false)) {
            return $this->cartError($request, 'This equipment is available for rental review only.');
        }

        if ($validated['purchase_type'] === 'rent' && (! ($product['is_for_rent'] ?? false) || empty($product['rental_price_per_day']))) {
            return $this->cartError($request, 'This item is not currently available for rental.');
        }

        $validated['name'] = $product['name'];
        $validated['price'] = $validated['purchase_type'] === 'rent'
            ? (int) $product['rental_price_per_day']
            : (int) $product['price'];
        $validated['unit'] = $validated['purchase_type'] === 'rent' ? 'day' : $product['unit'];
        $validated['image'] = $product['image'];
        $validated['rental_days'] = $validated['purchase_type'] === 'rent'
            ? $this->rentalDays($validated['rental_start_date'], $validated['rental_end_date'])
            : null;
        $validated['line_total'] = $this->lineTotal($validated);

        $items = collect(session('cart.items', []));
        $existing = $items->first(fn ($item) => $this->sameLine($item, $validated));

        if ($existing) {
            $items = $items->map(function ($item) use ($validated) {
                if ($this->sameLine($item, $validated)) {
                    $item['quantity'] += $validated['quantity'];
                    $item['line_total'] = $this->lineTotal($item);
                }

                return $item;
            });
        } else {
            $items->push($validated);
        }

        session(['cart.items' => $items->values()->all()]);

        if ($request->expectsJson()) {
            $cartTotal = $items->sum(fn ($item) => (int) ($item['line_total'] ?? 0));

            return response()->json([
                'message' => $validated['name'].' added to cart.',
                'cart_count' => $items->sum(fn ($item) => (int) ($item['quantity'] ?? 1)),
                'cart_total' => $cartTotal,
                'item' => $validated,
            ]);
        }

        return back()->with('success', $validated['name'].' added to cart.');
    }

    private function cartError(Request $request, string $message, int $status = 422): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->with('error', $message);
    }

    private function catalogProduct(string $slug): ?array
    {
        $databaseProduct = Product::query()
            ->with(['images', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($databaseProduct) {
            $imagePath = $databaseProduct->images->first()?->path;

            return $this->normalizeCatalogProduct([
                'slug' => $databaseProduct->slug,
                'name' => $databaseProduct->name,
                'category' => $databaseProduct->category?->name ?? 'Construction Materials',
                'price' => (int) $databaseProduct->price,
                'unit' => $databaseProduct->unit ?: 'item',
                'is_for_sale' => (bool) $databaseProduct->is_for_sale,
                'is_for_rent' => (bool) $databaseProduct->is_for_rent,
                'rental_price_per_day' => $databaseProduct->rental_price_per_day ? (int) $databaseProduct->rental_price_per_day : null,
                'image' => $imagePath && ! Str::endsWith($imagePath, '.svg')
                    ? asset('storage/'.$imagePath)
                    : $this->toolImage($databaseProduct->slug),
            ]);
        }

        $catalogProduct = collect(config('vant_products.products', []))
            ->first(fn (array $product) => ($product['slug'] ?? null) === $slug);

        if (! $catalogProduct) {
            return null;
        }

        $catalogProduct['image'] = $this->toolImage($slug);

        return $this->normalizeCatalogProduct($catalogProduct);
    }

    private function normalizeCatalogProduct(array $product): array
    {
        $rentalOnly = $this->isRentalOnly($product['category'] ?? null, $product['name'] ?? null);

        $product['is_for_sale'] = ! $rentalOnly && (bool) ($product['is_for_sale'] ?? true);
        $product['is_for_rent'] = $rentalOnly && (bool) ($product['is_for_rent'] ?? false);

        if (! $product['is_for_rent']) {
            $product['rental_price_per_day'] = null;
        } elseif (empty($product['rental_price_per_day'])) {
            $product['rental_price_per_day'] = max(25000, (int) ceil(((int) ($product['price'] ?? 250000)) * 0.04));
        }

        $product['price'] = (int) ($product['price'] ?? 0);
        $product['unit'] = (string) ($product['unit'] ?? 'item');
        $product['image'] = $product['image'] ?? $this->toolImage((string) $product['slug']);

        return $product;
    }

    private function toolImage(string $slug): string
    {
        foreach (['webp', 'jpg', 'jpeg', 'png', 'svg'] as $extension) {
            $path = "images/catalog-tools/{$slug}.{$extension}";

            if (is_file(public_path($path))) {
                return asset($path).'?v='.filemtime(public_path($path));
            }
        }

        return asset('images/tools/tool-placeholder.svg');
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'purchase_type' => ['nullable', 'in:buy,rent'],
            'rental_start_date' => ['nullable', 'date'],
            'rental_end_date' => ['nullable', 'date'],
        ]);

        $items = collect(session('cart.items', []))
            ->map(function ($item) use ($slug, $validated) {
                if ($item['slug'] === $slug
                    && ($validated['purchase_type'] ?? ($item['purchase_type'] ?? 'buy')) === ($item['purchase_type'] ?? 'buy')
                    && ($validated['rental_start_date'] ?? ($item['rental_start_date'] ?? null)) === ($item['rental_start_date'] ?? null)
                    && ($validated['rental_end_date'] ?? ($item['rental_end_date'] ?? null)) === ($item['rental_end_date'] ?? null)) {
                    $item['quantity'] = $validated['quantity'];
                    $item['line_total'] = $this->lineTotal($item);
                }

                return $item;
            })
            ->values()
            ->all();

        session(['cart.items' => $items]);

        return back()->with('success', 'Cart quantity updated.');
    }

    public function destroy(string $slug): RedirectResponse
    {
        session([
            'cart.items' => collect(session('cart.items', []))
                ->reject(fn ($item) => $item['slug'] === $slug
                    && request('purchase_type', $item['purchase_type'] ?? 'buy') === ($item['purchase_type'] ?? 'buy')
                    && request('rental_start_date', $item['rental_start_date'] ?? null) === ($item['rental_start_date'] ?? null)
                    && request('rental_end_date', $item['rental_end_date'] ?? null) === ($item['rental_end_date'] ?? null))
                ->values()
                ->all(),
        ]);

        return back()->with('success', 'Item removed from cart.');
    }

    private function rentalDays(?string $startDate, ?string $endDate): int
    {
        if (! $startDate || ! $endDate) {
            return 1;
        }

        return (int) max(1, CarbonImmutable::parse($startDate)->diffInDays(CarbonImmutable::parse($endDate)) + 1);
    }

    private function isRentalOnly(?string $category, ?string $name): bool
    {
        $name = str($name ?? '')->lower()->toString();

        if (in_array($category, [
            'Earthmoving & Heavy Machinery',
            'Foundation & Drilling Equipment',
            'Dewatering Equipment',
            'Concrete Equipment',
            'Lifting & Access',
            'Compaction & Generators',
        ], true)) {
            return true;
        }

        foreach ([
            'excavator',
            'bulldozer',
            'loader',
            'grader',
            'dump truck',
            'hauler',
            'trencher',
            'compactor',
            'roller',
            'concrete mixer',
            'mixer truck',
            'concrete pump',
            'batch plant',
            'cutting machine',
            'concrete buggy',
            'concrete hopper',
            'power trowel',
            'concrete vibrator',
            'tower crane',
            'mobile crane',
            'chain hoist',
            'forklift',
            'manlift',
            'boom lift',
            'scissor lift',
            'scaffolding',
            'drill rig',
            'pile',
            'rock breaker',
            'foundation auger',
            'generator',
            'light tower',
            'submersible pump',
            'wellpoint',
            'centrifugal pump',
        ] as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function lineTotal(array $item): int
    {
        return (int) $item['price'] * (int) $item['quantity'] * max(1, (int) ($item['rental_days'] ?? 1));
    }

    private function sameLine(array $item, array $validated): bool
    {
        return $item['slug'] === $validated['slug']
            && ($item['purchase_type'] ?? 'buy') === $validated['purchase_type']
            && ($item['rental_start_date'] ?? null) === ($validated['rental_start_date'] ?? null)
            && ($item['rental_end_date'] ?? null) === ($validated['rental_end_date'] ?? null);
    }
}

