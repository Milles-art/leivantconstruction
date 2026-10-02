@php($editing = isset($provider))

<form method="POST" action="{{ $editing ? route('admin.providers.update', $provider) : route('admin.providers.store') }}" class="grid gap-6 rounded-lg border border-white/10 bg-vant-card p-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif
    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Business Profile</p>
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Name</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="name" value="{{ old('name', $provider->name ?? '') }}" required>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Category</span>
                <select class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="category" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(old('category', $provider->category ?? 'Material Suppliers') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-3">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Region</span>
                <select class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" name="region_id" required>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id', $provider->region_id ?? null) == $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Phone</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="phone" value="{{ old('phone', $provider->phone ?? '') }}">
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Rating</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white focus:border-vant-gold focus:ring-vant-gold" type="number" min="0" max="5" step="0.1" name="rating" value="{{ old('rating', $provider->rating ?? 4.5) }}" required>
            </label>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Email</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" type="email" name="email" value="{{ old('email', $provider->email ?? '') }}">
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Location</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="location" value="{{ old('location', $provider->location ?? '') }}">
            </label>
        </div>

        <label class="mt-5 block">
            <span class="mb-2 block text-sm font-bold text-zinc-200">Description</span>
            <textarea class="min-h-40 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="description">{{ old('description', $provider->description ?? '') }}</textarea>
        </label>
    </section>

    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Discovery Status</p>
        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <label class="flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_verified" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_verified', $provider->is_verified ?? true))> Verified</label>
            <label class="flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_active" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_active', $provider->is_active ?? true))> Active In Discovery</label>
        </div>
    </section>
    <div class="flex gap-3">
        <button class="vant-button" type="submit">{{ $editing ? 'Update Provider' : 'Create Provider' }}</button>
        <a href="{{ route('admin.providers.index') }}" class="vant-button-outline">Cancel</a>
    </div>
</form>
