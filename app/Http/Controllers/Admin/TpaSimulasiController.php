<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\TpaJadwalSiswa;
use App\Models\TpaJawabanSiswa;
use App\Models\TpaSesiSiswa;
use App\Models\TpaSoal;
use App\Models\User;
use App\Services\TpaExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TpaSimulasiController extends Controller
{
    /**
     * Memulai Sesi Coba Ujian (Simulasi) oleh Administrator.
     */
    public function start(Request $request): RedirectResponse
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk mencoba simulasi ujian TPA.');
        }

        // Pastikan ada soal aktif di Bank Soal
        if (TpaSoal::aktif()->count() === 0) {
            return redirect()->route('admin.tpa.soal.index')
                ->with('error', 'Bank Soal masih kosong. Silakan tambahkan atau import butir soal terlebih dahulu sebelum mencoba ujian.');
        }

        // 1. Akun User Simulasi Khusus
        $simulasiUser = User::firstOrCreate(
            ['username' => 'simulasi.tpa'],
            [
                'name' => 'Calon Murid (Simulasi Admin)',
                'email' => 'simulasi.tpa@wikrama.sch.id',
                'password' => Hash::make('simulasi_admin_secret_123'),
                'role' => User::ROLE_CALON_SISWA,
                'is_active' => true,
            ]
        );

        // 2. Data Calon Siswa Simulasi
        $calonSiswa = CalonSiswa::withTrashed()->firstOrCreate(
            ['nomor_pendaftaran' => 'SIM-TPA-0001'],
            [
                'user_id' => $simulasiUser->id,
                'nama_lengkap' => 'Calon Murid (Simulasi Admin)',
                'nama_panggilan' => 'Simulasi',
                'nisn' => '0000000000',
                'jenis_kelamin' => 'L',
            ]
        );

        if ($calonSiswa->trashed()) {
            $calonSiswa->restore();
        }

        if ($calonSiswa->user_id !== $simulasiUser->id) {
            $calonSiswa->update(['user_id' => $simulasiUser->id]);
        }

        // Hubungkan jurusan & gelombang contoh jika belum ada
        if (! $calonSiswa->jurusan_id && ($jurusan = MasterJurusan::first())) {
            $calonSiswa->jurusan_id = $jurusan->id;
        }
        if (! $calonSiswa->gelombang_id && ($gelombang = MasterGelombang::first())) {
            $calonSiswa->gelombang_id = $gelombang->id;
        }
        $calonSiswa->save();

        // 3. Pastikan Jadwal Ujian Selalu Terbuka
        TpaJadwalSiswa::updateOrCreate(
            ['calon_siswa_id' => $calonSiswa->id],
            [
                'waktu_mulai' => now()->subDay(),
                'waktu_selesai' => now()->addDays(365),
                'is_diizinkan' => true,
                'catatan' => 'Sesi uji coba / simulasi mandiri Administrator',
            ]
        );

        // 4. Reset jika diminta, atau jika sesi lama sudah selesai/didiskualifikasi
        $shouldReset = $request->boolean('reset', false);
        $sesi = TpaSesiSiswa::where('calon_siswa_id', $calonSiswa->id)->first();

        if ($shouldReset || ! $sesi || in_array($sesi->status, ['selesai', 'didiskualifikasi'])) {
            $this->resetSimulasiData($calonSiswa);
        }

        // 5. Login sebagai User Simulasi & Pasang Session State
        $adminId = $admin->id;
        Auth::login($simulasiUser);

        session()->put('impersonate_admin_id', $adminId);
        session()->put('is_tpa_simulation', true);
        session()->put('impersonated_calon_siswa_id', $calonSiswa->id);

        $target = $request->input('target', 'ruang-tunggu');

        if ($target === 'ujian') {
            $examService = app(TpaExamService::class);
            $sesiAktif = $examService->getOrCreateSesi($calonSiswa);
            if ($sesiAktif->status === 'belum_mulai') {
                $examService->mulaiBabak($sesiAktif, 'literasi');
            }

            return redirect()->route('tpa.ujian')
                ->with('info', 'Anda masuk dalam Mode Simulasi Ujian TPA. Anda langsung berada di lembar soal calon murid.');
        }

        return redirect()->route('tpa.ruang-tunggu')
            ->with('info', 'Anda masuk dalam Mode Simulasi Ujian TPA. Anda berada di Ruang Tunggu calon murid.');
    }

    /**
     * Reset sesi simulasi (soal diacak ulang dan jawaban dikosongkan).
     */
    public function reset(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $calonSiswa = $user?->calonSiswa;

        if ($calonSiswa && session('is_tpa_simulation')) {
            $this->resetSimulasiData($calonSiswa);

            $target = $request->input('target', 'ujian');

            if ($target === 'ruang-tunggu') {
                return redirect()->route('tpa.ruang-tunggu')
                    ->with('info', 'Simulasi telah direset. Anda kembali ke Ruang Tunggu.');
            }

            $examService = app(TpaExamService::class);
            $sesiAktif = $examService->getOrCreateSesi($calonSiswa);
            $examService->mulaiBabak($sesiAktif, 'literasi');

            return redirect()->route('tpa.ujian')
                ->with('info', 'Simulasi ujian berhasil direset. Butir soal telah diacak kembali dari nomor 1.');
        }

        return redirect()->back();
    }

    /**
     * Keluar dari Mode Simulasi dan kembali ke akun Administrator.
     */
    public function exit(Request $request): RedirectResponse
    {
        $adminId = session('impersonate_admin_id');
        session()->forget(['impersonate_admin_id', 'is_tpa_simulation', 'impersonated_calon_siswa_id']);

        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin && $admin->isAdmin()) {
                Auth::login($admin);

                return redirect()->route('admin.tpa.pengaturan.index')
                    ->with('success', 'Sesi coba ujian selesai. Anda telah kembali ke akun Administrator.');
            }
        }

        Auth::logout();
        return redirect()->route('login')->with('info', 'Sesi simulasi telah berakhir. Silakan login kembali.');
    }

    /**
     * Hapus sesi dan jawaban lama untuk simulasi.
     */
    protected function resetSimulasiData(CalonSiswa $calonSiswa): void
    {
        $sesi = TpaSesiSiswa::where('calon_siswa_id', $calonSiswa->id)->first();
        if ($sesi) {
            TpaJawabanSiswa::where('tpa_sesi_siswa_id', $sesi->id)->delete();
            $sesi->delete();
        }
    }
}
