<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Inquiry;
use App\Models\ProjectMaterial;
use App\Models\ProjectRequest;
use App\Services\ConstructionBriefGenerator;
use App\Services\ConstructionPlanningEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConstructionPlanningController extends Controller
{
    public function __construct(
        private readonly ConstructionPlanningEngine $engine,
        private readonly ConstructionBriefGenerator $briefGenerator,
    )
    {
    }

    public function templates(): JsonResponse
    {
        return response()->json(['data' => $this->engine->templates()]);
    }

    public function materials(): JsonResponse
    {
        return response()->json(['data' => $this->engine->materials()]);
    }

    public function generatePlan(Request $request): JsonResponse
    {
        $validated = $this->validatePlanningInput($request);

        return response()->json(['data' => $this->engine->generate($validated)]);
    }

    public function calculateProject(Request $request): JsonResponse
    {
        $validated = $this->validatePlanningInput($request);

        return response()->json(['data' => $this->engine->generate($validated)]);
    }

    public function generateBrief(Request $request): JsonResponse
    {
        $validated = $this->validateBriefInput($request);

        return response()->json(['data' => $this->briefGenerator->generate($validated)]);
    }

    public function downloadBriefPdf(Request $request): Response
    {
        $validated = $this->validateBriefInput($request);
        $brief = $this->briefGenerator->generate($validated);
        $pdf = $this->briefPdf($brief);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="leivant-construction-brief.pdf"',
        ]);
    }

    public function storeBriefProject(Request $request): JsonResponse
    {
        $validated = $this->validateBriefInput($request);

        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:180'],
            'email' => ['nullable', 'email', 'max:160'],
        ]);

        $brief = $this->briefGenerator->generate($validated);
        $input = $brief['input'];

        $project = DB::transaction(function () use ($input, $brief): ProjectRequest {
            $inquiry = Inquiry::query()->create([
                'name' => $input['name'],
                'phone' => $input['phone'],
                'email' => $input['email'] ?: null,
                'subject' => 'Construction brief pricing request',
                'project_type' => $brief['summary']['project_type_label'],
                'site_location' => $input['location'] ?: null,
                'budget_range' => $input['budget'] ? 'TZS '.number_format((int) $input['budget']) : null,
                'timeline' => $brief['timeline']['duration_label'],
                'preferred_contact' => 'whatsapp',
                'message' => collect([
                    $brief['report_text'],
                    $input['notes'] ? 'Client notes: '.$input['notes'] : null,
                ])->filter()->implode("\n\n"),
                'status' => 'new',
            ]);

            SendWhatsAppNotification::dispatch($inquiry);

            $project = ProjectRequest::query()->create([
                'inquiry_id' => $inquiry->id,
                'name' => $input['name'],
                'phone' => $input['phone'],
                'email' => $input['email'] ?: null,
                'project_type' => $input['project_type'],
                'house_type' => $input['project_type'] === 'renovation' ? 'existing-house' : $input['house_type'],
                'bedrooms' => $input['bedrooms'],
                'bathrooms' => $input['bathrooms'],
                'floors' => $input['floors'],
                'finish_level' => $input['finish_level'],
                'roof_type' => $input['roof_type'],
                'plot_size' => $input['plot_size'],
                'current_status' => $input['project_type'] === 'renovation' ? $input['current_status'] : null,
                'work_type' => $input['project_type'] === 'renovation' ? $input['work_type'] : null,
                'areas_to_modify' => $input['project_type'] === 'renovation' ? $input['areas_to_modify'] : null,
                'budget' => $input['budget'],
                'location' => $input['location'] ?: null,
                'notes' => $input['notes'] ?: null,
                'floor_area_sqm' => $brief['summary']['floor_area'],
                'duration_weeks' => $brief['timeline']['duration_weeks'],
                'total_cost' => 0,
                'budget_status' => 'pricing pending',
                'plan_payload' => $brief,
                'brief_data' => $brief,
                'status' => 'pending',
            ]);

            foreach ($brief['materials'] as $material) {
                ProjectMaterial::query()->create([
                    'project_request_id' => $project->id,
                    'phase' => $material['phase'],
                    'material_name' => $material['name'],
                    'quantity' => $material['quantity'],
                    'unit' => $material['unit'],
                    'unit_cost' => 0,
                    'estimated_cost' => 0,
                    'source' => 'brief_formula',
                ]);
            }

            return $project;
        });

        return response()->json([
            'message' => 'Your project has been submitted. Our team will review and send pricing.',
            'data' => [
                'project_request_id' => $project->id,
                'status' => $project->status,
                'brief' => $brief,
            ],
        ], 201);
    }

    public function adminProjects(): JsonResponse
    {
        $perPage = min(max((int) request('per_page', 25), 5), 100);
        $projects = ProjectRequest::query()
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $projects->getCollection()
                ->map(fn (ProjectRequest $project): array => $this->projectPayload($project))
                ->values(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    public function adminProject(ProjectRequest $project): JsonResponse
    {
        return response()->json(['data' => $this->projectPayload($project->load('materials', 'inquiry'))]);
    }

    public function updateAdminProject(Request $request, ProjectRequest $project): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'quoted', 'approved', 'draft', 'submitted'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project status updated.',
            'data' => $this->projectPayload($project->refresh()->load('materials', 'inquiry')),
        ]);
    }

    public function storeProject(Request $request): JsonResponse
    {
        $validated = $this->validatePlanningInput($request);
        $status = $request->string('status')->lower()->toString() === 'submitted' ? 'submitted' : 'draft';

        if ($status === 'submitted') {
            $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'phone' => ['required', 'string', 'max:40'],
                'location' => ['required', 'string', 'max:180'],
            ]);
        }

        $plan = $this->engine->generate($validated);

        $project = DB::transaction(function () use ($validated, $status, $plan): ProjectRequest {
            $inquiry = null;

            if ($status === 'submitted') {
                $inquiry = Inquiry::query()->create([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? null,
                    'subject' => 'Construction planning system request',
                    'project_type' => $plan['summary']['house_type'],
                    'site_location' => $validated['location'] ?? null,
                    'budget_range' => $validated['budget'] ? 'TZS '.number_format((int) $validated['budget']) : null,
                    'timeline' => $plan['summary']['estimated_duration_weeks'].' weeks',
                    'preferred_contact' => 'whatsapp',
                    'message' => collect([
                        $plan['report_text'],
                        $validated['notes'] ? 'Client notes: '.$validated['notes'] : null,
                    ])->filter()->implode("\n\n"),
                    'status' => 'new',
                ]);

                SendWhatsAppNotification::dispatch($inquiry);
            }

            $project = ProjectRequest::query()->create([
                'house_template_id' => $plan['template']['id'] ?? null,
                'inquiry_id' => $inquiry?->id,
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'house_type' => $validated['house_type'],
                'bedrooms' => $validated['bedrooms'],
                'floors' => $validated['floors'],
                'finish_level' => $validated['finish_level'],
                'roof_type' => $validated['roof_type'],
                'plot_size' => $validated['plot_size'],
                'budget' => $validated['budget'] ?: null,
                'location' => $validated['location'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'floor_area_sqm' => $plan['summary']['estimated_floor_area'],
                'duration_weeks' => $plan['summary']['estimated_duration_weeks'],
                'total_cost' => $plan['totals']['total_cost'],
                'budget_status' => $plan['totals']['budget_status'],
                'plan_payload' => $plan,
                'status' => $status,
            ]);

            foreach ($plan['materials'] as $material) {
                ProjectMaterial::query()->create([
                    'project_request_id' => $project->id,
                    'material_id' => $material['id'] ?? null,
                    'phase' => $material['phase'],
                    'material_name' => $material['name'],
                    'quantity' => $material['quantity'],
                    'unit' => $material['unit'],
                    'unit_cost' => $material['unit_cost'],
                    'estimated_cost' => $material['estimated_cost'],
                    'source' => $material['source'],
                ]);
            }

            return $project;
        });

        return response()->json([
            'message' => $status === 'submitted'
                ? 'Construction planning request submitted to Leivant.'
                : 'Project draft saved.',
            'data' => [
                'project_request_id' => $project->id,
                'status' => $project->status,
                'inquiry_id' => $project->inquiry_id,
                'plan' => $plan,
            ],
        ], $status === 'submitted' ? 201 : 200);
    }

    private function validatePlanningInput(Request $request): array
    {
        return $request->validate([
            'house_type' => ['nullable', 'string', 'max:80'],
            'bedrooms' => ['nullable', 'integer', 'min:1', 'max:8'],
            'floors' => ['nullable', 'integer', 'min:1', 'max:4'],
            'finish_level' => ['nullable', 'string', 'max:40'],
            'roof_type' => ['nullable', 'string', 'max:40'],
            'plot_size' => ['nullable', 'string', 'max:40'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:180'],
            'notes' => ['nullable', 'string', 'max:2500'],
            'name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
        ]);
    }

    private function validateBriefInput(Request $request): array
    {
        return $request->validate([
            'project_type' => ['required', Rule::in(['new_construction', 'renovation', 'existing', 'existing_house'])],
            'house_type' => ['nullable', Rule::in(['bungalow', 'duplex'])],
            'bedrooms' => ['nullable', 'integer', 'min:1', 'max:8'],
            'bathrooms' => ['nullable', 'integer', 'min:1', 'max:6'],
            'floors' => ['nullable', 'integer', 'min:1', 'max:4'],
            'plot_size' => ['nullable', Rule::in(['compact', 'medium', 'large'])],
            'finish_level' => ['nullable', Rule::in(['basic', 'standard', 'premium'])],
            'roof_type' => ['nullable', Rule::in(['pitched', 'flat', 'hidden-parapet', 'slab'])],
            'current_status' => ['nullable', Rule::in(['incomplete', 'old-house', 'needs-renovation'])],
            'work_type' => ['nullable', Rule::in(['extension', 'renovation', 'finishing'])],
            'areas_to_modify' => ['nullable', 'array'],
            'areas_to_modify.*' => ['string', 'max:40'],
            'location' => ['nullable', 'string', 'max:180'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2500'],
            'name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
        ]);
    }

    private function projectPayload(ProjectRequest $project): array
    {
        $brief = $project->brief_data ?: $project->plan_payload;

        return [
            'id' => $project->id,
            'name' => $project->name,
            'email' => $project->email,
            'phone' => $project->phone,
            'project_type' => $project->project_type,
            'project_type_label' => data_get($brief, 'summary.project_type_label', str($project->project_type)->headline()->toString()),
            'house_type' => $project->house_type,
            'bedrooms' => $project->bedrooms,
            'bathrooms' => $project->bathrooms,
            'floors' => $project->floors,
            'finish_level' => $project->finish_level,
            'roof_type' => $project->roof_type,
            'location' => $project->location,
            'budget' => $project->budget,
            'status' => $project->status,
            'admin_notes' => $project->admin_notes,
            'floor_area_sqm' => $project->floor_area_sqm,
            'duration_weeks' => $project->duration_weeks,
            'brief_data' => $brief,
            'materials' => $project->relationLoaded('materials') ? $project->materials->values()->all() : [],
            'created_at' => $project->created_at?->toISOString(),
        ];
    }

    private function briefPdf(array $brief): string
    {
        $lines = [
            'LEIVANT CONSTRUCTION BRIEF',
            '',
            'Project Summary',
            'Type: '.data_get($brief, 'summary.project_type_label'),
            'Scope: '.data_get($brief, 'summary.scope'),
            'Size estimate: '.data_get($brief, 'summary.size_estimate'),
            'Timeline: '.data_get($brief, 'timeline.duration_label'),
            'Location: '.(data_get($brief, 'summary.location') ?: 'Not provided'),
            '',
            'Scope Of Work',
            'Primary work: '.data_get($brief, 'scope.primary_work'),
            'Area basis: '.data_get($brief, 'scope.area_basis'),
            'Pricing basis: '.data_get($brief, 'scope.pricing_basis'),
            '',
            'Construction Phases',
        ];

        foreach (data_get($brief, 'phases', []) as $phase) {
            $lines[] = '- '.$phase['name'].' ('.$phase['duration_weeks'].' week/s): '.$phase['description'];
        }

        $lines[] = '';
        $lines[] = 'Materials Estimate';

        foreach (data_get($brief, 'materials', []) as $material) {
            $lines[] = '- '.$material['name'].': '.$material['quantity'].' '.$material['unit'].' | '.$material['pricing_source'];
        }

        $lines[] = '';
        $lines[] = 'Timeline';

        foreach (data_get($brief, 'timeline.items', []) as $item) {
            $lines[] = '- '.$item['phase'].': Week '.$item['start_week'].' to '.$item['end_week'];
        }

        $lines[] = '';
        $lines[] = data_get($brief, 'pricing_note');

        return $this->simplePdf($lines);
    }

    private function simplePdf(array $lines): string
    {
        $chunks = array_chunk($lines, 42);
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = [];
        $nextId = 4;

        foreach ($chunks as $chunk) {
            $pageId = $nextId++;
            $contentId = $nextId++;
            $kids[] = $pageId.' 0 R';
            $content = $this->pdfContent($chunk);
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents '.$contentId.' 0 R >>';
            $objects[$contentId] = '<< /Length '.strlen($content).' >>'."\nstream\n".$content."\nendstream";
        }

        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $kids).'] /Count '.count($kids).' >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$body."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";

        return $pdf;
    }

    private function pdfContent(array $lines): string
    {
        $content = "BT\n/F1 11 Tf\n50 790 Td\n14 TL\n";

        foreach ($lines as $index => $line) {
            $fontSize = $index === 0 || in_array($line, ['Project Summary', 'Scope Of Work', 'Construction Phases', 'Materials Estimate', 'Timeline'], true) ? 14 : 10;
            $content .= '/F1 '.$fontSize." Tf\n";
            $content .= '('.$this->pdfEscape($line).") Tj\nT*\n";
        }

        return $content."ET";
    }

    private function pdfEscape(?string $value): string
    {
        $value = preg_replace('/[^\PC\s]/u', '', (string) $value);

        return str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $value);
    }
}
