<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use App\Models\ProjectMaterial;
use App\Models\ProjectRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => ProjectRequest::query()
                ->with('inquiry')
                ->when(request()->filled('search'), function ($query) {
                    $search = request()->string('search');

                    $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%"));
                })
                ->when(request()->filled('status'), fn ($query) => $query->where('status', request()->string('status')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function show(ProjectRequest $project): View
    {
        return view('admin.projects.show', [
            'project' => $project->load('materials', 'documents.user', 'inquiry'),
        ]);
    }

    public function update(Request $request, ProjectRequest $project): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'quoted', 'approved', 'draft', 'submitted'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'total_cost' => ['required', 'integer', 'min:0'],
            'budget_status' => ['required', 'string', 'max:160'],
            'duration_weeks' => ['required', 'integer', 'min:1', 'max:260'],
        ]);

        $project->update($validated);
        ActivityLog::record('project.updated', 'Updated project request #'.$project->id, $project);

        return back()->with('success', 'Project updated.');
    }

    public function storeMaterial(Request $request, ProjectRequest $project): RedirectResponse
    {
        $validated = $this->validatedMaterial($request);
        $validated['estimated_cost'] = (int) round(((float) $validated['quantity']) * ((int) $validated['unit_cost']));

        $material = $project->materials()->create($validated);
        ActivityLog::record('project_material.created', 'Added BOQ material to project #'.$project->id, $material);

        return back()->with('success', 'Material line added.');
    }

    public function updateMaterial(Request $request, ProjectRequest $project, ProjectMaterial $material): RedirectResponse
    {
        abort_unless($material->project_request_id === $project->id, 404);

        $validated = $this->validatedMaterial($request);
        $validated['estimated_cost'] = (int) round(((float) $validated['quantity']) * ((int) $validated['unit_cost']));

        $material->update($validated);
        ActivityLog::record('project_material.updated', 'Updated BOQ material on project #'.$project->id, $material);

        return back()->with('success', 'Material line updated.');
    }

    public function destroyMaterial(ProjectRequest $project, ProjectMaterial $material): RedirectResponse
    {
        abort_unless($material->project_request_id === $project->id, 404);

        $material->delete();
        ActivityLog::record('project_material.deleted', 'Deleted BOQ material from project #'.$project->id, $project);

        return back()->with('success', 'Material line deleted.');
    }

    public function quote(ProjectRequest $project): Response
    {
        $project->load('materials', 'inquiry');
        $lines = [
            'LEIVANT CONSTRUCTION QUOTE',
            'Quote #: LQ-'.$project->id,
            'Date: '.now()->format('d M Y'),
            '',
            'Client',
            'Name: '.($project->name ?: $project->inquiry?->name ?: 'Not provided'),
            'Phone: '.($project->phone ?: $project->inquiry?->phone ?: 'Not provided'),
            'Email: '.($project->email ?: $project->inquiry?->email ?: 'Not provided'),
            'Location: '.($project->location ?: $project->inquiry?->site_location ?: 'Not provided'),
            '',
            'Project Summary',
            'Type: '.str($project->project_type ?: 'construction')->headline(),
            'House: '.str($project->house_type ?: 'not provided')->headline(),
            'Area: '.number_format((int) $project->floor_area_sqm).' sqm',
            'Duration: '.number_format((int) $project->duration_weeks).' week(s)',
            'Status: '.str($project->status)->headline(),
            'Budget Status: '.$project->budget_status,
            '',
            'BOQ Lines',
        ];

        foreach ($project->materials as $material) {
            $lines[] = $material->phase.' - '.$material->material_name.': '.number_format((float) $material->quantity, 2).' '.$material->unit.' @ TZS '.number_format((int) $material->unit_cost).' = TZS '.number_format((int) $material->estimated_cost);
        }

        $lines[] = '';
        $lines[] = 'Estimated Total: TZS '.number_format((int) $project->total_cost);
        $lines[] = '';
        $lines[] = 'Note: This admin-generated quote is for review and confirmation before client approval.';

        return response($this->simplePdf($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="leivant-quote-'.$project->id.'.pdf"',
        ]);
    }

    public function storeDocument(Request $request, ProjectRequest $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'document_type' => ['required', Rule::in(['drawing', 'contract', 'permit', 'quote', 'site_photo', 'general'])],
            'document' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $path = $request->file('document')->store('project-documents', 'public');

        $document = $project->documents()->create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'document_type' => $validated['document_type'],
            'path' => $path,
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::record('project_document.created', 'Uploaded document to project #'.$project->id, $document);

        return back()->with('success', 'Project document uploaded.');
    }

    public function destroyDocument(ProjectRequest $project, ProjectDocument $document): RedirectResponse
    {
        abort_unless($document->project_request_id === $project->id, 404);

        Storage::disk('public')->delete($document->path);
        $document->delete();
        ActivityLog::record('project_document.deleted', 'Deleted document from project #'.$project->id, $project);

        return back()->with('success', 'Project document deleted.');
    }

    private function validatedMaterial(Request $request): array
    {
        return $request->validate([
            'phase' => ['required', 'string', 'max:160'],
            'material_name' => ['required', 'string', 'max:180'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:40'],
            'unit_cost' => ['required', 'integer', 'min:0'],
            'source' => ['required', 'string', 'max:80'],
        ]);
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

        return $pdf."trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";
    }

    private function pdfContent(array $lines): string
    {
        $content = "BT\n/F1 11 Tf\n50 790 Td\n14 TL\n";

        foreach ($lines as $index => $line) {
            $fontSize = $index === 0 || in_array($line, ['Client', 'Project Summary', 'BOQ Lines'], true) ? 14 : 10;
            $content .= '/F1 '.$fontSize." Tf\n";
            $content .= '('.$this->pdfEscape($line).") Tj\nT*\n";
        }

        return $content.'ET';
    }

    private function pdfEscape(?string $value): string
    {
        $value = preg_replace('/[^\PC\s]/u', '', (string) $value);

        return str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $value);
    }
}

