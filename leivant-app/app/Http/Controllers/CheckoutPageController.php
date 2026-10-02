<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutPageController extends Controller
{
    public function index(): View
    {
        $items = collect(session('cart.items', []))->values();

        return view('checkout.index', [
            'items' => $items,
            'total' => $items->sum(fn ($item) => $this->lineTotal($item)),
            'regions' => $this->regions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $items = collect(session('cart.items', []))->values();

        if ($items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Add at least one product before checkout.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'region' => ['required', 'string', 'max:80'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'payment_channel' => ['required', 'in:mpesa,tigopesa,airtel,halopesa'],
        ], [], [
            'delivery_address' => 'project / site location',
            'delivery_notes' => 'project / order notes',
        ]);

        $order = [
            'number' => 'Leivant-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            'customer' => $validated,
            'items' => $items->all(),
            'total' => $items->sum(fn ($item) => $this->lineTotal($item)),
            'status' => 'pending',
            'payment_status' => 'pending_review',
            'created_at' => now()->toDayDateTimeString(),
        ];

        if ($this->databaseReady()) {
            // Batch-load products once instead of querying per cart item.
            $productsBySlug = Product::query()
                ->whereIn('slug', $items->pluck('slug')->all())
                ->get()
                ->keyBy('slug');

            $persistedOrder = DB::transaction(function () use ($order, $items, $productsBySlug) {
                $persistedOrder = Order::query()->create([
                    'user_id' => auth()->id(),
                    'order_number' => $order['number'],
                    'customer_name' => $order['customer']['name'],
                    'customer_phone' => $order['customer']['phone'],
                    'customer_email' => $order['customer']['email'] ?? null,
                    'delivery_region' => $order['customer']['region'],
                    'delivery_address' => $order['customer']['delivery_address'],
                    'notes' => $order['customer']['delivery_notes'] ?? null,
                    'subtotal' => $order['total'],
                    'delivery_fee' => 0,
                    'total' => $order['total'],
                    'status' => 'pending',
                ]);

                foreach ($items as $item) {
                    $product = $productsBySlug->get($item['slug']);

                    $persistedOrder->items()->create([
                        'product_id' => $product?->id,
                        'product_name' => $item['name'],
                        'unit_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'purchase_type' => $item['purchase_type'] ?? 'buy',
                        'rental_start_date' => $item['rental_start_date'] ?? null,
                        'rental_end_date' => $item['rental_end_date'] ?? null,
                        'rental_days' => $item['rental_days'] ?? null,
                        'line_total' => $this->lineTotal($item),
                    ]);
                }

                $persistedOrder->payment()->create([
                    'provider' => 'manual_review',
                    'channel' => $order['customer']['payment_channel'],
                    'reference' => 'REVIEW-'.Str::upper(Str::random(12)),
                    'external_reference' => $order['number'],
                    'amount' => $order['total'],
                    'status' => 'pending_review',
                    'payload' => [
                        'message' => 'Customer selected a preferred payment method. No online payment was charged.',
                    ],
                ]);

                return $persistedOrder;
            });

            $order['id'] = $persistedOrder->id;
            $order['status'] = $persistedOrder->status;
            $order['payment_status'] = $persistedOrder->payment?->status ?? $order['payment_status'];
        }

        session()->forget('cart.items');
        session(['last_order' => $order]);

        return redirect()->route('orders.confirmation', $order['number'])
            ->with('success', 'Order request received. Leivant will review availability and contact the customer before payment is arranged.');
    }

    public function confirmation(string $order): View
    {
        if ($this->databaseReady()) {
            $persistedOrder = Order::query()->with(['items.product.images', 'payment'])->where('order_number', $order)->first();

            if ($persistedOrder) {
                // Order pages contain customer PII: only the buyer session,
                // the owning account, or an admin may view them.
                $ownsOrder = session('last_order.number') === $order
                    || (auth()->check() && $persistedOrder->user_id && $persistedOrder->user_id === auth()->id())
                    || (auth()->check() && (bool) auth()->user()->is_admin);
                abort_unless($ownsOrder, 404);

                return view('orders.confirmation', [
                    'order' => [
                        'number' => $persistedOrder->order_number,
                        'customer' => [
                            'name' => $persistedOrder->customer_name,
                            'phone' => $persistedOrder->customer_phone,
                            'email' => $persistedOrder->customer_email,
                            'region' => $persistedOrder->delivery_region,
                            'delivery_address' => $persistedOrder->delivery_address,
                            'payment_channel' => $persistedOrder->payment?->channel ?? 'manual review',
                        ],
                        'items' => $persistedOrder->items->map(fn ($item) => [
                            'name' => $item->product_name,
                            'price' => $item->unit_price,
                            'quantity' => $item->quantity,
                            'purchase_type' => $item->purchase_type,
                            'rental_start_date' => $item->rental_start_date?->toDateString(),
                            'rental_end_date' => $item->rental_end_date?->toDateString(),
                            'rental_days' => $item->rental_days,
                            'line_total' => $item->line_total,
                            'unit' => $item->purchase_type === 'rent' ? 'day' : ($item->product?->unit ?? 'item'),
                            'image' => $this->productDisplayImage($item->product),
                        ])->all(),
                        'total' => $persistedOrder->total,
                        'status' => $persistedOrder->status,
                        'payment_status' => $persistedOrder->payment?->status ?? 'pending_review',
                        'created_at' => $persistedOrder->created_at?->toDayDateTimeString(),
                    ],
                ]);
            }
        }

        abort_unless(session('last_order.number') === $order, 404);

        return view('orders.confirmation', [
            'order' => session('last_order'),
        ]);
    }

    private function databaseReady(): bool
    {
        try {
            return Schema::hasTable('orders') && Schema::hasTable('order_items') && Schema::hasTable('payments');
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    private function lineTotal(array $item): int
    {
        return (int) $item['price'] * (int) $item['quantity'] * max(1, (int) ($item['rental_days'] ?? 1));
    }

    private function productDisplayImage(?Product $product): ?string
    {
        if (! $product) {
            return null;
        }

        $imagePath = $product->images->first()?->path;

        if ($imagePath && ! Str::endsWith($imagePath, '.svg')) {
            return asset('storage/'.$imagePath);
        }

        return match ($product->slug) {
            'portable-concrete-mixer-350l' => asset('images/catalog-tools/portable-concrete-mixer-350l.jpg'),
            'plate-compactor-honda-engine' => asset('images/catalog-tools/plate-compactor-honda-engine.webp'),
            'concrete-vibrator-38mm' => asset('images/catalog-tools/concrete-vibrator-38mm.jpg'),
            'mobile-scaffolding-set' => asset('images/catalog-tools/mobile-scaffolding-set.webp'),
            'site-generator-5kva' => asset('images/catalog-tools/site-generator-5kva.jpg'),
            'professional-angle-grinder' => asset('images/catalog-tools/professional-angle-grinder.jpg'),
            'rotary-hammer-drill' => asset('images/catalog-tools/rotary-hammer-drill.jpg'),
            'heavy-duty-wheelbarrow' => asset('images/catalog-tools/heavy-duty-wheelbarrow.jpg'),
            'extension-ladder-24ft' => asset('images/catalog-tools/extension-ladder-24ft.webp'),
            'site-safety-gear-kit' => asset('images/catalog-tools/site-safety-gear-kit.webp'),
            default => asset('images/tools/tool-placeholder.svg'),
        };
    }

    private function regions(): array
    {
        try {
            return Region::query()->orderBy('name')->pluck('name')->all();
        } catch (\Throwable $e) {
            report($e);

            return [
                'Arusha',
                'Dar es Salaam',
                'Dodoma',
                'Mbeya',
                'Morogoro',
                'Mwanza',
                'Tanga',
            ];
        }
    }
}

