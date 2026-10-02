<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ConstructionPhase;
use App\Models\HouseTemplate;
use App\Models\Material;
use App\Models\MaterialPrice;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Provider;
use App\Models\Region;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD');

        if (app()->environment('production') && (! $adminPassword || $adminPassword === 'password')) {
            throw new \RuntimeException('ADMIN_PASSWORD must be set to a strong value before seeding production.');
        }

        $admin = User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'ovinigaspary@gmail.com')],
            [
                'name' => env('ADMIN_NAME', 'Leivant Admin'),
                'phone' => '+255717970799',
                'account_type' => 'admin',
                'password' => Hash::make($adminPassword ?: 'password'),
                'is_admin' => true,
            ]
        );

        $regions = collect([
            'Arusha',
            'Dar es Salaam',
            'Dodoma',
            'Geita',
            'Iringa',
            'Kagera',
            'Katavi',
            'Kigoma',
            'Kilimanjaro',
            'Lindi',
            'Manyara',
            'Mara',
            'Mbeya',
            'Morogoro',
            'Mtwara',
            'Mwanza',
            'Njombe',
            'Pwani',
            'Rukwa',
            'Ruvuma',
            'Shinyanga',
            'Simiyu',
            'Singida',
            'Songwe',
            'Tabora',
            'Tanga',
            'Kaskazini Pemba',
            'Kusini Pemba',
            'Mjini Magharibi',
            'Kaskazini Unguja',
            'Kusini Unguja',
        ])
            ->mapWithKeys(fn ($name) => [
                $name => Region::query()->updateOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                ),
            ]);

        $catalogCategories = collect(config('vant_products.categories', []));
        $catalogProducts = collect(config('vant_products.products', []));
        $equipmentCategorySlugs = $catalogCategories->pluck('slug')->all();

        $categories = $catalogCategories->mapWithKeys(fn ($category) => [
            $category['name'] => Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'description' => $category['description'], 'is_active' => true]
            ),
        ]);

        Category::query()
            ->whereNotIn('slug', $equipmentCategorySlugs)
            ->whereIn('slug', ['cement-aggregates', 'steel-rebar', 'blocks-stones', 'roofing', 'finishes', 'concrete-equipment', 'lifting-access', 'site-safety', 'compaction-generators'])
            ->update(['is_active' => false]);

        collect([
            ['Architecture', 'Concept design, working drawings, permit-ready documentation, layouts, and residential or commercial planning.'],
            ['Engineering', 'Structural review, civil works advice, foundation guidance, drainage planning, MEP coordination, and site inspections.'],
            ['Construction', 'Managed construction execution for new builds, renovations, roofing, finishing, supervision, and site progress.'],
            ['Skilled Labour', 'Planned skilled trade support for masons, steel fixers, carpenters, electricians, plumbers, painters, and tile setters.'],
            ['Site Support', 'Reliable site support for preparation, loading, trenching, cleaning, mixing, and material movement under supervision.'],
        ])->each(function ($service): void {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($service[0])],
                [
                    'name' => $service[0],
                    'summary' => $service[1],
                    'description' => $service[1].' Leivant reviews the project location, scope, budget range, timeline, and readiness before advising the next delivery step.',
                    'icon' => Str::slug($service[0]),
                    'image_path' => null,
                    'is_active' => true,
                ]
            );
        });

        $providers = collect([
            ['Leivant Equipment Desk', 'Company Equipment', 'Dar es Salaam', '+255717970799', 'Internal Leivant-owned tools and equipment inventory desk.', 5.0],
            ['Kijenge Supplies Ltd', 'Material Suppliers', 'Dar es Salaam', '+255717970799', 'Fast cement, sand, and block delivery across Kinondoni, Ubungo, and Ilala.', 4.8],
            ['Mwanza Steel Traders', 'Material Suppliers', 'Mwanza', '+255713440220', 'Rebar, binding wire, and structural steel for lake-zone projects.', 4.7],
            ['Arusha Buildline Architects', 'Architects', 'Arusha', '+255754601992', 'Modern residential and lodge design with permit-ready documentation.', 4.9],
            ['Dodoma Civil Works Group', 'Contractors', 'Dodoma', '+255768884101', 'Foundations, drainage, boundary walls, and commercial renovations.', 4.6],
            ['Morogoro Engineering Bureau', 'Engineers', 'Morogoro', '+255742118901', 'Structural design, site inspections, and technical reports.', 4.8],
            ['Tanga Equipment Hire', 'Equipment Rental', 'Tanga', '+255713440221', 'Concrete mixers, compactors, scaffolding, generators, and site equipment rental for coastal projects.', 4.6],
            ['Mbeya Trade Fundis', 'Skilled Labour', 'Mbeya', '+255713440222', 'Masons, carpenters, plumbers, painters, electricians, and tile setters for regional construction sites.', 4.5],
            ['Kilimanjaro Plumbing & Electrical', 'Electrical & Plumbing', 'Kilimanjaro', '+255713440223', 'Building wiring, plumbing installation, maintenance, and testing support for residential and commercial work.', 4.6],
            ['Pwani Site Logistics', 'Transport & Logistics', 'Pwani', '+255713440224', 'Material transport, site delivery coordination, loading teams, and regional construction logistics.', 4.4],
        ])->mapWithKeys(function ($provider) use ($regions) {
            $created = Provider::query()->updateOrCreate(
                ['slug' => Str::slug($provider[0])],
                [
                    'region_id' => $regions[$provider[2]]->id,
                    'name' => $provider[0],
                    'category' => $provider[1],
                    'phone' => $provider[3],
                    'email' => Str::slug($provider[0]).'@leivant.test',
                    'location' => $provider[2],
                    'description' => $provider[4],
                    'rating' => $provider[5],
                    'is_verified' => true,
                    'is_active' => true,
                ]
            );

            return [$provider[0] => $created];
        });

        $equipmentSlugs = $catalogProducts->pluck('slug')->all();
        $regionsForCatalog = $regions->keys()->values();

        $catalogProducts->each(function (array $productData, int $index) use ($categories, $providers, $regionsForCatalog): void {
            $provider = $providers['Leivant Equipment Desk'];
            $regionName = $regionsForCatalog[$index % $regionsForCatalog->count()] ?? 'Dar es Salaam';
            $name = $productData['name'];
            $categoryName = $productData['category'];

            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $categories[$categoryName]->id,
                    'provider_id' => $provider->id,
                    'name' => $name,
                    'description' => ucfirst($productData['use']).'.',
                    'price' => $productData['price'],
                    'unit' => $productData['unit'],
                    'stock' => $productData['stock'],
                    'region' => $regionName,
                    'is_for_sale' => $productData['is_for_sale'],
                    'is_for_rent' => $productData['is_for_rent'],
                    'rental_price_per_day' => $productData['rental_price_per_day'],
                    'availability_status' => $productData['availability_status'],
                    'equipment_condition' => $productData['equipment_condition'],
                    'is_featured' => $index < 8,
                    'is_active' => true,
                ]
            );

            $path = $this->storeCatalogImage($productData, $categoryName);

            ProductImage::query()->updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 0],
                ['path' => $path, 'alt_text' => $name, 'is_primary' => true]
            );
        });

        Product::query()
            ->whereNotIn('slug', $equipmentSlugs)
            ->update(['is_active' => false]);

        $this->seedConstructionPlanner($providers);

        $admin->reviews()->create([
            'rating' => 5,
            'comment' => 'Seeded review for Leivant quality checks.',
            'is_approved' => true,
        ]);
    }

    private function seedConstructionPlanner($providers): void
    {
        collect([
            ['Modern Bungalow', 'modern-bungalow', 'modern-bungalow', 62, 16, 1.00, 20, ['massing' => 'wide-low', 'accent' => '#f5b700']],
            ['Modern Duplex', 'duplex', 'duplex', 82, 18, 1.08, 28, ['massing' => 'split-two-floor', 'accent' => '#38bdf8']],
            ['Rental Apartment Block', 'rental-apartment', 'rental-apartment', 110, 22, 1.20, 36, ['massing' => 'vertical-block', 'accent' => '#f8fafc']],
        ])->each(function (array $template): void {
            HouseTemplate::query()->updateOrCreate(
                ['slug' => $template[1]],
                [
                    'name' => $template[0],
                    'house_type' => $template[2],
                    'base_area_sqm' => $template[3],
                    'area_per_bedroom_sqm' => $template[4],
                    'floor_multiplier' => $template[5],
                    'base_duration_weeks' => $template[6],
                    'formulas' => [],
                    'model_config' => $template[7],
                    'is_active' => true,
                ]
            );
        });

        $phases = collect([
            ['Foundation', 'foundation', 1, .17, 'Setting out, excavation, sub-base, concrete, damp proofing, and slab/foundation preparation.'],
            ['Walls', 'walls', 2, .21, 'Blockwork, steel, columns, lintels, beams, plaster base, and structural coordination.'],
            ['Roofing', 'roofing', 3, .13, 'Roof cover, trusses or roof slab allowance, waterproofing, drainage, and access safety.'],
            ['Electrical', 'electrical', 4, .10, 'Wiring routes, sockets, DB, lighting points, temporary power, and testing allowance.'],
            ['Plumbing', 'plumbing', 5, .10, 'Water supply, drainage, inspection points, sanitary fixtures, and wet area checks.'],
            ['Finishing', 'finishing', 6, .29, 'Tiles, paint, ceiling, fittings, doors, windows, final fixtures, cleaning, and handover.'],
        ])->mapWithKeys(fn (array $phase) => [
            $phase[0] => ConstructionPhase::query()->updateOrCreate(
                ['slug' => $phase[1]],
                ['name' => $phase[0], 'sort_order' => $phase[2], 'duration_factor' => $phase[3], 'description' => $phase[4]]
            ),
        ]);

        collect([
            ['Foundation', 'Cement Bags', 'cement-bags', 'bag', 'cement_bags', 1.08, 'cement-bags', 'Kijenge Supplies Ltd'],
            ['Foundation', 'Mchanga / Building Sand Trip', 'mchanga-building-sand-trip', 'trip', 'sand_trips', 1.06, 'mchanga-building-sand-trip', 'Kijenge Supplies Ltd'],
            ['Foundation', 'Kokoto / Crushed Aggregate Trip', 'kokoto-crushed-aggregate-trip', 'trip', 'aggregate_trips', 1.06, 'kokoto-crushed-aggregate-trip', 'Kijenge Supplies Ltd'],
            ['Walls', 'Concrete Blocks 6 Inch', 'concrete-blocks-6-inch', 'block', 'blocks', 1.10, 'concrete-blocks-6-inch', 'Kijenge Supplies Ltd'],
            ['Walls', 'Reinforcement Steel Rebar 12mm', 'reinforcement-steel-rebar-12mm', 'kg', 'steel_kg', 1.08, 'reinforcement-steel-rebar-12mm', 'Mwanza Steel Traders'],
            ['Roofing', 'Roofing Sheets Gauge 28', 'roofing-sheets-gauge-28', 'sqm', 'roofing_sqm', 1.08, 'roofing-sheets-gauge-28', 'Tanga Equipment Hire'],
            ['Electrical', 'Electrical Wiring Bundle', 'electrical-wiring-bundle', 'bundle', 'electrical_bundles', 1.05, 'electrical-wiring-bundle', 'Kilimanjaro Plumbing & Electrical'],
            ['Plumbing', 'Plumbing Fixture Set', 'plumbing-fixture-set', 'set', 'plumbing_sets', 1.00, 'plumbing-fixture-set', 'Kilimanjaro Plumbing & Electrical'],
            ['Finishing', 'Floor Tiles Standard', 'floor-tiles-standard', 'sqm', 'tiles_sqm', 1.08, 'floor-tiles-standard', 'Mbeya Trade Fundis'],
            ['Finishing', 'Interior Paint 20L', 'interior-paint-20l', 'bucket', 'paint_buckets', 1.05, 'interior-paint-20l', 'Mbeya Trade Fundis'],
            ['Finishing', 'Gypsum Ceiling Board', 'gypsum-ceiling-board', 'board', 'gypsum_boards', 1.05, 'gypsum-ceiling-board', 'Mbeya Trade Fundis'],
        ])->each(function (array $row) use ($phases, $providers): void {
            $material = Material::query()->updateOrCreate(
                ['slug' => $row[2]],
                [
                    'construction_phase_id' => $phases[$row[0]]->id,
                    'name' => $row[1],
                    'unit' => $row[3],
                    'formula_key' => $row[4],
                    'waste_factor' => $row[5],
                    'source_hint' => 'marketplace',
                    'is_active' => true,
                ]
            );

            $product = Product::query()->where('slug', $row[6])->first();
            $provider = $providers[$row[7]] ?? null;

            if ($product) {
                MaterialPrice::query()->updateOrCreate(
                    ['material_id' => $material->id, 'product_id' => $product->id],
                    [
                        'provider_id' => $provider?->id,
                        'region' => $product->region,
                        'price' => $product->price,
                        'unit' => $product->unit,
                        'source' => $provider ? 'discovery' : 'marketplace',
                        'is_active' => true,
                        'checked_at' => now(),
                    ]
                );
            }
        });
    }

    private function storeCatalogImage(array $productData, string $category): string
    {
        $slug = $productData['slug'];

        foreach (['webp', 'jpg', 'jpeg', 'png', 'svg'] as $extension) {
            $localPath = public_path("images/catalog-tools/{$slug}.{$extension}");

            if (is_file($localPath)) {
                $path = "products/{$slug}.{$extension}";
                Storage::disk('public')->put($path, file_get_contents($localPath));

                return $path;
            }
        }

        $path = "products/{$slug}.svg";
        Storage::disk('public')->put($path, $this->productSvg($productData['name'], $category, '#D4A017'));

        return $path;
    }

    private function productSvg(string $name, string $category, string $color): string
    {
        $safeName = e($name);
        $safeCategory = e($category);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800" role="img" aria-label="{$safeName}">
  <rect width="1200" height="800" fill="#0a0a0a"/>
  <rect x="80" y="80" width="1040" height="640" rx="28" fill="{$color}" opacity="0.18"/>
  <path d="M80 620 390 360l170 130 230-260 330 390v100H80z" fill="{$color}" opacity="0.55"/>
  <rect x="120" y="120" width="360" height="44" fill="#D4A017"/>
  <rect x="120" y="190" width="620" height="24" fill="#ffffff" opacity="0.65"/>
  <text x="120" y="310" fill="#ffffff" font-family="Arial, sans-serif" font-size="58" font-weight="700">{$safeName}</text>
  <text x="120" y="380" fill="#D4A017" font-family="Arial, sans-serif" font-size="34" font-weight="700">{$safeCategory}</text>
  <text x="120" y="690" fill="#ffffff" font-family="Arial, sans-serif" font-size="30" font-weight="700">Leivant Construction Solutions</text>
</svg>
SVG;
    }
}
