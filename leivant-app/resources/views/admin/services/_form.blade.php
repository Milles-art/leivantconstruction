@php($editing = isset($service))

<form method="POST" action="{{ $editing ? route('admin.services.update', $service) : route('admin.services.store') }}" class="grid gap-6 rounded-lg border border-white/10 bg-vant-card p-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif
    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Service Content</p>
        <label>
            <span class="mb-2 mt-5 block text-sm font-bold text-zinc-200">Name</span>
            <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="name" value="{{ old('name', $service->name ?? '') }}" required>
        </label>
        <label>
            <span class="mb-2 mt-5 block text-sm font-bold text-zinc-200">Summary</span>
            <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="summary" value="{{ old('summary', $service->summary ?? '') }}" required>
        </label>
        <label>
            <span class="mb-2 mt-5 block text-sm font-bold text-zinc-200">Description</span>
            <textarea class="min-h-44 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="description" required>{{ old('description', $service->description ?? '') }}</textarea>
        </label>
    </section>

    <section class="rounded-lg border border-white/10 bg-black/20 p-5">
        <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Display Settings</p>
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Icon Key</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="icon" value="{{ old('icon', $service->icon ?? '') }}" placeholder="architecture, engineering...">
            </label>
            <label>
                <span class="mb-2 block text-sm font-bold text-zinc-200">Image Path</span>
                <input class="min-h-14 w-full rounded-md border border-vant-line bg-black/60 px-4 py-3 text-base text-white placeholder-zinc-500 focus:border-vant-gold focus:ring-vant-gold" name="image_path" value="{{ old('image_path', $service->image_path ?? '') }}" placeholder="services/image.jpg">
            </label>
        </div>
        <label class="mt-5 flex min-h-14 items-center gap-3 rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm font-bold text-zinc-200"><input type="checkbox" name="is_active" value="1" class="rounded border-vant-line bg-black text-vant-gold" @checked(old('is_active', $service->is_active ?? true))> Active Public Service</label>
    </section>
    <div class="flex gap-3">
        <button class="vant-button" type="submit">{{ $editing ? 'Update Service' : 'Create Service' }}</button>
        <a href="{{ route('admin.services.index') }}" class="vant-button-outline">Cancel</a>
    </div>
</form>
