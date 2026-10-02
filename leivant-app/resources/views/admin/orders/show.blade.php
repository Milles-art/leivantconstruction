@extends('layouts.admin')

@section('title', $order->order_number.' | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="admin-eyebrow">Order</p>
            <h1 class="mt-2">{{ $order->order_number }}</h1>
            <p class="mt-2 text-sm">{{ $order->customer_name }} | {{ $order->customer_phone }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="vant-button-outline">Back</a>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[1fr_0.72fr]">
        <div class="space-y-6">
            <section class="admin-card p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="admin-eyebrow">Items</p>
                        <h2>Checkout request.</h2>
                    </div>
                    <span class="admin-pill">{{ $order->items->count() }} item(s)</span>
                </div>
                <div class="mt-5 grid gap-3">
                    @foreach ($order->items as $item)
                        <div class="rounded-md border border-zinc-200 bg-white p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-bold text-zinc-950">{{ $item->product_name }}</p>
                                    <p class="mt-1 text-xs font-bold uppercase text-vant-gold">{{ $item->purchase_type === 'rent' ? 'Rental / Lending' : 'Purchase' }}</p>
                                    <p class="mt-1 text-sm text-zinc-600">{{ $item->quantity }} x TZS {{ number_format($item->unit_price) }}</p>
                                    @if ($item->purchase_type === 'rent')
                                        <p class="mt-1 text-xs font-semibold text-zinc-500">
                                            {{ $item->rental_start_date?->toFormattedDateString() }} to {{ $item->rental_end_date?->toFormattedDateString() }} &middot; {{ $item->rental_days }} day(s)
                                        </p>
                                    @endif
                                </div>
                                <p class="font-black text-vant-gold">TZS {{ number_format($item->line_total) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-card p-6">
                <p class="admin-eyebrow">Customer And Site</p>
                <h2 class="mt-2">Delivery details.</h2>
                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @foreach ([
                        'Customer' => $order->customer_name,
                        'Phone' => $order->customer_phone,
                        'Email' => $order->customer_email ?? 'Not provided',
                        'Region' => $order->delivery_region,
                        'Site' => $order->delivery_address,
                        'Preferred Payment' => str($order->payment?->channel ?? 'manual review')->headline(),
                    ] as $label => $value)
                        <div class="rounded-md border border-zinc-200 bg-white p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">{{ $label }}</p>
                            <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="admin-card h-max p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="admin-eyebrow">Order Status</p>
                    <h2 class="mt-2">Read-only record.</h2>
                </div>
                <span class="admin-badge {{ in_array($order->status, ['completed', 'paid'], true) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ str($order->status)->headline() }}
                </span>
            </div>
            <div class="mt-5 grid gap-3 text-sm">
                <div class="rounded-md border border-zinc-200 bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">Payment Review</p>
                    <p class="mt-1 font-semibold text-zinc-950">{{ str($order->payment?->status ?? 'pending')->headline() }}</p>
                </div>
                <div class="rounded-md border border-zinc-200 bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">Total</p>
                    <p class="mt-1 font-semibold text-vant-gold">TZS {{ number_format($order->total) }}</p>
                </div>
                <div class="rounded-md border border-zinc-200 bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">Site</p>
                    <p class="mt-1 font-semibold text-zinc-950">{{ $order->delivery_address }}</p>
                </div>
                <div class="rounded-md border border-zinc-200 bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">Notes</p>
                    <p class="mt-1 whitespace-pre-line font-semibold text-zinc-950">{{ $order->notes ?: 'No internal notes saved.' }}</p>
                </div>
            </div>
            <p class="mt-5 rounded-md bg-amber-50 p-4 text-sm leading-6 text-zinc-700">
                Payment and order operations are intentionally disabled here. Use this page for visibility and follow-up only.
            </p>
        </aside>
    </div>
@endsection
