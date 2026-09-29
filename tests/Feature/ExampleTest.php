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
        $response->assertSee('Galeri Fasilitas SMK Wikrama 1 Garut');
        $response->assertSee('Galeri Proses Pembelajaran Nyata');
        $response->assertSee('Profil 12 Alumni Berprestasi');
        $response->assertSee('Testimoni 8 Pimpinan Industri Mitra', false);
        $response->assertSee('Testimoni 6 Tokoh Masyarakat & Pendidikan', false);
        $response->assertSee('Testimoni 6 Orang Tua Siswa & Santri', false);
        $response->assertSee('Gelombang Aktif', false);
        $response->assertSee('Lab-TJKT.jpg', false);
        $response->assertSee('10. Kemana saya menghubungi panitia', false);

        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
    }
}
