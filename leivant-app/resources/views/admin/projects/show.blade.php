@extends('layouts.admin')

@section('title', 'Project #'.$project->id.' | Leivant Admin')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="admin-eyebrow">Project / BOQ</p>
            <h1 class="mt-2">Project #{{ $project->id }}</h1>
            <p class="mt-2 text-sm">{{ $project->name ?: $project->inquiry?->name ?: 'Unknown client' }} | {{ $project->phone ?: $project->inquiry?->phone ?: 'No phone' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.projects.quote', $project) }}" target="_blank" class="vant-button">Quote PDF</a>
            <a href="{{ route('admin.projects.index') }}" class="vant-button-outline">Back</a>
        </div>
    </div>

    <div class="mt-7 grid gap-6 xl:grid-cols-[.8fr_1.2fr]">
        <section class="admin-card p-6">
            <p class="admin-eyebrow">Quotation Control</p>
            <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="mt-5 grid gap-4">
                @csrf
                @method('PATCH')
                <label><span class="mb-2 block text-sm font-bold">Status</span><select class="vant-input min-h-14 w-full" name="status">@foreach (['pending', 'reviewed', 'quoted', 'approved', 'draft', 'submitted'] as $status)<option value="{{ $status }}" @selected($project->status === $status)>{{ str($status)->headline() }}</option>@endforeach</select></label>
                <label><span class="mb-2 block text-sm font-bold">Total Cost</span><input class="vant-input min-h-14 w-full" type="number" name="total_cost" value="{{ old('total_cost', $project->total_cost) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Budget Status</span><input class="vant-input min-h-14 w-full" name="budget_status" value="{{ old('budget_status', $project->budget_status) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Duration Weeks</span><input class="vant-input min-h-14 w-full" type="number" name="duration_weeks" value="{{ old('duration_weeks', $project->duration_weeks) }}" required></label>
                <label><span class="mb-2 block text-sm font-bold">Admin Notes</span><textarea class="vant-input min-h-40 w-full" name="admin_notes">{{ old('admin_notes', $project->admin_notes) }}</textarea></label>
                <button class="vant-button" type="submit">Save Project</button>
            </form>
        </section>

        <section class="admin-card p-6">
            <p class="admin-eyebrow">Project Details</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach ([
                    'Project Type' => str($project->project_type)->headline(),
                    'House Type' => str($project->house_type)->headline(),
                    'Area' => number_format($project->floor_area_sqm).' sqm',
                    'Bedrooms / Floors' => $project->bedrooms.' bedroom(s), '.$project->floors.' floor(s)',
                    'Finish / Roof' => str($project->finish_level)->headline().' / '.str($project->roof_type)->headline(),
                    'Budget' => $project->budget ? 'TZS '.number_format($project->budget) : 'Not provided',
                ] as $label => $value)
                    <div class="rounded-lg border border-zinc-200 bg-white p-4"><p class="text-xs font-bold uppercase text-zinc-500">{{ $label }}</p><p class="mt-1 font-bold text-zinc-950">{{ $value }}</p></div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="admin-card mt-7">
        <div class="admin-card-header">
            <div><p class="admin-eyebrow">BOQ Builder</p><h2>Material Lines</h2></div>
            <span class="admin-pill">{{ $project->materials->count() }} Lines</span>
        </div>
        <div class="grid gap-3 p-4">
            @foreach ($project->materials as $material)
                <form method="POST" action="{{ route('admin.projects.materials.update', [$project, $material]) }}" class="grid gap-3 rounded-xl border border-zinc-200 bg-white p-4 xl:grid-cols-[1fr_1fr_.7fr_.5fr_.7fr_.7fr_auto] xl:items-end">
                    @csrf @method('PATCH')
                    <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Phase</span><input class="vant-input w-full" name="phase" value="{{ $material->phase }}" required></label>
                    <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Material</span><input class="vant-input w-full" name="material_name" value="{{ $material->material_name }}" required></label>
                    <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Qty</span><input class="vant-input w-full" type="number" step="0.01" name="quantity" value="{{ $material->quantity }}" required></label>
                    <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Unit</span><input class="vant-input w-full" name="unit" value="{{ $material->unit }}" required></label>
                    <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Unit Cost</span><input class="vant-input w-full" type="number" name="unit_cost" value="{{ $material->unit_cost }}" required></label>
                    <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Source</span><input class="vant-input w-full" name="source" value="{{ $material->source }}" required></label>
                    <div class="flex gap-2"><button class="vant-button px-4" type="submit">Save</button></div>
                </form>
                <form method="POST" action="{{ route('admin.projects.materials.destroy', [$project, $material]) }}" onsubmit="return confirm('Delete this BOQ line?')">
                    @csrf @method('DELETE')
                    <button class="font-bold text-red-600" type="submit">Delete {{ $material->material_name }}</button>
                </form>
            @endforeach
        </div>
        <form method="POST" action="{{ route('admin.projects.materials.store', $project) }}" class="grid gap-3 border-t border-zinc-100 p-4 xl:grid-cols-[1fr_1fr_.7fr_.5fr_.7fr_.7fr_auto] xl:items-end">
            @csrf
            <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Phase</span><input class="vant-input w-full" name="phase" required></label>
            <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Material</span><input class="vant-input w-full" name="material_name" required></label>
            <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Qty</span><input class="vant-input w-full" type="number" step="0.01" name="quantity" required></label>
            <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Unit</span><input class="vant-input w-full" name="unit" required></label>
            <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Unit Cost</span><input class="vant-input w-full" type="number" name="unit_cost" required></label>
            <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Source</span><input class="vant-input w-full" name="source" value="admin_quote" required></label>
            <button class="vant-button" type="submit">Add Line</button>
        </form>
    </section>

    <section class="admin-card mt-7">
        <div class="admin-card-header">
            <div><p class="admin-eyebrow">Documents</p><h2>Project Files</h2></div>
            <span class="admin-pill">{{ $project->documents->count() }} Files</span>
        </div>
        <div class="grid gap-4 p-4 lg:grid-cols-[.8fr_1.2fr]">
            <form method="POST" action="{{ route('admin.projects.documents.store', $project) }}" enctype="multipart/form-data" class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4">
                @csrf
                <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">File Name</span><input class="vant-input w-full" name="name" required></label>
                <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Type</span><select class="vant-input w-full" name="document_type">@foreach (['drawing', 'contract', 'permit', 'quote', 'site_photo', 'general'] as $type)<option value="{{ $type }}">{{ str($type)->headline() }}</option>@endforeach</select></label>
                <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Document</span><input class="vant-input w-full" type="file" name="document" required></label>
                <label><span class="mb-1 block text-xs font-bold uppercase text-zinc-500">Notes</span><textarea class="vant-input min-h-28 w-full" name="notes"></textarea></label>
                <button class="vant-button" type="submit">Upload File</button>
            </form>

            <div class="grid gap-3">
                @forelse ($project->documents as $document)
                    <div class="flex flex-col gap-3 rounded-lg border border-zinc-200 bg-white p-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="font-bold text-zinc-950">{{ $document->name }}</p>
                            <p class="mt-1 text-xs font-bold uppercase text-zinc-500">{{ str($document->document_type)->headline() }} | {{ $document->user?->name ?? 'Admin' }} | {{ $document->created_at?->format('d M Y') }}</p>
                            @if ($document->notes)
                                <p class="mt-2 text-sm text-zinc-600">{{ $document->notes }}</p>
                            @endif
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($document->path) }}" target="_blank" class="font-bold text-vant-gold">Open</a>
                            <form method="POST" action="{{ route('admin.projects.documents.destroy', [$project, $document]) }}" onsubmit="return confirm('Delete this project document?')">
                                @csrf @method('DELETE')
                                <button class="font-bold text-red-600" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-zinc-200 bg-white p-5 text-sm text-zinc-500">No project files uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
