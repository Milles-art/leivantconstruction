<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Inquiry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_and_detail_pages_render_company_service_content(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('Construction Services')
            ->assertSee('Site Support')
            ->assertDontSee('Construction Tool Lending');

        foreach (['architecture', 'engineering', 'construction', 'skilled-labour', 'site-support'] as $slug) {
            $this->get("/services/{$slug}")
                ->assertOk()
                ->assertSee('What Leivant Delivers')
                ->assertSee('Submit Service Request');
        }
    }

    public function test_guest_can_submit_structured_service_request(): void
    {
        Queue::fake();
        $service = $this->createService('Architecture', 'architecture');

        $this->post(route('services.request', 'architecture'), [
            'name' => 'Amina Client',
            'company' => 'Amina Properties',
            'phone' => '+255717970799',
            'email' => 'amina@example.com',
            'region' => 'Dar es Salaam',
            'site_location' => 'Salasala, Kinondoni',
            'project_type' => 'New home design',
            'project_stage' => 'Land or site already secured',
            'budget_range' => 'TZS 20 million - 75 million',
            'timeline' => 'Within 1 to 3 months',
            'preferred_contact' => 'whatsapp',
            'message' => 'We need design support for a family house with room to expand later.',
        ])->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inquiries', [
            'service_id' => $service->id,
            'name' => 'Amina Client',
            'company' => 'Amina Properties',
            'region' => 'Dar es Salaam',
            'site_location' => 'Salasala, Kinondoni',
            'project_type' => 'New home design',
            'timeline' => 'Within 1 to 3 months',
            'preferred_contact' => 'whatsapp',
            'status' => 'new',
        ]);

        Queue::assertPushed(SendWhatsAppNotification::class);
    }

    public function test_guest_can_submit_house_planner_request_through_contact_flow(): void
    {
        Queue::fake();

        $this->post(route('contact.store'), [
            'name' => 'Neema Planner',
            'phone' => '+255717111222',
            'email' => 'neema@example.com',
            'subject' => 'House planning and design request',
            'project_type' => 'New family house',
            'site_location' => 'Kibaha, Pwani',
            'timeline' => 'Within 1 month',
            'budget_range' => 'TZS 25M - 100M',
            'preferred_contact' => 'whatsapp',
            'project_details' => "Planning checklist\nDesign and documents: Concept layout and room schedule; Architectural drawings and elevations\nDesign services\n2D floor plan direction; 3D house visualization; Interior Design Planning; Exterior Design Planning\nCost and BOQ services\nConstruction Cost Estimation; BOQ Preparation; Material Quantity Estimation\nTechnical layout services\nElectrical Layout Planning; Plumbing Layout Planning; Structural Planning\nProject delivery services\nConstruction Supervision; Project Management; Contractor Coordination",
            'message' => 'House planning and design studio request for a 3-4 room single floor concept. Generated checklist is included in the project details field.',
        ])->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Neema Planner',
            'phone' => '+255717111222',
            'subject' => 'House planning and design request',
            'project_type' => 'New family house',
            'site_location' => 'Kibaha, Pwani',
            'timeline' => 'Within 1 month',
            'budget_range' => 'TZS 25M - 100M',
            'preferred_contact' => 'whatsapp',
            'status' => 'new',
        ]);

        $inquiry = Inquiry::query()->where('subject', 'House planning and design request')->firstOrFail();
        $this->assertStringContainsString('Design and documents', $inquiry->message);
        $this->assertStringContainsString('BOQ Preparation', $inquiry->message);
        $this->assertStringContainsString('Project Management', $inquiry->message);
        $this->assertStringContainsString('Generated checklist is included', $inquiry->message);

        Queue::assertPushed(SendWhatsAppNotification::class);
    }

    public function test_service_request_validates_required_project_fields(): void
    {
        $this->createService('Construction', 'construction');

        $this->from('/services/construction')
            ->post(route('services.request', 'construction'), [])
            ->assertRedirect('/services/construction')
            ->assertSessionHasErrors([
                'name',
                'phone',
                'region',
                'site_location',
                'project_type',
                'timeline',
                'preferred_contact',
                'message',
            ]);
    }

    public function test_admin_inquiry_detail_shows_structured_service_request_data(): void
    {
        $admin = User::factory()->admin()->create();
        $service = $this->createService('Construction', 'construction');
        $inquiry = Inquiry::query()->create([
            'service_id' => $service->id,
            'name' => 'Joseph Builder',
            'company' => 'JB Holdings',
            'phone' => '+255700000001',
            'email' => 'joseph@example.com',
            'region' => 'Arusha',
            'site_location' => 'Njiro',
            'subject' => 'Construction service request - Site supervision',
            'project_type' => 'Site supervision',
            'project_stage' => 'Construction already started',
            'budget_range' => 'TZS 75 million - 250 million',
            'timeline' => 'Within 2 to 4 weeks',
            'preferred_contact' => 'phone',
            'message' => 'We need Leivant to review progress and coordinate finishing works.',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.inquiries.show', $inquiry))
            ->assertOk()
            ->assertSee('Project Brief')
            ->assertSee('JB Holdings')
            ->assertSee('Njiro')
            ->assertSee('Site supervision')
            ->assertSee('TZS 75 million - 250 million');
    }

    private function createService(string $name, string $slug): Service
    {
        return Service::query()->create([
            'name' => $name,
            'slug' => $slug,
            'summary' => "{$name} service summary.",
            'description' => "{$name} service description.",
            'icon' => $slug,
            'is_active' => true,
        ]);
    }
}
