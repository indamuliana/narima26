<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase1SetupTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that the landing page loads successfully and contains Nampi branding.
     */
    public function test_landing_page_loads_with_brand_and_helpdesk(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('NAMPI');
        $response->assertSee('SMK WIKRAMA 1 GARUT');
        $response->assertSee('https://wa.me/6281323314430', false);
        $response->assertSee('images/logo.png');
    }

    /**
     * Test that the primary assets exist in public/images.
     */
    public function test_branding_assets_exist(): void
    {
        $this->assertFileExists(public_path('images/logo.png'));
        $this->assertFileExists(public_path('images/kop_surat.jpg'));
    }
}
