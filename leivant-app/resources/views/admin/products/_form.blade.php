@php($editing = isset($product))

<form method="POST" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="grid gap-6 rounded-lg border border-white/10 bg-vant-card p-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Equipment Identity</p>
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Equipment Name</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="Example: Site Generator 5kVA" required>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Service / Availability Area</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="region" value="{{ old('region', $product->region ?? 'Dar es Salaam') }}" required>
            </label>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Equipment Category</span>
                <select class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Internal Equipment Desk</span>
                <select class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="provider_id">
                    @foreach ($providers as $provider)
                        <option value="{{ $provider->id }}" @selected(old('provider_id', $product->provider_id ?? $equipmentDesk->id) == $provider->id)>{{ $provider->name }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <label class="mt-5 block">
            <span class="mb-2 block text-sm font-bold text-zinc-200">Equipment Description</span>
            <textarea class="min-h-44 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="description" required placeholder="Describe what the equipment is used for, where it fits on site, and any handling notes.">{{ old('description', $product->description ?? '') }}</textarea>
        </label>
    </section>

    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Pricing & Stock</p>
        <div class="mt-5 grid gap-5 md:grid-cols-3">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Selling Price (TZS)</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" type="number" name="price" value="{{ old('price', $product->price ?? '') }}" required>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Renting Price / Day (TZS)</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" type="number" name="rental_price_per_day" value="{{ old('rental_price_per_day', $product->rental_price_per_day ?? '') }}">
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Unit</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="unit" value="{{ old('unit', $product->unit ?? 'unit') }}" required>
            </label>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-3">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Stock</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Admin Status</span>
                <select class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="availability_status" required>
                    @foreach (['available', 'limited', 'booked', 'maintenance'] as $status)
                        <option value="{{ $status }}" @selected(old('availability_status', $product->availability_status ?? 'available') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Internal Condition Note</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="equipment_condition" value="{{ old('equipment_condition', $product->equipment_condition ?? 'Good working condition') }}" required>
            </label>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <label class="flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_for_sale" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_for_sale', $product->is_for_sale ?? true))> For Sale</label>
            <label class="flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_for_rent" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_for_rent', $product->is_for_rent ?? true))> For Rent</label>
            <label class="flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_featured" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_featured', $product->is_featured ?? false))> Featured</label>
            <label class="flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_active" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_active', $product->is_active ?? true))> Active</label>
        </div>
    </section>

    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Equipment Images</p>
        <label class="mt-5 block">
            <span class="mb-2 block text-sm font-bold text-zinc-200">Upload Product Images</span>
            <input class="block w-full rounded-md border border-vant-line bg-black/60 text-sm text-zinc-300 file:mr-4 file:min-h-14 file:border-0 file:bg-vant-gold file:px-5 file:py-3 file:font-bold file:text-black" type="file" name="images[]" multiple accept="image/*">
        </label>
        @if ($editing && $product->images->count())
            <div class="mt-5 grid gap-3 sm:grid-cols-3 md:grid-cols-4">
                @foreach ($product->images as $image)
                    <div class="overflow-hidden rounded-md border border-white/10 bg-white/5">
                        <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt_text }}" class="h-28 w-full object-cover">
                        <div class="grid gap-2 p-3">
                            <form method="POST" action="{{ route('admin.products.images.update', [$product, $image]) }}" class="grid gap-2">
                                @csrf
                                @method('PATCH')
                                <input class="vant-input w-full text-sm" name="alt_text" value="{{ $image->alt_text }}" placeholder="Alt text">
                                <input class="vant-input w-full text-sm" type="number" name="sort_order" value="{{ $image->sort_order }}" min="0">
                                <button class="rounded-full border border-vant-gold/40 px-3 py-2 text-xs font-bold text-vant-gold" type="submit">Save Image</button>
                            </form>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.products.images.primary', [$product, $image]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-full bg-vant-gold px-3 py-2 text-xs font-bold text-white" type="submit">{{ $image->is_primary ? 'Primary' : 'Set Primary' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirm('Delete this image?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full border border-red-200 px-3 py-2 text-xs font-bold text-red-600" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button class="vant-button" type="submit">{{ $editing ? 'Update Equipment' : 'Create Equipment' }}</button>
        <a href="{{ route('admin.products.index') }}" class="vant-button-outline">Cancel</a>
    </div>
</form>

