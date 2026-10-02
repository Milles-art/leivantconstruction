@extends('layouts.admin')

@section('title', 'Equipment Admin | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Products</p>
            <h1 class="mt-2">Tools & Equipment</h1>
            <p class="mt-2 max-w-2xl text-sm">Manage Leivant-owned tools and equipment available for sale or daily rental.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="vant-button">New Equipment</a>
    </div>

    <form method="GET" class="admin-filter-bar mt-6 md:grid-cols-[1fr_auto_auto_auto_auto]">
        <input class="vant-input min-h-12" name="search" value="{{ request('search') }}" placeholder="Search equipment, region, or description">
        <select class="vant-input min-h-12" name="category_id">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select class="vant-input min-h-12" name="mode">
            <option value="">Any mode</option>
            <option value="sale" @selected(request('mode') === 'sale')>For sale</option>
            <option value="rent" @selected(request('mode') === 'rent')>For rent</option>
        </select>
        <select class="vant-input min-h-12" name="status">
            <option value="">All visibility</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button class="vant-button" type="submit">Filter</button>
    </form>

    <form method="POST" action="{{ route('admin.products.bulk') }}" class="admin-table-wrap mt-7">
        @csrf
        <div class="flex flex-col gap-2 border-b border-zinc-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2>Inventory List</h2>
                <p class="mt-1 text-sm text-zinc-500">Sale price, rental rate, stock, and admin availability status.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select class="vant-input min-h-10" name="action" required>
                    <option value="">Bulk action</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="featured">Feature</option>
                    <option value="unfeatured">Unfeature</option>
                </select>
                <button class="vant-button-outline py-2" type="submit">Apply</button>
                <span class="admin-pill">{{ $products->total() }} items</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-zinc-200">
                    <tr>
                        <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('[data-product-check]').forEach((box) => box.checked = this.checked)"></th>
                        <th class="px-4 py-3">Equipment</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Mode</th>
                        <th class="px-4 py-3">Sale Price</th>
                        <th class="px-4 py-3">Rent / Day</th>
                        <th class="px-4 py-3">Availability</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach ($products as $product)
                        <tr>
                            <td class="px-4 py-4"><input data-product-check type="checkbox" name="products[]" value="{{ $product->id }}"></td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-zinc-950">{{ $product->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $product->equipment_condition }}</p>
                            </td>
                            <td class="px-4 py-4 text-zinc-700">{{ $product->category?->name }}</td>
                            <td class="px-4 py-4 text-zinc-700">
                                @if ($product->is_for_sale)
                                    <span class="rounded-full bg-vant-gold/15 px-2.5 py-1 text-xs font-bold uppercase text-vant-gold">Buy</span>
                                @endif
                                @if ($product->is_for_rent)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold uppercase text-emerald-700">Rent</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-vant-gold">TZS {{ number_format($product->price) }}</td>
                            <td class="px-4 py-4 text-emerald-700">{{ $product->rental_price_per_day ? 'TZS '.number_format($product->rental_price_per_day) : 'Not rentable' }}</td>
                            <td class="px-4 py-4">
                                <span class="admin-badge {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">{{ str($product->availability_status)->replace('_', ' ')->title() }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-vant-gold hover:text-vant-orange">Edit</a>
                                    @if ($product->is_active)
                                        <form method="POST" action="{{ route('admin.products.deactivate', $product) }}" onsubmit="return confirm('Hide this equipment item from public listings?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="font-bold text-vant-orange" type="submit">Deactivate</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-bold uppercase tracking-wide text-zinc-500">Inactive</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-100 p-4">{{ $products->links() }}</div>
    </form>
@endsection

