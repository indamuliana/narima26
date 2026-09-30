<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Lulus Wikrama Siap Membangun Negeri');
        $response->assertSee('Solusi pendidikan akhlak berkualitas di zaman modern');
        $response->assertSee('brosur.smkwikrama1garut.sch.id');
        $response->assertSee('Gelombang Aktif', false);
        $response->assertSee('SMK Wikrama 1 Garut', false);

        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
    }
}
