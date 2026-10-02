<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_blocks_guest(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_dashboard_allows_admin_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_admin_can_create_provider_with_controlled_category(): void
    {
        $admin = User::factory()->admin()->create();
        $region = Region::factory()->create(['name' => 'Tanga', 'slug' => 'tanga']);

        $this->actingAs($admin)
            ->post(route('admin.providers.store'), [
                'name' => 'Tanga Equipment Hire',
                'category' => 'Equipment Rental',
                'region_id' => $region->id,
                'phone' => '+255700222333',
                'email' => 'tanga@example.com',
                'location' => 'Tanga City',
                'description' => 'Concrete mixers, scaffolding, compactors, and site equipment rental.',
                'rating' => 4.7,
                'is_verified' => '1',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.providers.index'));

        $this->assertDatabaseHas('providers', [
            'name' => 'Tanga Equipment Hire',
            'category' => 'Equipment Rental',
            'is_verified' => true,
            'is_active' => true,
        ]);
    }
}
