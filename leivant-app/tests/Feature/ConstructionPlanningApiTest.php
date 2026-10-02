<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ConstructionPlanningApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_rule_based_planner_generates_house_plan_materials_and_costs(): void
    {
        $response = $this->postJson('/api/generate-plan', [
            'house_type' => 'duplex',
            'bedrooms' => 4,
            'floors' => 2,
            'finish_level' => 'premium',
            'roof_type' => 'hidden-parapet',
            'plot_size' => 'large',
            'budget' => 180000000,
            'location' => 'Kibaha, Pwani',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.summary.house_type', 'Modern Duplex')
            ->assertJsonPath('data.summary.floors', 2)
            ->assertJsonStructure([
                'data' => [
                    'summary' => ['estimated_floor_area', 'estimated_duration_weeks', 'budget_status'],
                    'phases' => [['name', 'duration_weeks', 'cost', 'materials']],
                    'materials' => [['phase', 'name', 'quantity', 'unit', 'unit_cost', 'estimated_cost']],
                    'totals' => ['total_cost', 'phase_costs', 'cost_per_sqm'],
                    'visual' => ['type', 'floors', 'roof_type', 'finish_level'],
                ],
            ]);

        $this->assertGreaterThan(0, $response->json('data.totals.total_cost'));
        $this->assertGreaterThan(0, $response->json('data.materials.0.quantity'));
    }

    public function test_planner_choices_change_generated_area_and_visual_model(): void
    {
        $bungalow = $this->postJson('/api/generate-plan', [
            'house_type' => 'modern-bungalow',
            'bedrooms' => 2,
            'floors' => 1,
            'finish_level' => 'standard',
            'roof_type' => 'pitched',
            'plot_size' => 'compact',
        ])->assertOk();

        $apartment = $this->postJson('/api/generate-plan', [
            'house_type' => 'rental-apartment',
            'bedrooms' => 4,
            'floors' => 3,
            'finish_level' => 'premium',
            'roof_type' => 'flat',
            'plot_size' => 'large',
        ])->assertOk();

        $this->assertGreaterThan(
            $bungalow->json('data.summary.estimated_floor_area'),
            $apartment->json('data.summary.estimated_floor_area')
        );
        $this->assertSame('rental-apartment', $apartment->json('data.visual.type'));
        $this->assertSame(3, $apartment->json('data.visual.floors'));
    }

    public function test_submitted_project_request_creates_project_materials_and_inquiry(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/project-requests', [
            'status' => 'submitted',
            'name' => 'Amina Planner',
            'phone' => '+255717111222',
            'email' => 'amina@example.com',
            'house_type' => 'modern-bungalow',
            'bedrooms' => 3,
            'floors' => 1,
            'finish_level' => 'standard',
            'roof_type' => 'pitched',
            'plot_size' => 'medium',
            'budget' => 100000000,
            'location' => 'Mbezi Beach',
            'notes' => 'Need a family house with open kitchen.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'submitted');

        $this->assertDatabaseHas('project_requests', [
            'name' => 'Amina Planner',
            'house_type' => 'modern-bungalow',
            'status' => 'submitted',
        ]);
        $this->assertDatabaseHas('inquiries', [
            'name' => 'Amina Planner',
            'subject' => 'Construction planning system request',
            'status' => 'new',
        ]);

        $project = ProjectRequest::query()->where('name', 'Amina Planner')->firstOrFail();
        $this->assertGreaterThan(0, $project->materials()->count());

        $inquiry = Inquiry::query()->where('subject', 'Construction planning system request')->firstOrFail();
        $this->assertStringContainsString('Construction Planning System Report', $inquiry->message);
    }

    public function test_admin_can_review_submitted_construction_plan_dashboard(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();

        $this->postJson('/api/project-requests', [
            'status' => 'submitted',
            'name' => 'Baraka Owner',
            'phone' => '+255718222333',
            'email' => 'baraka@example.com',
            'house_type' => 'duplex',
            'bedrooms' => 4,
            'floors' => 2,
            'finish_level' => 'premium',
            'roof_type' => 'hidden-parapet',
            'plot_size' => 'large',
            'budget' => 180000000,
            'location' => 'Dodoma City',
            'notes' => 'Need a premium family house with strong exterior design.',
        ])->assertCreated();

        $inquiry = Inquiry::query()->where('name', 'Baraka Owner')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.inquiries.show', $inquiry))
            ->assertOk()
            ->assertSee('Leivant Planning Dashboard')
            ->assertSee('Material Lines For Review')
            ->assertSee('Check Marketplace stock and prices')
            ->assertSee('Cement Bags')
            ->assertSee('Prepare BOQ and final quotation');
    }

    public function test_construction_brief_generator_handles_renovation_and_pdf(): void
    {
        $response = $this->postJson('/api/generate-brief', [
            'project_type' => 'renovation',
            'current_status' => 'old-house',
            'work_type' => 'renovation',
            'areas_to_modify' => ['kitchen', 'bathrooms', 'roof'],
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Mikocheni',
            'budget' => 40000000,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.summary.project_type_label', 'Existing House / Renovation')
            ->assertJsonStructure([
                'data' => [
                    'summary' => ['size_estimate', 'scope', 'timeline'],
                    'phases' => [['name', 'duration_weeks', 'materials']],
                    'materials' => [['phase', 'name', 'quantity', 'unit', 'pricing_source']],
                    'timeline' => ['duration_weeks', 'duration_label', 'items'],
                ],
            ]);

        $this->assertGreaterThan(0, $response->json('data.summary.floor_area'));
        $this->assertStringContainsString('Kitchen', $response->json('data.summary.scope'));

        $this->postJson('/api/generate-brief-pdf', [
            'project_type' => 'renovation',
            'current_status' => 'old-house',
            'work_type' => 'renovation',
            'areas_to_modify' => ['kitchen', 'bathrooms', 'roof'],
            'location' => 'Mikocheni',
        ])->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_project_submission_and_admin_project_api_flow(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();

        $submitted = $this->postJson('/api/projects', [
            'project_type' => 'new_construction',
            'house_type' => 'duplex',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'floors' => 2,
            'plot_size' => 'medium',
            'finish_level' => 'premium',
            'roof_type' => 'hidden-parapet',
            'location' => 'Kibaha',
            'budget' => 120000000,
            'name' => 'Grace Developer',
            'phone' => '+255711222333',
            'email' => 'grace@example.com',
        ]);

        $submitted->assertCreated()
            ->assertJsonPath('message', 'Your project has been submitted. Our team will review and send pricing.')
            ->assertJsonPath('data.status', 'pending');

        $projectId = $submitted->json('data.project_request_id');

        $this->assertDatabaseHas('project_requests', [
            'id' => $projectId,
            'project_type' => 'new_construction',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/admin/projects')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Grace Developer']);

        $this->actingAs($admin)
            ->putJson("/api/admin/projects/{$projectId}", [
                'status' => 'quoted',
                'admin_notes' => 'Marketplace and Discovery checks completed.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'quoted');

        $this->assertDatabaseHas('project_requests', [
            'id' => $projectId,
            'status' => 'quoted',
            'admin_notes' => 'Marketplace and Discovery checks completed.',
        ]);
    }

    public function test_admin_projects_react_panel_renders(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->assertSee('brief-admin-root')
            ->assertSee('Construction Brief Projects')
            ->assertSee('brief-admin-app.js');
    }
}
