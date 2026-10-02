@extends('layouts.admin')

@section('title', 'Construction Brief Projects | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Project Control</p>
            <h1 class="mt-2">Construction Brief Projects</h1>
            <p class="mt-2 max-w-2xl text-sm">Review submitted plans, update quotation status, and manage BOQ material lines.</p>
        </div>
    </div>
    <div class="admin-card mt-7 overflow-hidden">
        <table class="w-full min-w-[920px] text-left text-sm">
            <thead><tr><th class="px-4 py-3">Project</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Estimate</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Created</th><th class="px-4 py-3">Action</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-4 py-4"><p class="font-bold text-zinc-950">{{ str($project->project_type)->headline() }}</p><p class="text-xs text-zinc-500">{{ $project->location ?: 'No location' }}</p></td>
                        <td class="px-4 py-4 text-zinc-600">{{ $project->name ?: $project->inquiry?->name ?: 'Unknown' }}<br><span class="text-xs">{{ $project->phone ?: $project->inquiry?->phone }}</span></td>
                        <td class="px-4 py-4 text-vant-gold">TZS {{ number_format($project->total_cost) }}</td>
                        <td class="px-4 py-4"><span class="admin-pill">{{ str($project->status)->headline() }}</span></td>
                        <td class="px-4 py-4 text-zinc-600">{{ $project->created_at?->format('d M Y') }}</td>
                        <td class="px-4 py-4"><a href="{{ route('admin.projects.show', $project) }}" class="font-bold text-vant-gold">Open BOQ</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-500">No project requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-zinc-100 p-4">{{ $projects->links() }}</div>
    </div>
@endsection
