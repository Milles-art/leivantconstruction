@php($editing = isset($category))
<form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="admin-card grid gap-5 p-6">
    @csrf
    @if ($editing) @method('PUT') @endif
    <label>
        <span class="mb-2 block text-sm font-bold">Name</span>
        <input class="vant-input min-h-14 w-full" name="name" value="{{ old('name', $category->name ?? '') }}" required>
    </label>
    <label>
        <span class="mb-2 block text-sm font-bold">Description</span>
        <textarea class="vant-input min-h-32 w-full" name="description">{{ old('description', $category->description ?? '') }}</textarea>
    </label>
    <label class="flex min-h-14 items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm font-bold">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
        Active category
    </label>
    <div class="flex gap-3">
        <button class="vant-button" type="submit">{{ $editing ? 'Update Category' : 'Create Category' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="vant-button-outline">Cancel</a>
    </div>
</form>
