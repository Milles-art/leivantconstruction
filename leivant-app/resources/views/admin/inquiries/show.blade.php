@extends('layouts.admin')

@section('title', $inquiry->subject.' | Leivant')

@section('admin')
    @php
        $project = $inquiry->projectRequest;
        $plan = $project?->plan_payload ?? [];
        $summary = data_get($plan, 'summary', []);
        $totals = data_get($plan, 'totals', []);
        $phaseCosts = collect(data_get($totals, 'phase_costs', []));
        $materials = $project?->materials ?? collect();
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="admin-eyebrow">Inquiry</p>
            <h1 class="mt-2">{{ $inquiry->subject }}</h1>
            <p class="mt-2 text-sm">{{ $inquiry->name }} | {{ $inquiry->phone }}{{ $inquiry->email ? ' | '.$inquiry->email : '' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($inquiry->email)
                <a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Leivant Construction: '.$inquiry->subject) }}" class="vant-button-outline">Email Client</a>
            @endif
            @if ($inquiry->phone)
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $inquiry->phone) }}" target="_blank" rel="noopener" class="vant-button-outline">WhatsApp</a>
            @endif
            <a href="{{ route('admin.inquiries.index') }}" class="vant-button-outline">Back</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[1fr_0.78fr]">
        <div class="space-y-6">
            <section class="admin-card p-6">
                <h2>Project Brief</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        'Service' => $inquiry->service?->name ?? 'General',
                        'Company / Client' => $inquiry->company ?? 'Not provided',
                        'Region' => $inquiry->region ?? 'Not provided',
                        'Site Location' => $inquiry->site_location ?? 'Not provided',
                        'Project Type' => $inquiry->project_type ?? 'Not provided',
                        'Project Stage' => $inquiry->project_stage ?? 'Not provided',
                        'Budget Range' => $inquiry->budget_range ?? 'Not provided',
                        'Timeline' => $inquiry->timeline ?? 'Not provided',
                        'Preferred Contact' => $inquiry->preferred_contact ? str($inquiry->preferred_contact)->title() : 'Not provided',
                    ] as $label => $value)
                        <div class="rounded-md border border-zinc-200 bg-white p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-zinc-400">{{ $label }}</p>
                            <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($project)
                <section class="admin-card p-6">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="admin-eyebrow">Planning Engine</p>
                            <h2 class="mt-2">Leivant Planning Dashboard</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-6">Rule-based plan submitted from the Services page. Confirm BOQ, drawings, current Marketplace prices, Discovery providers, labour, delivery, and supervision before final quotation.</p>
                        </div>
                        <a href="{{ route('admin.projects.show', $project) }}" class="vant-button-outline">Open Project Desk</a>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ([
                            'Concept' => $summary['house_type'] ?? $project->house_type,
                            'Area' => number_format((int) $project->floor_area_sqm).' sqm',
                            'Duration' => number_format((int) $project->duration_weeks).' weeks',
                            'Estimated Total' => 'TZS '.number_format((int) $project->total_cost),
                            'Budget Status' => $project->budget_status,
                            'Bedrooms / Floors' => $project->bedrooms.' bedroom(s), '.$project->floors.' floor(s)',
                            'Finish / Roof' => str($project->finish_level)->headline().' / '.str($project->roof_type)->headline(),
                            'Plot' => str($project->plot_size)->headline(),
                        ] as $label => $value)
                            <div class="rounded-md border border-zinc-200 bg-white p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-zinc-500">{{ $label }}</p>
                                <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $value ?: 'Not provided' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 grid gap-5 xl:grid-cols-[0.95fr_1.05fr]">
                        <div class="rounded-lg border border-white/10 bg-black/20 p-5">
                            <h3 class="text-xl font-extrabold text-white">Phase Cost Route</h3>
                            <div class="mt-4 space-y-3">
                                @forelse ($phaseCosts as $phase)
                                    <div>
                                        <div class="flex items-center justify-between gap-3 text-sm font-bold text-zinc-200">
                                            <span>{{ $phase['phase'] ?? 'Phase' }}</span>
                                            <span class="text-vant-gold">TZS {{ number_format((int) ($phase['cost'] ?? 0)) }}</span>
                                        </div>
                                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                                            <span class="block h-full rounded-full bg-vant-gold" style="width: {{ max(8, min(100, (int) round(((int) ($phase['cost'] ?? 0) / max(1, (int) ($project->total_cost ?: 1))) * 100))) }}%"></span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-zinc-400">No phase cost route was stored.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-black/20 p-5">
                            <h3 class="text-xl font-extrabold text-white">Admin Next Actions</h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach (['Review drawings and room schedule', 'Check Marketplace stock and prices', 'Match Discovery providers', 'Confirm labour and delivery', 'Prepare BOQ and final quotation'] as $action)
                                    <span class="rounded-full bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-wide text-zinc-200">{{ $action }}</span>
                                @endforeach
                            </div>
                            @if ($project->notes)
                                <p class="mt-5 whitespace-pre-line rounded-md bg-white/5 p-4 text-sm leading-6 text-zinc-300">{{ $project->notes }}</p>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-white/10 bg-vant-card p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">BOQ Starting Point</p>
                            <h2 class="mt-2 text-3xl font-extrabold text-white">Material Lines For Review</h2>
                        </div>
                        <p class="text-sm font-semibold text-zinc-400">{{ $materials->count() }} line(s)</p>
                    </div>
                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full min-w-[720px] text-left text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 uppercase">Phase</th>
                                    <th class="px-4 py-3 uppercase">Material</th>
                                    <th class="px-4 py-3 uppercase">Quantity</th>
                                    <th class="px-4 py-3 uppercase">Source</th>
                                    <th class="px-4 py-3 text-right uppercase">Estimate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse ($materials as $material)
                                    <tr>
                                        <td class="px-4 py-3 text-zinc-300">{{ $material->phase }}</td>
                                        <td class="px-4 py-3 font-semibold text-white">{{ $material->material_name }}</td>
                                        <td class="px-4 py-3 text-zinc-300">{{ number_format((float) $material->quantity, 2) }} {{ $material->unit }}</td>
                                        <td class="px-4 py-3 text-zinc-300">{{ str($material->source)->headline() }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-vant-gold">TZS {{ number_format((int) $material->estimated_cost) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-zinc-400">No material lines were stored for this inquiry.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 rounded-md bg-vant-gold/10 p-4 text-sm leading-6 text-zinc-300">These lines are a planning starting point. Admin must confirm live Marketplace and Discovery pricing before sharing any final quotation with the client.</p>
                </section>
            @endif

            <section class="admin-card p-6">
                <h2>Customer Message</h2>
                <p class="mt-4 whitespace-pre-line leading-7 text-zinc-700">{{ $inquiry->message }}</p>
            </section>
        </div>

        <aside class="admin-card h-max p-6">
            <h2>Admin Response</h2>
            <p class="mt-2 text-sm leading-6">Assign ownership, set priority, schedule follow-up, and keep internal notes.</p>
            <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}" class="mt-5 grid gap-4" onsubmit="if (this.send_email && this.send_email.checked) { return confirm('Send this response email to the client now?'); } return true;">
                @csrf
                @method('PATCH')
                <label>
                    <span class="mb-2 block text-sm font-bold">Status</span>
                    <select class="vant-input w-full" name="status">
                        @foreach (['new', 'in_progress', 'responded', 'closed'] as $status)
                            <option value="{{ $status }}" @selected($inquiry->status === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-bold">Priority</span>
                    <select class="vant-input w-full" name="priority">
                        @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $inquiry->priority ?? 'normal') === $priority)>{{ str($priority)->headline() }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-bold">Assigned Admin</span>
                    <select class="vant-input w-full" name="assigned_to">
                        <option value="">Unassigned</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}" @selected(old('assigned_to', $inquiry->assigned_to) == $admin->id)>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-bold">Follow Up At</span>
                    <input class="vant-input w-full" type="datetime-local" name="follow_up_at" value="{{ old('follow_up_at', $inquiry->follow_up_at?->format('Y-m-d\TH:i')) }}">
                </label>
                <label>
                    <span class="mb-2 block text-sm font-bold">Internal Notes</span>
                    <textarea class="vant-input min-h-32 w-full" name="internal_notes">{{ old('internal_notes', $inquiry->internal_notes) }}</textarea>
                </label>
                <label>
                    <span class="mb-2 block text-sm font-bold">Response Note</span>
                    <textarea class="vant-input min-h-40 w-full" name="response">{{ old('response', $inquiry->response) }}</textarea>
                </label>
                <label class="flex items-start gap-3 rounded-lg border border-zinc-200 bg-white p-4 text-sm">
                    <input class="mt-1" type="checkbox" name="send_email" value="1" @disabled(! $inquiry->email)>
                    <span>
                        <span class="block font-bold text-zinc-950">Send response email to client</span>
                        @if ($inquiry->email)
                            <span class="mt-1 block text-xs leading-5 text-zinc-500">Recipient: {{ $inquiry->email }}. Uses the saved response note and sends from the Leivant mailbox.</span>
                        @else
                            <span class="mt-1 block text-xs leading-5 text-red-600">No client email is saved for this inquiry, so email sending is unavailable.</span>
                        @endif
                    </span>
                </label>
                <button class="vant-button" type="submit">Save Response</button>
            </form>
        </aside>
    </div>
@endsection

