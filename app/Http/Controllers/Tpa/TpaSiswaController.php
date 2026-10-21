<?php

namespace App\Http\Controllers\Tpa;

use App\Http\Controllers\Controller;
use App\Models\TpaPengaturan;
use App\Models\TpaSoal;
use App\Services\TpaExamService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TpaSiswaController extends Controller
{
    public function __construct(
        protected TpaExamService $examService
    ) {}

    /**
     * Tampilan Ruang Tunggu (Waiting Room) sebelum mulai ujian.
     */
    public function ruangTunggu(): View|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        if (! $calonSiswa) {
            abort(403, 'Profil calon siswa tidak ditemukan.');
        }

        $jadwal = $calonSiswa->tpaJadwal()->first();
        $sesi = $calonSiswa->tpaSesi()->first();
        $pengaturan = TpaPengaturan::getSettings();

        // Jika sesi sudah selesai atau didiskualifikasi, arahkan ke halaman selesai
        if ($sesi && in_array($sesi->status, ['selesai', 'didiskualifikasi'])) {
            return redirect()->route('tpa.selesai');
        }

        // Cek status izin masuk
        $isBolehMasuk = $jadwal ? $jadwal->isBolehMasukNow() : false;

        $totalSoalLiterasi = TpaSoal::literasi()->aktif()->count();
        $totalSoalNumerasi = TpaSoal::numerasi()->aktif()->count();

        return view('tpa.ruang-tunggu', compact(
            'calonSiswa',
            'jadwal',
            'sesi',
            'pengaturan',
            'isBolehMasuk',
            'totalSoalLiterasi',
            'totalSoalNumerasi'
        ));
    }

    /**
     * Siswa menekan tombol 'Mulai Ujian' dari ruang tunggu.
     */
    public function mulaiUjian(): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $jadwal = $calonSiswa?->tpaJadwal()->first();

        if (! $jadwal || ! $jadwal->isBolehMasukNow()) {
            return redirect()->route('tpa.ruang-tunggu')
                ->with('error', 'Waktu ujian belum tiba atau akses Anda belum diizinkan oleh panitia.');
        }

        $sesi = $this->examService->getOrCreateSesi($calonSiswa);

        if ($sesi->status === 'belum_mulai') {
            $this->examService->mulaiBabak($sesi, 'literasi');
        } elseif ($sesi->status === 'transisi') {
            $this->examService->mulaiBabak($sesi, 'numerasi');
        } elseif (in_array($sesi->status, ['selesai', 'didiskualifikasi'])) {
            return redirect()->route('tpa.selesai');
        }

        return redirect()->route('tpa.ujian');
    }

    /**
     * Halaman Pengerjaan Soal (2/3 Soal, 1/3 Kisi Lembar Jawaban).
     */
    public function ujian(): View|RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $sesi = $calonSiswa->tpaSesi;
        $pengaturan = TpaPengaturan::getSettings();

        if (! $sesi || $sesi->status === 'belum_mulai') {
            return redirect()->route('tpa.ruang-tunggu');
        }

        if (in_array($sesi->status, ['selesai', 'didiskualifikasi'])) {
            return redirect()->route('tpa.selesai');
        }

        // Jika dalam status transisi setelah Babak 1
        if ($sesi->status === 'transisi') {
            return view('tpa.transisi', compact('calonSiswa', 'sesi', 'pengaturan'));
        }

        // Tentukan babak aktif
        $babakAktif = $sesi->status === 'babak_1' ? 'literasi' : 'numerasi';
        $soalIds = $babakAktif === 'literasi' ? ($sesi->urutan_soal_literasi ?? []) : ($sesi->urutan_soal_numerasi ?? []);

        // Cek sisa waktu (server time)
        $sisaWaktuDetik = $this->examService->getSisaWaktuDetik($sesi, $pengaturan);

        // Jika waktu babak habis, auto advance / auto submit
        if ($sisaWaktuDetik <= 0) {
            if ($babakAktif === 'literasi') {
                $this->examService->selesaikanBabak1($sesi);
                return redirect()->route('tpa.ujian');
            } else {
                $this->examService->selesaikanBabak2($sesi);
                return redirect()->route('tpa.selesai');
            }
        }

        // Ambil data soal terurut sesuai acakan siswa
        $soalList = TpaSoal::whereIn('id', $soalIds)->get()->keyBy('id');
        $soalSorted = collect($soalIds)->map(fn($id) => $soalList->get($id))->filter()->values();

        // Ambil peta jawaban siswa
        $jawabanMap = $sesi->jawabanSiswa->keyBy('tpa_soal_id');

        return view('tpa.ujian', [
            'calonSiswa' => $calonSiswa,
            'sesi' => $sesi,
            'pengaturan' => $pengaturan,
            'babakAktif' => $babakAktif,
            'soalSorted' => $soalSorted,
            'jawabanMap' => $jawabanMap,
            'sisaWaktuDetik' => $sisaWaktuDetik,
        ]);
    }

    /**
     * Endpoint AJAX untuk menyimpan jawaban real-time.
     */
    public function simpanJawaban(Request $request): JsonResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $sesi = $calonSiswa?->tpaSesi;

        if (! $sesi || ! in_array($sesi->status, ['babak_1', 'babak_2'])) {
            return response()->json(['success' => false, 'message' => 'Sesi ujian tidak aktif.'], 403);
        }

        $request->validate([
            'soal_id' => 'required|integer|exists:tpa_soal,id',
            'jawaban' => 'nullable|string|max:5',
            'is_ragu' => 'required|boolean',
        ]);

        $jawaban = $this->examService->simpanJawaban(
            $sesi,
            $request->integer('soal_id'),
            $request->input('jawaban'),
            $request->boolean('is_ragu')
        );

        return response()->json([
            'success' => true,
            'data' => [
                'soal_id' => $jawaban->tpa_soal_id,
                'jawaban' => $jawaban->jawaban_dipilih,
                'is_ragu' => $jawaban->is_ragu,
            ],
        ]);
    }

    /**
     * Konfirmasi penyelesaian Babak 1 (Literasi) lalu masuk ke jeda transisi Babak 2.
     */
    public function submitBabak1(): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $sesi = $calonSiswa?->tpaSesi;

        if ($sesi && $sesi->status === 'babak_1') {
            $this->examService->selesaikanBabak1($sesi);
        }

        return redirect()->route('tpa.ujian');
    }

    /**
     * Konfirmasi penyelesaian Babak 2 (Numerasi) lalu tuntaskan seluruh ujian TPA.
     */
    public function submitBabak2(): RedirectResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $sesi = $calonSiswa?->tpaSesi;

        if ($sesi && $sesi->status === 'babak_2') {
            $this->examService->selesaikanBabak2($sesi);
        }

        return redirect()->route('tpa.selesai');
    }

    /**
     * Endpoint AJAX deteksi kecurangan (tab switch, minimize window).
     */
    public function catatPelanggaran(Request $request): JsonResponse
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $sesi = $calonSiswa?->tpaSesi;
        $pengaturan = TpaPengaturan::getSettings();

        if (! $sesi || ! in_array($sesi->status, ['babak_1', 'babak_2'])) {
            return response()->json(['success' => false], 403);
        }

        $alasan = $request->input('alasan', 'Meninggalkan tab pengerjaan ujian');
        $violations = $sesi->catatPelanggaran($alasan);
        $isDisqualified = false;

        if ($violations >= $pengaturan->max_pelanggaran) {
            $this->examService->diskualifikasi($sesi, 'Batas toleransi pelanggaran terlampaui (' . $violations . ' kali).');
            $isDisqualified = true;
        }

        return response()->json([
            'success' => true,
            'violations' => $violations,
            'max_pelanggaran' => $pengaturan->max_pelanggaran,
            'is_disqualified' => $isDisqualified,
            'redirect_url' => $isDisqualified ? route('tpa.selesai') : null,
        ]);
    }

    /**
     * Halaman Selesai / Tuntas Ujian TPA (Kerahasiaan skor tetap terjaga).
     */
    public function selesai(): View
    {
        $calonSiswa = auth()->user()->calonSiswa;
        $sesi = $calonSiswa?->tpaSesi;

        return view('tpa.selesai', compact('calonSiswa', 'sesi'));
    }
}
