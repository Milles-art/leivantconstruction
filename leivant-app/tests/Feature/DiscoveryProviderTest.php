<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_discovery_is_public_and_shows_provider_account_entry_points(): void
    {
        $region = Region::factory()->create(['name' => 'Tanga', 'slug' => 'tanga']);
        Provider::factory()->create([
            'region_id' => $region->id,
            'name' => 'Tanga Equipment Hire',
            'slug' => 'tanga-equipment-hire',
            'category' => 'Equipment Rental',
            'location' => 'Tanga City',
            'description' => 'Concrete mixers, compactors, scaffolding, and generators for coastal projects.',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->get('/discovery?region=Tanga&category=Equipment%20Rental&search=compactors')
            ->assertOk()
            ->assertSee('Find construction providers across Tanzania')
            ->assertSee('Register Your Business')
            ->assertSee('Provider Login')
            ->assertSee('Tanga Equipment Hire')
            ->assertSee('Equipment Rental')
            ->assertSee('Tanga City');
    }

    public function test_provider_registration_creates_pending_profile_not_public_listing(): void
    {
        $region = Region::factory()->create(['name' => 'Mbeya', 'slug' => 'mbeya']);

        $this->post('/register', [
            'provider_name' => 'Mbeya Trade Fundis',
            'category' => 'Skilled Labour',
            'region_id' => $region->id,
            'location' => 'Mbeya City',
            'email' => 'mbeya@example.com',
            'phone' => '+255700111222',
            'description' => 'Skilled masons, carpenters, plumbers, and electricians for regional projects.',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('providers', [
            'name' => 'Mbeya Trade Fundis',
            'is_active' => false,
            'is_verified' => false,
        ]);

        $this->get('/discovery?search=Mbeya%20Trade%20Fundis')
            ->assertOk()
            ->assertSee('No matching providers');
    }
}
