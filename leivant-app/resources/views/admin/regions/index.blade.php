@extends('layouts.admin')
@section('title', 'Regions | Leivant Admin')
@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="admin-eyebrow">Discovery Taxonomy</p><h1 class="mt-2">Regions</h1><p class="mt-2 text-sm">Manage regions used for provider discovery and project location grouping.</p></div>
        <a href="{{ route('admin.regions.create') }}" class="vant-button">New Region</a>
    </div>
    <div class="admin-card mt-7 overflow-hidden">
        <table class="w-full min-w-[620px] text-left text-sm">
            <thead><tr><th class="px-4 py-3">Region</th><th class="px-4 py-3">Providers</th><th class="px-4 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach ($regions as $region)
                    <tr>
                        <td class="px-4 py-4"><p class="font-bold text-zinc-950">{{ $region->name }}</p><p class="text-xs text-zinc-500">{{ $region->slug }}</p></td>
                        <td class="px-4 py-4 text-zinc-600">{{ $region->providers_count }}</td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-3">
                                <a class="font-bold text-vant-gold" href="{{ route('admin.regions.edit', $region) }}">Edit</a>
                                <span class="text-xs font-bold uppercase tracking-wide text-zinc-500">Protected</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-zinc-100 p-4">{{ $regions->links() }}</div>
    </div>
@endsection
