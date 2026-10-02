@extends('layouts.admin')
@section('title', 'Categories | Leivant Admin')
@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="admin-eyebrow">Marketplace Taxonomy</p><h1 class="mt-2">Categories</h1><p class="mt-2 text-sm">Manage product categories used by marketplace items.</p></div>
        <a href="{{ route('admin.categories.create') }}" class="vant-button">New Category</a>
    </div>
    <div class="admin-card mt-7 overflow-hidden">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead><tr><th class="px-4 py-3">Category</th><th class="px-4 py-3">Products</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach ($categories as $category)
                    <tr>
                        <td class="px-4 py-4"><p class="font-bold text-zinc-950">{{ $category->name }}</p><p class="text-xs text-zinc-500">{{ $category->slug }}</p></td>
                        <td class="px-4 py-4 text-zinc-600">{{ $category->products_count }}</td>
                        <td class="px-4 py-4"><span class="admin-pill">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-3">
                                <a class="font-bold text-vant-gold" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                @if ($category->is_active)
                                    <form method="POST" action="{{ route('admin.categories.hide', $category) }}" onsubmit="return confirm('Hide this category from public product filters?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="font-bold text-vant-orange" type="submit">Hide</button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold uppercase tracking-wide text-zinc-500">Hidden</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-zinc-100 p-4">{{ $categories->links() }}</div>
    </div>
@endsection
