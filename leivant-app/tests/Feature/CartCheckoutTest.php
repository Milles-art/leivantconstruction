<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_sale_equipment_to_session_cart(): void
    {
        $product = $this->equipmentProduct([
            'name' => 'Professional Angle Grinder',
            'slug' => 'professional-angle-grinder',
            'price' => 180000,
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => 18000,
        ]);

        $this->post('/cart', [
            'slug' => $product->slug,
            'quantity' => 2,
            'purchase_type' => 'buy',
        ])->assertRedirect();

        $this->assertEquals(2, collect(session('cart.items'))->sum('quantity'));
        $this->assertEquals('buy', session('cart.items.0.purchase_type'));
        $this->assertEquals(360000, session('cart.items.0.line_total'));
    }

    public function test_guest_can_add_equipment_to_cart_with_live_json_response(): void
    {
        $product = $this->equipmentProduct([
            'name' => 'Claw Hammer',
            'slug' => 'claw-hammer',
            'price' => 35000,
            'is_for_sale' => true,
            'is_for_rent' => false,
        ]);

        $this->postJson('/cart', [
            'slug' => $product->slug,
            'quantity' => 1,
            'purchase_type' => 'buy',
        ])
            ->assertOk()
            ->assertJson([
                'message' => 'Claw Hammer added to cart.',
                'cart_count' => 1,
            ]);

        $this->assertEquals('claw-hammer', session('cart.items.0.slug'));
        $this->assertEquals(1, collect(session('cart.items'))->sum('quantity'));
    }

    public function test_rental_equipment_requires_dates(): void
    {
        $product = $this->equipmentProduct([
            'name' => 'Plate Compactor Honda Engine',
            'slug' => 'plate-compactor-honda-engine',
            'is_for_sale' => false,
            'is_for_rent' => true,
            'rental_price_per_day' => 120000,
        ]);

        $this->post('/cart', [
            'slug' => $product->slug,
            'quantity' => 1,
            'purchase_type' => 'rent',
        ])->assertSessionHasErrors(['rental_start_date', 'rental_end_date']);
    }

    public function test_rental_equipment_calculates_days_and_checkout_persists_order_item(): void
    {
        $product = $this->equipmentProduct([
            'name' => 'Mobile Scaffolding Set',
            'slug' => 'mobile-scaffolding-set',
            'price' => 1350000,
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => 65000,
        ]);

        $this->post('/cart', [
            'slug' => $product->slug,
            'quantity' => 2,
            'purchase_type' => 'rent',
            'rental_start_date' => now()->addDay()->toDateString(),
            'rental_end_date' => now()->addDays(3)->toDateString(),
        ])->assertRedirect();

        $item = session('cart.items.0');
        $this->assertEquals('rent', $item['purchase_type']);
        $this->assertEquals(3, $item['rental_days']);
        $this->assertEquals(390000, $item['line_total']);

        $this->post('/checkout', [
            'name' => 'Leivant Client',
            'phone' => '+255717970799',
            'email' => 'client@example.com',
            'region' => 'Dar es Salaam',
            'delivery_address' => 'Salasala, Dar es Salaam',
            'delivery_notes' => 'Deliver to site gate and call supervisor.',
            'payment_channel' => 'mpesa',
        ])->assertRedirect();

        $this->assertDatabaseHas('order_items', [
            'product_name' => 'Mobile Scaffolding Set',
            'purchase_type' => 'rent',
            'quantity' => 2,
            'rental_days' => 3,
            'line_total' => 390000,
        ]);
    }

    public function test_checkout_requires_cart_items(): void
    {
        $this->post('/checkout', [
            'name' => 'Gaspary Jovin Lyamuya',
            'phone' => '+255717970799',
            'region' => 'Dar es Salaam',
            'delivery_address' => 'Salasala, Dar es Salaam',
            'payment_channel' => 'mpesa',
        ])->assertRedirect('/products');
    }

    public function test_checkout_uses_region_selection_and_mock_payment_message(): void
    {
        Region::factory()->create(['name' => 'Tanga', 'slug' => 'tanga']);

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('Tanga')
            ->assertSee('mock payment mode');
    }

    private function equipmentProduct(array $overrides = []): Product
    {
        $region = Region::factory()->create(['name' => 'Dar es Salaam', 'slug' => 'dar-es-salaam']);
        $provider = Provider::factory()->create([
            'region_id' => $region->id,
            'name' => 'Leivant Equipment Desk',
            'slug' => 'vant-equipment-desk',
            'category' => 'Company Equipment',
        ]);
        $category = Category::factory()->create([
            'name' => 'Concrete Equipment',
            'slug' => 'concrete-equipment',
        ]);

        return Product::factory()->create(array_merge([
            'category_id' => $category->id,
            'provider_id' => $provider->id,
            'name' => 'Portable Concrete Mixer 350L',
            'slug' => 'portable-concrete-mixer-350l',
            'description' => 'Leivant-owned equipment for construction sites.',
            'price' => 1850000,
            'unit' => 'unit',
            'stock' => 4,
            'region' => 'Dar es Salaam',
            'is_for_sale' => true,
            'is_for_rent' => true,
            'rental_price_per_day' => 85000,
            'availability_status' => 'available',
            'equipment_condition' => 'Good working condition',
            'is_active' => true,
        ], $overrides));
    }
}
