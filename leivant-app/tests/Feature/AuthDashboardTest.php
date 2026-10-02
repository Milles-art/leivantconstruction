<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_forms_render_polished_provider_content(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Provider and admin login')
            ->assertSee('Register your business');

        $this->get('/register')
            ->assertOk()
            ->assertSee('Provider Registration')
            ->assertSee('New profiles are reviewed by Leivant');
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['password' => bcrypt('password')]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin');
    }

    public function test_provider_dashboard_shows_pending_and_approved_status(): void
    {
        $pendingProvider = Provider::factory()->create([
            'name' => 'Pending Supplier',
            'is_active' => false,
            'is_verified' => false,
        ]);
        $pendingUser = User::factory()->create(['provider_id' => $pendingProvider->id]);

        $this->actingAs($pendingUser)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Pending Leivant Review')
            ->assertSee('Not Public Yet');

        $approvedProvider = Provider::factory()->create([
            'name' => 'Approved Supplier',
            'is_active' => true,
            'is_verified' => true,
        ]);
        $approvedUser = User::factory()->create(['provider_id' => $approvedProvider->id]);

        $this->actingAs($approvedUser)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Approved Profile')
            ->assertSee('Visible In Discovery');
    }

    public function test_provider_can_update_profile_and_return_to_review(): void
    {
        $region = Region::factory()->create(['name' => 'Tanga', 'slug' => 'tanga']);
        $provider = Provider::factory()->create([
            'name' => 'Old Equipment Hire',
            'category' => 'Equipment Rental',
            'region_id' => $region->id,
            'is_active' => true,
            'is_verified' => true,
        ]);
        $user = User::factory()->create([
            'provider_id' => $provider->id,
            'email' => 'old@example.com',
            'phone' => '+255700000001',
        ]);

        $this->actingAs($user)
            ->get('/dashboard/profile')
            ->assertOk()
            ->assertSee('Update your business details');

        $this->actingAs($user)
            ->patch('/dashboard/profile', [
                'provider_name' => 'Tanga Site Equipment',
                'category' => 'Equipment Rental',
                'region_id' => $region->id,
                'location' => 'Tanga City',
                'email' => 'tanga@example.com',
                'phone' => '+255700000002',
                'description' => 'Concrete mixers, scaffolding, compactors, and site equipment rental for coastal projects.',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'name' => 'Tanga Site Equipment',
            'slug' => 'tanga-site-equipment',
            'is_active' => false,
            'is_verified' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Tanga Site Equipment',
            'email' => 'tanga@example.com',
            'phone' => '+255700000002',
        ]);
    }
}
