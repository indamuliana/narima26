<?php

namespace Tests\Feature;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSeragam;
use App\Models\Tagihan;
use App\Models\UkuranSeragam;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeragamKlasterPemesananTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswaUser;
    protected CalonSiswa $calonSiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->siswaUser = User::where('role', User::ROLE_CALON_SISWA)->first();

        $program = MasterProgram::first();
        $jurusan = MasterJurusan::first();
        $gelombang = MasterGelombang::first();

        $this->calonSiswa = CalonSiswa::factory()->create([
            'user_id' => $this->siswaUser->id,
            'nomor_pendaftaran' => '26AAY9999',
            'nisn' => '0099881122',
            'nama_lengkap' => 'Ahmad Testing',
            'jenis_kelamin' => 'L',
            'status_spmb' => SpmbStatus::MELENGKAPI_DATA,
            'status_data' => 'BELUM_LENGKAP',
            'program_id' => $program->id,
            'jurusan_id' => $jurusan->id,
            'gelombang_id' => $gelombang->id,
        ]);
    }

    public function test_candidate_can_view_seragam_tab_with_klaster(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.lengkapi-data.index', ['tab' => 'seragam']));

        $response->assertOk();
        $response->assertSee('Klaster 1 prioritas', false);
        $response->assertSee('Klaster 2', false);
        $response->assertSee('Klaster 3', false);
        $response->assertSee('Pesan Sekarang');
        $response->assertSee('Pesan Nanti');
        $response->assertSee('Tidak Pesan');
    }

    public function test_candidate_can_save_seragam_with_mixed_pesan_sekarang_and_pesan_nanti(): void
    {
        $masterList = MasterSeragam::aktif()
            ->where(function ($q) {
                $q->whereNull('jenis_kelamin')->orWhere('jenis_kelamin', 'L');
            })
            ->get()
            ->unique('nama_jenis');

        $this->assertNotEmpty($masterList);

        $payload = [];
        $i = 0;
        foreach ($masterList as $ms) {
            // Put MPLS in PESAN_SEKARANG, KBM in PESAN_NANTI, and OPSIONAL in TIDAK_PESAN
            if ($ms->klaster === MasterSeragam::KLASTER_MPLS) {
                $status = UkuranSeragam::STATUS_PESAN_SEKARANG;
            } elseif ($ms->klaster === MasterSeragam::KLASTER_KBM) {
                $status = UkuranSeragam::STATUS_PESAN_NANTI;
            } else {
                $status = UkuranSeragam::STATUS_TIDAK_PESAN;
            }

            $payload[$i++] = [
                'jenis_seragam_id' => $ms->id,
                'ukuran' => 'XL',
                'status_pemesanan' => $status,
                'jumlah' => 1,
            ];
        }

        $response = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.seragam'), [
                'seragam' => $payload,
            ]);

        $response->assertRedirect(route('calon-siswa.lengkapi-data.index', ['tab' => 'seragam']));
        $response->assertSessionHas('success');

        // Check database records
        $savedItems = UkuranSeragam::where('calon_siswa_id', $this->calonSiswa->id)->get();
        $this->assertGreaterThan(0, $savedItems->count());

        $nowItems = $savedItems->where('status_pemesanan', UkuranSeragam::STATUS_PESAN_SEKARANG);
        $laterItems = $savedItems->where('status_pemesanan', UkuranSeragam::STATUS_PESAN_NANTI);
        $noItems = $savedItems->where('status_pemesanan', UkuranSeragam::STATUS_TIDAK_PESAN);

        $this->assertGreaterThan(0, $nowItems->count());
        $this->assertGreaterThan(0, $laterItems->count());
        $this->assertGreaterThan(0, $noItems->count());

        foreach ($nowItems as $item) {
            $this->assertTrue($item->beli_di_sekolah);
            $this->assertTrue($item->isPesanSekarang());
        }

        foreach ($laterItems as $item) {
            $this->assertFalse($item->beli_di_sekolah);
            $this->assertTrue($item->isPesanNanti());
        }

        foreach ($noItems as $item) {
            $this->assertFalse($item->beli_di_sekolah);
            $this->assertTrue($item->isTidakPesan());
        }
    }

    public function test_initial_uniform_invoice_only_bills_pesan_sekarang_items(): void
    {
        $masterList = MasterSeragam::aktif()
            ->where(function ($q) {
                $q->whereNull('jenis_kelamin')->orWhere('jenis_kelamin', 'L');
            })
            ->get()
            ->unique('nama_jenis');

        $payload = [];
        $i = 0;
        foreach ($masterList as $ms) {
            $status = ($ms->klaster === MasterSeragam::KLASTER_MPLS)
                ? UkuranSeragam::STATUS_PESAN_SEKARANG
                : UkuranSeragam::STATUS_PESAN_NANTI;

            $payload[$i++] = [
                'jenis_seragam_id' => $ms->id,
                'ukuran' => 'L',
                'status_pemesanan' => $status,
                'jumlah' => 1,
            ];
        }

        $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.seragam'), ['seragam' => $payload]);

        // Generate initial uniform invoice
        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->generateUniformInvoice($this->calonSiswa);

        $this->assertInstanceOf(Tagihan::class, $invoice);
        $this->assertEquals(Tagihan::JENIS_SERAGAM, $invoice->jenis_tagihan);
        $this->assertEquals(1, $invoice->tahap_seragam);

        // Check that only PESAN_SEKARANG items are attached
        $savedItems = UkuranSeragam::where('calon_siswa_id', $this->calonSiswa->id)->get();
        foreach ($savedItems as $item) {
            if ($item->status_pemesanan === UkuranSeragam::STATUS_PESAN_SEKARANG) {
                $this->assertEquals($invoice->id, $item->tagihan_id);
                $this->assertEquals(1, $item->tahap_pemesanan);
            } else {
                $this->assertNull($item->tagihan_id);
            }
        }
    }

    public function test_candidate_can_self_service_activate_pending_uniforms(): void
    {
        // 1. Initial selection: Jas Almamater (MPLS) is PESAN_SEKARANG, others PESAN_NANTI
        $masterList = MasterSeragam::aktif()
            ->where(function ($q) {
                $q->whereNull('jenis_kelamin')->orWhere('jenis_kelamin', 'L');
            })
            ->get()
            ->unique('nama_jenis');

        $payload = [];
        $i = 0;
        foreach ($masterList as $ms) {
            $status = ($ms->nama_jenis === 'Jas Almamater')
                ? UkuranSeragam::STATUS_PESAN_SEKARANG
                : UkuranSeragam::STATUS_PESAN_NANTI;

            $payload[$i++] = [
                'jenis_seragam_id' => $ms->id,
                'ukuran' => 'M',
                'status_pemesanan' => $status,
                'jumlah' => 1,
            ];
        }

        $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.lengkapi-data.seragam'), ['seragam' => $payload]);

        // 2. Candidate transitions to MENUNGGU_DAFTAR_ULANG
        $this->calonSiswa->update(['status_spmb' => SpmbStatus::MENUNGGU_DAFTAR_ULANG]);

        // Generate initial uniform invoice
        $invoiceService = app(InvoiceService::class);
        $invoice1 = $invoiceService->generateUniformInvoice($this->calonSiswa);
        $this->assertNotNull($invoice1);

        // 3. View Daftar Ulang page, should see pending uniforms
        $response = $this->actingAs($this->siswaUser)
            ->get(route('calon-siswa.daftar-ulang.index', ['tab' => 'seragam']));

        $response->assertOk();
        $response->assertSee('Sisa Seragam Sekolah (Pesan Nanti)');
        $response->assertSee('Aktifkan Pemesanan & Terbitkan Tagihan Susulan', false);

        // 4. Select 2 pending uniforms to activate
        $pendingItems = UkuranSeragam::where('calon_siswa_id', $this->calonSiswa->id)
            ->where('status_pemesanan', UkuranSeragam::STATUS_PESAN_NANTI)
            ->whereNull('tagihan_id')
            ->take(2)
            ->get();

        $this->assertCount(2, $pendingItems);
        $selectedIds = $pendingItems->pluck('id')->toArray();

        // 5. Submit activation
        $postResponse = $this->actingAs($this->siswaUser)
            ->post(route('calon-siswa.daftar-ulang.aktivasi-seragam'), [
                'ukuran_seragam_ids' => $selectedIds,
            ]);

        $postResponse->assertRedirect(route('calon-siswa.daftar-ulang.index', ['tab' => 'seragam']));
        $postResponse->assertSessionHas('success');

        // 6. Verify second invoice is created
        $invoice2 = Tagihan::where('calon_siswa_id', $this->calonSiswa->id)
            ->where('jenis_tagihan', Tagihan::JENIS_SERAGAM)
            ->where('tahap_seragam', 2)
            ->first();

        $this->assertNotNull($invoice2);
        $this->assertStringContainsString('TAG-SRG2', $invoice2->nomor_tagihan);

        // 7. Verify the 2 selected items are updated and linked to invoice 2
        foreach ($selectedIds as $id) {
            $updatedItem = UkuranSeragam::find($id);
            $this->assertEquals(UkuranSeragam::STATUS_PESAN_SEKARANG, $updatedItem->status_pemesanan);
            $this->assertTrue($updatedItem->beli_di_sekolah);
            $this->assertEquals(2, $updatedItem->tahap_pemesanan);
            $this->assertEquals($invoice2->id, $updatedItem->tagihan_id);
        }
    }
}
