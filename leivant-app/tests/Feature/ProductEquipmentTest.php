<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductEquipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_filters_equipment_by_category_mode_and_price(): void
    {
        $this->equipmentProduct([
            'name' => 'Portable Concrete Mixer 350L',
            'slug' => 'portable-concrete-mixer-350l',
            'category_name' => 'Concrete Equipment',
            'region' => 'Dar es Salaam',
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => 85000,
        ]);

        $this->equipmentProduct([
            'name' => 'Site Safety Gear Kit',
            'slug' => 'site-safety-gear-kit',
            'category_name' => 'Site Safety',
            'region' => 'Arusha',
            'is_for_sale' => true,
            'is_for_rent' => false,
            'rental_price_per_day' => null,
        ]);

        $this->get('/products?search=mixer&category=Concrete%20Equipment&mode=rent&min_price=50000&max_price=100000')
            ->assertOk()
            ->assertSee('Buy Construction Materials')
            ->assertSee('Portable Concrete Mixer 350L')
            ->assertSee('Rent')
            ->assertDontSee('Site Safety Gear Kit');
    }

    public function test_admin_can_create_equipment_with_sale_and_rental_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $region = Region::factory()->create(['name' => 'Dar es Salaam', 'slug' => 'dar-es-salaam']);
        $provider = Provider::factory()->create([
            'region_id' => $region->id,
            'name' => 'Leivant Equipment Desk',
            'slug' => 'vant-equipment-desk',
            'category' => 'Company Equipment',
        ]);
        $category = Category::factory()->create([
            'name' => 'Power Tools',
            'slug' => 'power-tools',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Professional Laser Level Kit',
                'region' => 'Dar es Salaam',
                'category_id' => $category->id,
                'provider_id' => $provider->id,
                'description' => 'Leivant-owned laser level kit for layout, alignment, and finishing checks.',
                'price' => 420000,
                'unit' => 'item',
                'stock' => 8,
                'is_for_sale' => '1',
                'is_for_rent' => '1',
                'rental_price_per_day' => 30000,
                'availability_status' => 'available',
                'equipment_condition' => 'Site-tested',
                'is_active' => '1',
            ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Professional Laser Level Kit',
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => 30000,
            'availability_status' => 'available',
            'equipment_condition' => 'Site-tested',
        ]);
    }

    private function equipmentProduct(array $overrides = []): Product
    {
        $regionName = $overrides['region'] ?? 'Dar es Salaam';
        $categoryName = $overrides['category_name'] ?? 'Concrete Equipment';

        $region = Region::factory()->create([
            'name' => $regionName,
            'slug' => str($regionName)->slug(),
        ]);
        $provider = Provider::factory()->create([
            'region_id' => $region->id,
            'name' => 'Leivant Equipment Desk',
            'slug' => 'vant-equipment-desk-'.str()->random(6),
            'category' => 'Company Equipment',
        ]);
        $category = Category::factory()->create([
            'name' => $categoryName,
            'slug' => str($categoryName)->slug().'-'.str()->random(6),
        ]);

        unset($overrides['category_name']);

        return Product::factory()->create(array_merge([
            'category_id' => $category->id,
            'provider_id' => $provider->id,
            'name' => 'Portable Concrete Mixer 350L',
            'slug' => 'portable-concrete-mixer-350l',
            'description' => 'Leivant-owned equipment for construction sites.',
            'price' => 1850000,
            'unit' => 'unit',
            'stock' => 4,
            'region' => $regionName,
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => 85000,
            'availability_status' => 'available',
            'equipment_condition' => 'Good working condition',
            'is_active' => true,
        ], $overrides));
    }
}
