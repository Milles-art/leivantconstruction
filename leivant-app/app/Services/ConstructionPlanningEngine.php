<?php

namespace App\Services;

use App\Models\ConstructionPhase;
use App\Models\HouseTemplate;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConstructionPlanningEngine
{
    public function templates(): array
    {
        if (Schema::hasTable('house_templates') && HouseTemplate::query()->where('is_active', true)->exists()) {
            return HouseTemplate::query()
                ->where('is_active', true)
                ->orderBy('base_area_sqm')
                ->get()
                ->map(fn (HouseTemplate $template) => $this->templatePayload($template))
                ->all();
        }

        return array_values(self::defaultTemplates());
    }

    public function materials(): array
    {
        if (Schema::hasTable('materials') && Material::query()->where('is_active', true)->exists()) {
            return Material::query()
                ->with(['phase', 'prices.product', 'prices.provider'])
                ->where('is_active', true)
                ->get()
                ->map(function (Material $material): array {
                    $price = $this->priceFor($material->slug, $material->name, $material->unit);

                    return [
                        'name' => $material->name,
                        'slug' => $material->slug,
                        'phase' => $material->phase?->name ?? Str::headline($material->formula_key),
                        'unit' => $material->unit,
                        'formula_key' => $material->formula_key,
                        'source' => $price['source'],
                        'unit_cost' => $price['price'],
                    ];
                })
                ->values()
                ->all();
        }

        return collect(self::defaultMaterials())
            ->map(fn (array $material) => array_merge($material, [
                'source' => $this->priceFor($material['slug'], $material['name'], $material['unit'])['source'],
                'unit_cost' => $this->priceFor($material['slug'], $material['name'], $material['unit'])['price'],
            ]))
            ->values()
            ->all();
    }

    public function generate(array $input): array
    {
        $normalized = $this->normalizeInput($input);
        $template = $this->resolveTemplate($normalized['house_type']);
        $metrics = $this->metrics($normalized, $template);
        $materials = $this->materialLines($normalized, $metrics);
        $phases = $this->phases($normalized, $metrics, $materials);
        $totals = $this->totals($normalized, $metrics, $phases);

        return [
            'input' => $normalized,
            'template' => $template,
            'summary' => [
                'house_type' => $template['name'],
                'bedrooms' => $normalized['bedrooms'],
                'floors' => $normalized['floors'],
                'finish_level' => $normalized['finish_level'],
                'roof_type' => $normalized['roof_type'],
                'plot_size' => $normalized['plot_size'],
                'location' => $normalized['location'],
                'estimated_floor_area' => $metrics['floor_area'],
                'footprint_area' => $metrics['footprint_area'],
                'roof_area' => $metrics['roof_area'],
                'estimated_duration_weeks' => $metrics['duration_weeks'],
                'budget' => $normalized['budget'],
                'budget_status' => $totals['budget_status'],
                'budget_gap' => $totals['budget_gap'],
                'recommendation' => $this->recommendation($normalized, $totals),
            ],
            'phases' => $phases,
            'materials' => $materials,
            'totals' => $totals,
            'timeline' => $this->timeline($phases),
            'visual' => $this->visualPayload($normalized, $template, $metrics),
            'report_text' => $this->reportText($template, $normalized, $metrics, $phases, $totals),
        ];
    }

    public function reportText(array $template, array $input, array $metrics, array $phases, array $totals): string
    {
        $phaseLines = collect($phases)
            ->map(fn (array $phase) => $phase['name'].': '.number_format($phase['cost']).' TZS, '.$phase['duration_weeks'].' weeks')
            ->implode("\n");

        return trim(implode("\n\n", [
            'Construction Planning System Report',
            'House concept: '.$template['name'],
            'Brief: '.$input['bedrooms'].' bedroom(s), '.$input['floors'].' floor(s), '.$input['finish_level'].' finish, '.$input['roof_type'].' roof, '.$input['plot_size'].' plot.',
            'Estimated floor area: '.$metrics['floor_area'].' sqm',
            'Estimated duration: '.$metrics['duration_weeks'].' weeks',
            'Estimated total: '.number_format($totals['total_cost']).' TZS',
            'Budget status: '.$totals['budget_status'],
            "Construction phases:\n".$phaseLines,
            'Note: This is a rule-based planning estimate. Leivant admin should confirm current Marketplace stock, Discovery providers, site condition, drawings, BOQ, labour, delivery, and final quotation before procurement or construction.',
        ]));
    }

    private function normalizeInput(array $input): array
    {
        $budget = $input['budget'] ?? null;
        $budget = is_numeric($budget) ? (int) $budget : (int) preg_replace('/\D+/', '', (string) $budget);

        return [
            'house_type' => $input['house_type'] ?? $input['goal'] ?? 'modern-bungalow',
            'bedrooms' => max(1, min(8, (int) ($input['bedrooms'] ?? 3))),
            'floors' => max(1, min(4, (int) ($input['floors'] ?? 1))),
            'finish_level' => $input['finish_level'] ?? 'standard',
            'roof_type' => $input['roof_type'] ?? 'pitched',
            'plot_size' => $input['plot_size'] ?? 'medium',
            'budget' => $budget > 0 ? $budget : null,
            'location' => trim((string) ($input['location'] ?? $input['site_location'] ?? '')),
            'notes' => trim((string) ($input['notes'] ?? '')),
            'name' => trim((string) ($input['name'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
        ];
    }

    private function resolveTemplate(string $houseType): array
    {
        $slug = Str::slug($houseType);

        if (Schema::hasTable('house_templates')) {
            $template = HouseTemplate::query()
                ->where('is_active', true)
                ->where(fn ($query) => $query->where('slug', $slug)->orWhere('house_type', $slug))
                ->first();

            if ($template) {
                return $this->templatePayload($template);
            }
        }

        return self::defaultTemplates()[$slug] ?? self::defaultTemplates()['modern-bungalow'];
    }

    private function templatePayload(HouseTemplate $template): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'slug' => $template->slug,
            'house_type' => $template->house_type,
            'base_area_sqm' => $template->base_area_sqm,
            'area_per_bedroom_sqm' => $template->area_per_bedroom_sqm,
            'floor_multiplier' => (float) $template->floor_multiplier,
            'base_duration_weeks' => $template->base_duration_weeks,
            'formulas' => $template->formulas ?? [],
            'model_config' => $template->model_config ?? [],
        ];
    }

    private function metrics(array $input, array $template): array
    {
        $finishFactors = ['basic' => 0.92, 'standard' => 1, 'premium' => 1.22, 'luxury' => 1.42];
        $plotFactors = ['compact' => 0.92, 'medium' => 1, 'large' => 1.12, 'estate' => 1.25];
        $roofFactors = ['flat' => 1.08, 'pitched' => 1.32, 'hidden-parapet' => 1.15, 'slab' => 1.1];

        $floorMultiplier = (float) ($template['floor_multiplier'] ?? 1);
        $floorArea = (int) round((
            ($template['base_area_sqm'] ?? 72)
            + ($input['bedrooms'] * ($template['area_per_bedroom_sqm'] ?? 14))
        ) * $input['floors'] * $floorMultiplier * ($plotFactors[$input['plot_size']] ?? 1));

        $footprint = (int) ceil($floorArea / max(1, $input['floors']));
        $roofArea = (int) ceil($footprint * ($roofFactors[$input['roof_type']] ?? 1.22));
        $duration = (int) ceil(($template['base_duration_weeks'] ?? 20) + ($input['bedrooms'] * 1.4) + (($input['floors'] - 1) * 7));
        $duration = (int) ceil($duration * ($finishFactors[$input['finish_level']] ?? 1));

        return [
            'floor_area' => $floorArea,
            'footprint_area' => $footprint,
            'roof_area' => $roofArea,
            'wall_area' => (int) ceil($floorArea * 2.85),
            'duration_weeks' => max(12, $duration),
        ];
    }

    private function materialLines(array $input, array $metrics): array
    {
        $definitions = $this->materialDefinitions();

        return $definitions
            ->map(function (array $material) use ($input, $metrics): array {
                $quantity = $this->quantity($material['formula_key'], $input, $metrics);
                $quantity = ceil($quantity * (float) ($material['waste_factor'] ?? 1.08));
                $price = $this->priceFor($material['slug'], $material['name'], $material['unit']);

                return [
                    'id' => $material['id'] ?? null,
                    'phase' => $material['phase'],
                    'name' => $material['name'],
                    'slug' => $material['slug'],
                    'quantity' => $quantity,
                    'unit' => $material['unit'],
                    'unit_cost' => $price['price'],
                    'estimated_cost' => (int) round($quantity * $price['price']),
                    'source' => $price['source'],
                    'source_label' => $price['label'],
                ];
            })
            ->values()
            ->all();
    }

    private function phases(array $input, array $metrics, array $materials): array
    {
        $phaseDefaults = self::defaultPhases();
        $phaseCosts = collect($materials)->groupBy('phase')->map(fn (Collection $items) => (int) $items->sum('estimated_cost'));
        $finishFactor = ['basic' => 0.95, 'standard' => 1, 'premium' => 1.2, 'luxury' => 1.38][$input['finish_level']] ?? 1;

        return collect($phaseDefaults)
            ->map(function (array $phase) use ($input, $metrics, $materials, $phaseCosts, $finishFactor): array {
                $phaseMaterials = collect($materials)->where('phase', $phase['name'])->values()->all();
                $baseCost = (int) ($phaseCosts[$phase['name']] ?? 0);
                $labour = (int) round($baseCost * ($phase['labour_factor'] ?? 0.32) * $finishFactor);
                $duration = max(1, (int) ceil($metrics['duration_weeks'] * ($phase['duration_share'] ?? 0.14)));

                return [
                    'name' => $phase['name'],
                    'slug' => $phase['slug'],
                    'description' => $phase['description'],
                    'duration_weeks' => $duration,
                    'cost' => $baseCost + $labour,
                    'material_cost' => $baseCost,
                    'labour_allowance' => $labour,
                    'materials' => $phaseMaterials,
                ];
            })
            ->values()
            ->all();
    }

    private function totals(array $input, array $metrics, array $phases): array
    {
        $materialAndLabour = (int) collect($phases)->sum('cost');
        $professional = (int) round($metrics['floor_area'] * 45000);
        $contingency = (int) round(($materialAndLabour + $professional) * 0.08);
        $total = $materialAndLabour + $professional + $contingency;
        $budget = $input['budget'];
        $gap = $budget ? $budget - $total : null;

        return [
            'phase_costs' => collect($phases)->map(fn (array $phase) => ['phase' => $phase['name'], 'cost' => $phase['cost']])->values()->all(),
            'material_and_labour_cost' => $materialAndLabour,
            'professional_cost' => $professional,
            'contingency' => $contingency,
            'total_cost' => $total,
            'cost_per_sqm' => (int) round($total / max(1, $metrics['floor_area'])),
            'budget' => $budget,
            'budget_gap' => $gap,
            'budget_status' => $budget ? ($gap >= 0 ? 'within budget' : 'above budget') : 'budget not provided',
        ];
    }

    private function timeline(array $phases): array
    {
        $week = 1;

        return collect($phases)
            ->map(function (array $phase) use (&$week): array {
                $start = $week;
                $end = $week + $phase['duration_weeks'] - 1;
                $week = $end + 1;

                return [
                    'phase' => $phase['name'],
                    'start_week' => $start,
                    'end_week' => $end,
                    'duration_weeks' => $phase['duration_weeks'],
                ];
            })
            ->values()
            ->all();
    }

    private function recommendation(array $input, array $totals): string
    {
        if (! $input['budget']) {
            return 'Add a budget to compare the concept against available funds.';
        }

        if ($totals['budget_gap'] >= 0) {
            return 'The selected scope is inside the stated budget range. Keep admin review for live prices and BOQ confirmation.';
        }

        if ($input['finish_level'] === 'luxury' || $input['finish_level'] === 'premium') {
            return 'The budget is tight for this finish level. Consider standard finishes or phased delivery.';
        }

        return 'The budget is below this rule-based estimate. Reduce scope, adjust roof/finish choices, or request a phased plan.';
    }

    private function visualPayload(array $input, array $template, array $metrics): array
    {
        return [
            'type' => $template['slug'],
            'floors' => $input['floors'],
            'bedrooms' => $input['bedrooms'],
            'roof_type' => $input['roof_type'],
            'finish_level' => $input['finish_level'],
            'width' => max(7, min(16, round(sqrt($metrics['footprint_area']) * 1.2, 1))),
            'depth' => max(6, min(14, round(sqrt($metrics['footprint_area']) * 0.9, 1))),
            'height' => 2.9 * $input['floors'],
            'accent' => ['basic' => '#d4a017', 'standard' => '#f5b700', 'premium' => '#38bdf8', 'luxury' => '#f8fafc'][$input['finish_level']] ?? '#f5b700',
        ];
    }

    private function quantity(string $key, array $input, array $metrics): float
    {
        return match ($key) {
            'cement_bags' => ($metrics['floor_area'] * 3.65) + ($input['floors'] * 38),
            'sand_trips' => $metrics['floor_area'] / 14,
            'aggregate_trips' => $metrics['floor_area'] / 18,
            'blocks' => $metrics['floor_area'] * 44,
            'steel_kg' => $metrics['floor_area'] * (6.8 + ($input['floors'] * 0.8)),
            'roofing_sqm' => $input['roof_type'] === 'slab' ? $metrics['roof_area'] * 0.35 : $metrics['roof_area'],
            'electrical_bundles' => max(2, ($input['bedrooms'] * 0.75) + $input['floors']),
            'plumbing_sets' => max(2, ceil(($input['bedrooms'] + 1) / 2)),
            'tiles_sqm' => $metrics['floor_area'] * (['basic' => 0.45, 'standard' => 0.72, 'premium' => 0.86, 'luxury' => 0.95][$input['finish_level']] ?? 0.72),
            'paint_buckets' => max(2, $metrics['wall_area'] / 115),
            'gypsum_boards' => $input['finish_level'] === 'basic' ? $metrics['floor_area'] * 0.1 : $metrics['floor_area'] / 3.2,
            default => $metrics['floor_area'],
        };
    }

    private function materialDefinitions(): Collection
    {
        if (Schema::hasTable('materials') && Material::query()->where('is_active', true)->exists()) {
            return Material::query()
                ->with('phase')
                ->where('is_active', true)
                ->get()
                ->map(fn (Material $material) => [
                    'id' => $material->id,
                    'phase' => $material->phase?->name ?? 'General',
                    'name' => $material->name,
                    'slug' => $material->slug,
                    'unit' => $material->unit,
                    'formula_key' => $material->formula_key,
                    'waste_factor' => (float) $material->waste_factor,
                ]);
        }

        return collect(self::defaultMaterials());
    }

    private function priceFor(string $slug, string $name, string $unit): array
    {
        if (Schema::hasTable('material_prices')) {
            $material = Material::query()->where('slug', $slug)->first();
            $price = $material?->prices()
                ->where('is_active', true)
                ->latest('checked_at')
                ->latest('id')
                ->first();

            if ($price) {
                return [
                    'price' => $price->price,
                    'source' => $price->source,
                    'label' => $price->source === 'discovery' ? 'Discovery provider price' : 'Marketplace product price',
                ];
            }
        }

        $product = $this->marketplaceProduct($slug, $name);

        if ($product) {
            return [
                'price' => (int) $product['price'],
                'source' => 'marketplace',
                'label' => 'Marketplace product price',
            ];
        }

        $fallback = collect(self::defaultMaterials())->firstWhere('slug', $slug);

        return [
            'price' => (int) ($fallback['fallback_price'] ?? 0),
            'source' => 'template',
            'label' => 'Planning template price',
        ];
    }

    private function marketplaceProduct(string $slug, string $name): ?array
    {
        $productSlug = self::defaultMaterials()[$slug]['product_slug'] ?? $slug;

        if (Schema::hasTable('products')) {
            $product = Product::query()
                ->where('is_active', true)
                ->where(fn ($query) => $query
                    ->where('slug', $productSlug)
                    ->orWhere('slug', $slug)
                    ->orWhere('name', 'like', '%'.$name.'%'))
                ->first();

            if ($product) {
                return ['price' => $product->price, 'unit' => $product->unit, 'source' => 'marketplace'];
            }
        }

        $catalog = collect(config('vant_products.products', []))
            ->first(fn (array $product) => ($product['slug'] ?? null) === $productSlug || str_contains(strtolower($product['name']), strtolower($name)));

        return $catalog ? ['price' => $catalog['price'], 'unit' => $catalog['unit'], 'source' => 'marketplace'] : null;
    }

    private static function defaultTemplates(): array
    {
        return [
            'modern-bungalow' => [
                'id' => null,
                'name' => 'Modern Bungalow',
                'slug' => 'modern-bungalow',
                'house_type' => 'modern-bungalow',
                'base_area_sqm' => 62,
                'area_per_bedroom_sqm' => 16,
                'floor_multiplier' => 1,
                'base_duration_weeks' => 20,
                'formulas' => [],
                'model_config' => ['massing' => 'wide-low', 'accent' => '#f5b700'],
            ],
            'duplex' => [
                'id' => null,
                'name' => 'Modern Duplex',
                'slug' => 'duplex',
                'house_type' => 'duplex',
                'base_area_sqm' => 82,
                'area_per_bedroom_sqm' => 18,
                'floor_multiplier' => 1.08,
                'base_duration_weeks' => 28,
                'formulas' => [],
                'model_config' => ['massing' => 'split-two-floor', 'accent' => '#38bdf8'],
            ],
            'rental-apartment' => [
                'id' => null,
                'name' => 'Rental Apartment Block',
                'slug' => 'rental-apartment',
                'house_type' => 'rental-apartment',
                'base_area_sqm' => 110,
                'area_per_bedroom_sqm' => 22,
                'floor_multiplier' => 1.2,
                'base_duration_weeks' => 36,
                'formulas' => [],
                'model_config' => ['massing' => 'vertical-block', 'accent' => '#f8fafc'],
            ],
        ];
    }

    private static function defaultPhases(): array
    {
        return [
            ['name' => 'Foundation', 'slug' => 'foundation', 'duration_share' => 0.17, 'labour_factor' => 0.28, 'description' => 'Setting out, excavation, sub-base, concrete, damp proofing, and slab/foundation preparation.'],
            ['name' => 'Walls', 'slug' => 'walls', 'duration_share' => 0.21, 'labour_factor' => 0.35, 'description' => 'Blockwork, steel, columns, lintels, beams, plaster base, and structural coordination.'],
            ['name' => 'Roofing', 'slug' => 'roofing', 'duration_share' => 0.13, 'labour_factor' => 0.3, 'description' => 'Roof cover, trusses or roof slab allowance, waterproofing, drainage, and access safety.'],
            ['name' => 'Electrical', 'slug' => 'electrical', 'duration_share' => 0.1, 'labour_factor' => 0.42, 'description' => 'Wiring routes, sockets, DB, lighting points, temporary power, and testing allowance.'],
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'duration_share' => 0.1, 'labour_factor' => 0.4, 'description' => 'Water supply, drainage, inspection points, sanitary fixtures, and wet area checks.'],
            ['name' => 'Finishing', 'slug' => 'finishing', 'duration_share' => 0.29, 'labour_factor' => 0.48, 'description' => 'Tiles, paint, ceiling, fittings, doors, windows, final fixtures, cleaning, and handover.'],
        ];
    }

    private static function defaultMaterials(): array
    {
        return [
            'cement-bags' => ['phase' => 'Foundation', 'name' => 'Cement Bags', 'slug' => 'cement-bags', 'unit' => 'bag', 'formula_key' => 'cement_bags', 'waste_factor' => 1.08, 'fallback_price' => 18500, 'product_slug' => 'cement-bags'],
            'mchanga-building-sand-trip' => ['phase' => 'Foundation', 'name' => 'Mchanga / Building Sand Trip', 'slug' => 'mchanga-building-sand-trip', 'unit' => 'trip', 'formula_key' => 'sand_trips', 'waste_factor' => 1.06, 'fallback_price' => 180000, 'product_slug' => 'mchanga-building-sand-trip'],
            'kokoto-crushed-aggregate-trip' => ['phase' => 'Foundation', 'name' => 'Kokoto / Crushed Aggregate Trip', 'slug' => 'kokoto-crushed-aggregate-trip', 'unit' => 'trip', 'formula_key' => 'aggregate_trips', 'waste_factor' => 1.06, 'fallback_price' => 220000, 'product_slug' => 'kokoto-crushed-aggregate-trip'],
            'concrete-blocks-6-inch' => ['phase' => 'Walls', 'name' => 'Concrete Blocks 6 Inch', 'slug' => 'concrete-blocks-6-inch', 'unit' => 'block', 'formula_key' => 'blocks', 'waste_factor' => 1.1, 'fallback_price' => 1500, 'product_slug' => 'concrete-blocks-6-inch'],
            'reinforcement-steel-rebar-12mm' => ['phase' => 'Walls', 'name' => 'Reinforcement Steel Rebar 12mm', 'slug' => 'reinforcement-steel-rebar-12mm', 'unit' => 'kg', 'formula_key' => 'steel_kg', 'waste_factor' => 1.08, 'fallback_price' => 3200, 'product_slug' => 'reinforcement-steel-rebar-12mm'],
            'roofing-sheets-gauge-28' => ['phase' => 'Roofing', 'name' => 'Roofing Sheets Gauge 28', 'slug' => 'roofing-sheets-gauge-28', 'unit' => 'sqm', 'formula_key' => 'roofing_sqm', 'waste_factor' => 1.08, 'fallback_price' => 26000, 'product_slug' => 'roofing-sheets-gauge-28'],
            'electrical-wiring-bundle' => ['phase' => 'Electrical', 'name' => 'Electrical Wiring Bundle', 'slug' => 'electrical-wiring-bundle', 'unit' => 'bundle', 'formula_key' => 'electrical_bundles', 'waste_factor' => 1.05, 'fallback_price' => 260000, 'product_slug' => 'electrical-wiring-bundle'],
            'plumbing-fixture-set' => ['phase' => 'Plumbing', 'name' => 'Plumbing Fixture Set', 'slug' => 'plumbing-fixture-set', 'unit' => 'set', 'formula_key' => 'plumbing_sets', 'waste_factor' => 1, 'fallback_price' => 380000, 'product_slug' => 'plumbing-fixture-set'],
            'floor-tiles-standard' => ['phase' => 'Finishing', 'name' => 'Floor Tiles Standard', 'slug' => 'floor-tiles-standard', 'unit' => 'sqm', 'formula_key' => 'tiles_sqm', 'waste_factor' => 1.08, 'fallback_price' => 28000, 'product_slug' => 'floor-tiles-standard'],
            'interior-paint-20l' => ['phase' => 'Finishing', 'name' => 'Interior Paint 20L', 'slug' => 'interior-paint-20l', 'unit' => 'bucket', 'formula_key' => 'paint_buckets', 'waste_factor' => 1.05, 'fallback_price' => 95000, 'product_slug' => 'interior-paint-20l'],
            'gypsum-ceiling-board' => ['phase' => 'Finishing', 'name' => 'Gypsum Ceiling Board', 'slug' => 'gypsum-ceiling-board', 'unit' => 'board', 'formula_key' => 'gypsum_boards', 'waste_factor' => 1.05, 'fallback_price' => 22000, 'product_slug' => 'gypsum-ceiling-board'],
        ];
    }
}
