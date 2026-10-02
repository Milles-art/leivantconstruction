@extends('layouts.admin')

@section('title', 'Services Admin | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Services</p>
            <h1 class="mt-2">Service Categories</h1>
            <p class="mt-2 max-w-2xl text-sm">Maintain Leivant company service pages, summaries, and request categories.</p>
        </div>
        <a href="{{ route('admin.services.create') }}" class="vant-button">New Service</a>
    </div>

    <section class="admin-card admin-card-plain mt-7">
        <div class="admin-card-header">
            <div>
                <p class="admin-eyebrow">Service Library</p>
                <h2>Public service pages.</h2>
            </div>
            <span class="admin-pill bg-white">{{ $services->total() }} services</span>
        </div>
        <div class="grid gap-3 p-4">
            @forelse ($services as $service)
                <article class="rounded-lg border border-zinc-200 bg-white p-5 transition hover:border-vant-gold/60 hover:bg-amber-50">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-heading text-2xl text-zinc-950">{{ $service->name }}</h2>
                                <span class="admin-badge {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">
                                    {{ $service->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-zinc-600">{{ $service->summary }}</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.services.edit', $service) }}" class="font-bold text-vant-gold hover:text-vant-orange">Edit</a>
                            @if ($service->is_active)
                                <form method="POST" action="{{ route('admin.services.hide', $service) }}" onsubmit="return confirm('Hide this service from the public website?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="font-bold text-vant-orange" type="submit">Hide</button>
                                </form>
                            @else
                                <span class="text-xs font-bold uppercase tracking-wide text-zinc-500">Hidden</span>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <p class="p-5 text-sm text-zinc-500">No services found.</p>
            @endforelse
        </div>
        <div class="border-t border-zinc-100 p-4">{{ $services->links() }}</div>
    </section>
@endsection
