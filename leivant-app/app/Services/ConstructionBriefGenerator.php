<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ConstructionBriefGenerator
{
    public function generate(array $input): array
    {
        $data = $this->normalize($input);
        $metrics = $data['project_type'] === 'renovation'
            ? $this->renovationMetrics($data)
            : $this->newBuildMetrics($data);
        $materials = $this->materials($data, $metrics);
        $phases = $this->phases($data, $metrics, $materials);
        $timeline = $this->timeline($phases);
        $summary = $this->summary($data, $metrics, $timeline);

        return [
            'input' => $data,
            'summary' => $summary,
            'scope' => $this->scope($data, $metrics),
            'phases' => $phases,
            'materials' => $materials,
            'timeline' => $timeline,
            'pricing_note' => 'Pricing is not final here. Leivant reviews Marketplace stock, Discovery providers, labour, delivery, drawings, BOQ, and site condition before sending a quotation.',
            'report_text' => $this->reportText($summary, $phases, $materials, $timeline),
        ];
    }

    public function normalize(array $input): array
    {
        $projectType = Str::of((string) ($input['project_type'] ?? 'new_construction'))->lower()->replace([' ', '-'], '_')->toString();
        $projectType = in_array($projectType, ['renovation', 'existing', 'existing_house'], true) ? 'renovation' : 'new_construction';
        $budget = $input['budget'] ?? null;
        $budget = is_numeric($budget) ? (int) $budget : (int) preg_replace('/\D+/', '', (string) $budget);
        $areas = $input['areas_to_modify'] ?? [];

        if (is_string($areas)) {
            $areas = array_filter(array_map('trim', explode(',', $areas)));
        }

        return [
            'project_type' => $projectType,
            'house_type' => $input['house_type'] ?? 'bungalow',
            'bedrooms' => max(1, min(8, (int) ($input['bedrooms'] ?? 3))),
            'bathrooms' => max(1, min(6, (int) ($input['bathrooms'] ?? 2))),
            'floors' => max(1, min(4, (int) ($input['floors'] ?? 1))),
            'plot_size' => $input['plot_size'] ?? 'medium',
            'finish_level' => $input['finish_level'] ?? 'standard',
            'roof_type' => $input['roof_type'] ?? 'pitched',
            'current_status' => $input['current_status'] ?? 'incomplete',
            'work_type' => $input['work_type'] ?? 'renovation',
            'areas_to_modify' => array_values(array_unique($areas ?: ['finishing'])),
            'location' => trim((string) ($input['location'] ?? '')),
            'budget' => $budget > 0 ? $budget : null,
            'notes' => trim((string) ($input['notes'] ?? '')),
            'name' => trim((string) ($input['name'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
        ];
    }

    private function newBuildMetrics(array $data): array
    {
        $base = [
            'bungalow' => 68,
            'duplex' => 92,
        ][$data['house_type']] ?? 68;
        $plotFactor = ['compact' => .92, 'medium' => 1, 'large' => 1.12][$data['plot_size']] ?? 1;
        $floorFactor = $data['house_type'] === 'duplex' ? 1.08 : 1;
        $floorArea = (int) round(($base + ($data['bedrooms'] * 14) + ($data['bathrooms'] * 6)) * $data['floors'] * $floorFactor * $plotFactor);
        $footprint = (int) ceil($floorArea / max(1, $data['floors']));
        $roofFactor = ['pitched' => 1.32, 'flat' => 1.08, 'hidden-parapet' => 1.15, 'slab' => 1.1][$data['roof_type']] ?? 1.2;
        $finishFactor = ['basic' => .92, 'standard' => 1, 'premium' => 1.18][$data['finish_level']] ?? 1;
        $duration = (int) ceil((18 + ($data['bedrooms'] * 1.5) + (($data['floors'] - 1) * 7)) * $finishFactor);

        return [
            'floor_area' => max(55, $floorArea),
            'footprint_area' => max(45, $footprint),
            'roof_area' => (int) ceil($footprint * $roofFactor),
            'wall_area' => (int) ceil($floorArea * 2.8),
            'duration_weeks' => max(16, $duration),
            'scope_factor' => 1,
        ];
    }

    private function renovationMetrics(array $data): array
    {
        $areaMap = [
            'living' => 24,
            'kitchen' => 16,
            'bedrooms' => 18 * max(1, min(4, $data['bedrooms'])),
            'bathrooms' => 7 * max(1, min(3, $data['bathrooms'])),
            'roof' => 55,
            'exterior' => 45,
            'electrical' => 35,
            'plumbing' => 28,
            'finishing' => 55,
            'extension' => 42,
        ];
        $baseArea = collect($data['areas_to_modify'])->sum(fn (string $area): int => $areaMap[$area] ?? 18);
        $workFactor = ['extension' => 1.35, 'renovation' => 1, 'finishing' => .78][$data['work_type']] ?? 1;
        $floorArea = (int) round(max(25, $baseArea) * $workFactor);
        $duration = (int) ceil(8 + ($floorArea / 14) + ($data['work_type'] === 'extension' ? 8 : 0));

        return [
            'floor_area' => $floorArea,
            'footprint_area' => $floorArea,
            'roof_area' => in_array('roof', $data['areas_to_modify'], true) ? max(45, (int) round($floorArea * 1.18)) : 0,
            'wall_area' => (int) ceil($floorArea * 2.4),
            'duration_weeks' => max(6, $duration),
            'scope_factor' => $data['work_type'] === 'extension' ? .72 : .46,
        ];
    }

    private function materials(array $data, array $metrics): array
    {
        $factor = $metrics['scope_factor'];
        $roofArea = $metrics['roof_area'] ?: (int) round($metrics['floor_area'] * .32);

        $materials = [
            ['Site Preparation', 'Setting out and site protection', max(1, ceil($metrics['floor_area'] / 120)), 'allowance', 'site setup'],
            ['Foundation', 'Cement', ceil((($metrics['floor_area'] * 3.4) + ($data['floors'] * 28)) * $factor), 'bags', 'floor area x concrete allowance'],
            ['Foundation', 'Sand / Mchanga', ceil(($metrics['floor_area'] / 15) * $factor), 'trips', 'floor area / 15'],
            ['Foundation', 'Aggregate / Kokoto', ceil(($metrics['floor_area'] / 19) * $factor), 'trips', 'floor area / 19'],
            ['Structure', 'Concrete blocks', ceil(($metrics['floor_area'] * 38) * $factor), 'blocks', 'floor area x walling factor'],
            ['Structure', 'Steel reinforcement', ceil(($metrics['floor_area'] * (5.8 + ($data['floors'] * .7))) * $factor), 'kg', 'floor area x structural factor'],
            ['Roofing', 'Roofing material allowance', ceil($roofArea * ($data['project_type'] === 'renovation' ? .72 : 1)), 'sqm', 'roof area allowance'],
            ['Electrical & Plumbing', 'Electrical wiring bundle', max(1, ceil(($data['bedrooms'] + $data['floors']) * $factor)), 'bundle', 'rooms and floors allowance'],
            ['Electrical & Plumbing', 'Plumbing fixture set', max(1, ceil(($data['bathrooms'] + 1) * $factor)), 'set', 'bathrooms and kitchen allowance'],
            ['Finishing', 'Floor tiles / finish material', ceil($metrics['floor_area'] * (['basic' => .42, 'standard' => .72, 'premium' => .88][$data['finish_level']] ?? .72)), 'sqm', 'floor area x finish factor'],
            ['Finishing', 'Paint', max(1, ceil($metrics['wall_area'] / 115)), '20L buckets', 'wall area / paint coverage'],
        ];

        return collect($materials)
            ->filter(fn (array $material): bool => (float) $material[2] > 0)
            ->map(fn (array $material): array => [
                'phase' => $material[0],
                'name' => $material[1],
                'quantity' => (float) $material[2],
                'unit' => $material[3],
                'formula' => $material[4],
                'pricing_source' => 'Marketplace / Discovery review',
            ])
            ->values()
            ->all();
    }

    private function phases(array $data, array $metrics, array $materials): array
    {
        $phaseTemplates = [
            ['Site Preparation', .10, 'Confirm access, site measurements, clearing, protection, and working route.'],
            ['Foundation', .18, $data['project_type'] === 'renovation' ? 'Confirm base repairs, extension foundation, slab corrections, and damp proofing where needed.' : 'Excavation, sub-base, concrete, damp proofing, and slab or footing works.'],
            ['Structure', .22, $data['project_type'] === 'renovation' ? 'Wall changes, openings, repairs, extension blockwork, columns, lintels, and structural checks.' : 'Walls, columns, lintels, beams, masonry, and structural coordination.'],
            ['Roofing', .14, 'Roof cover or slab waterproofing, drainage, gutters, access safety, and ceiling impact.'],
            ['Electrical & Plumbing', .15, 'Sockets, lighting, DB, water supply, drainage, inspection points, and fixture routing.'],
            ['Finishing', .21, 'Tiles, paint, ceiling, doors, windows, fittings, cleaning, and handover preparation.'],
        ];

        return collect($phaseTemplates)
            ->map(function (array $phase) use ($metrics, $materials): array {
                $duration = max(1, (int) ceil($metrics['duration_weeks'] * $phase[1]));

                return [
                    'name' => $phase[0],
                    'slug' => Str::slug($phase[0]),
                    'description' => $phase[2],
                    'duration_weeks' => $duration,
                    'materials' => collect($materials)->where('phase', $phase[0])->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function timeline(array $phases): array
    {
        $week = 1;
        $items = collect($phases)->map(function (array $phase) use (&$week): array {
            $start = $week;
            $end = $week + $phase['duration_weeks'] - 1;
            $week = $end + 1;

            return [
                'phase' => $phase['name'],
                'start_week' => $start,
                'end_week' => $end,
                'duration_weeks' => $phase['duration_weeks'],
            ];
        })->values();
        $total = (int) $items->sum('duration_weeks');

        return [
            'duration_weeks' => $total,
            'duration_label' => $this->durationLabel($total),
            'items' => $items->all(),
        ];
    }

    private function summary(array $data, array $metrics, array $timeline): array
    {
        $typeLabel = $data['project_type'] === 'renovation' ? 'Existing House / Renovation' : 'New Construction';

        return [
            'project_type' => $data['project_type'],
            'project_type_label' => $typeLabel,
            'house_type' => $data['project_type'] === 'renovation' ? 'Existing house' : Str::headline($data['house_type']),
            'size_estimate' => $metrics['floor_area'].' sqm planning area',
            'floor_area' => $metrics['floor_area'],
            'scope' => $data['project_type'] === 'renovation'
                ? Str::headline($data['work_type']).' for '.collect($data['areas_to_modify'])->map(fn ($area) => Str::headline($area))->implode(', ')
                : $data['bedrooms'].' bedroom, '.$data['bathrooms'].' bathroom '.Str::headline($data['house_type']),
            'location' => $data['location'],
            'budget' => $data['budget'],
            'timeline' => $timeline['duration_label'],
        ];
    }

    private function scope(array $data, array $metrics): array
    {
        return [
            'primary_work' => $data['project_type'] === 'renovation' ? Str::headline($data['work_type']) : 'Full new build route',
            'area_basis' => $metrics['floor_area'].' sqm calculated planning area',
            'pricing_basis' => 'Quantities are estimated now; prices are confirmed by Leivant after admin review.',
            'admin_review' => ['Drawings / measurements', 'Marketplace stock and prices', 'Discovery providers', 'Labour and supervision', 'Delivery and site access', 'Final BOQ and quotation'],
        ];
    }

    private function reportText(array $summary, array $phases, array $materials, array $timeline): string
    {
        $phaseLines = collect($phases)->map(fn (array $phase) => '- '.$phase['name'].': '.$phase['duration_weeks'].' week(s)')->implode("\n");
        $materialLines = collect($materials)->map(fn (array $material) => '- '.$material['name'].': '.$material['quantity'].' '.$material['unit'])->implode("\n");

        return trim(implode("\n\n", [
            'Construction Brief Generator Report',
            'Project type: '.$summary['project_type_label'],
            'Scope: '.$summary['scope'],
            'Size estimate: '.$summary['size_estimate'],
            'Timeline: '.$timeline['duration_label'],
            "Construction phases:\n".$phaseLines,
            "Materials estimate:\n".$materialLines,
            'Note: Leivant confirms pricing after reviewing Marketplace products, Discovery providers, BOQ, labour, delivery, and site condition.',
        ]));
    }

    private function durationLabel(int $weeks): string
    {
        $months = max(1, (int) ceil($weeks / 4));

        if ($months <= 1) {
            return $weeks.' weeks';
        }

        return $months.' month'.($months === 1 ? '' : 's').' estimate';
    }
}
