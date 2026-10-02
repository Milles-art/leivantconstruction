<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_public_pages_render_vant_content(): void
    {
        foreach (['/', '/services', '/products', '/solution', '/discovery', '/contact'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('Leivant');
        }
    }

    public function test_product_filters_render_tools_equipment_catalog(): void
    {
        $this->get('/products?category=Concrete%20Equipment&mode=rent')
            ->assertOk()
            ->assertSee('Buy Construction Materials')
            ->assertSee('Marketplace')
            ->assertSee('Heavy equipment rental');
    }

    public function test_marketplace_includes_core_building_materials(): void
    {
        $this->get('/products?search=Mchanga')
            ->assertOk()
            ->assertSee('Mchanga')
            ->assertSee('Building Materials');

        $this->get('/products?search=Kokoto')
            ->assertOk()
            ->assertSee('Kokoto')
            ->assertSee('Crushed Aggregate');
    }

    public function test_contact_page_has_professional_company_inquiry_form(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Tell Leivant What Your Project Needs')
            ->assertSee('Heavy equipment rental')
            ->assertSee('Leivant Construction Desk');
    }
}
