@php($editing = isset($region))
<form method="POST" action="{{ $editing ? route('admin.regions.update', $region) : route('admin.regions.store') }}" class="admin-card grid gap-5 p-6">
    @csrf
    @if ($editing) @method('PUT') @endif
    <label>
        <span class="mb-2 block text-sm font-bold">Region Name</span>
        <input class="vant-input min-h-14 w-full" name="name" value="{{ old('name', $region->name ?? '') }}" required>
    </label>
    <div class="flex gap-3"><button class="vant-button" type="submit">{{ $editing ? 'Update Region' : 'Create Region' }}</button><a href="{{ route('admin.regions.index') }}" class="vant-button-outline">Cancel</a></div>
</form>
