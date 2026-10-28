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
     * Dapatkan atau buat entitas Akun Demo Siswa & Calon Siswa Demo.
     */
    public static function getOrCreateDemoStudent(): array
    {
        // 1. Akun User Demo Siswa
        $demoUser = User::whereIn('username', ['demo.siswa', 'simulasi.tpa'])
            ->orWhere('email', 'demo.siswa@wikrama.sch.id')
            ->first();

        if (! $demoUser) {
            $demoUser = User::create([
                'name' => 'Calon Murid (Akun Demo)',
                'username' => 'demo.siswa',
                'email' => 'demo.siswa@wikrama.sch.id',
                'password' => Hash::make('demo12345'),
                'role' => User::ROLE_CALON_SISWA,
                'is_active' => true,
            ]);
        } else {
            $demoUser->update([
                'name' => 'Calon Murid (Akun Demo)',
                'username' => 'demo.siswa',
                'email' => 'demo.siswa@wikrama.sch.id',
                'password' => Hash::make('demo12345'),
                'role' => User::ROLE_CALON_SISWA,
                'is_active' => true,
            ]);
        }

        // 2. Data Calon Siswa Demo
        $calonSiswa = CalonSiswa::withTrashed()
            ->whereIn('nomor_pendaftaran', ['DEMO-2026-001', 'SIM-TPA-0001'])
            ->orWhere('user_id', $demoUser->id)
            ->first();

        if (! $calonSiswa) {
            $calonSiswa = CalonSiswa::create([
                'nomor_pendaftaran' => 'DEMO-2026-001',
                'user_id' => $demoUser->id,
                'nama_lengkap' => 'Calon Murid (Akun Demo)',
                'nama_panggilan' => 'Demo',
                'nisn' => '0000000000',
                'jenis_kelamin' => 'L',
                'status_spmb' => \App\Enums\SpmbStatus::DATA_LENGKAP->value,
            ]);
        } else {
            if ($calonSiswa->trashed()) {
                $calonSiswa->restore();
            }
            $calonSiswa->update([
                'nomor_pendaftaran' => 'DEMO-2026-001',
                'user_id' => $demoUser->id,
                'nama_lengkap' => 'Calon Murid (Akun Demo)',
                'nama_panggilan' => 'Demo',
                'nisn' => '0000000000',
            ]);
        }

        // Hubungkan jurusan & gelombang contoh jika belum ada
        if (! $calonSiswa->jurusan_id && ($jurusan = MasterJurusan::first())) {
            $calonSiswa->jurusan_id = $jurusan->id;
        }
        if (! $calonSiswa->gelombang_id && ($gelombang = MasterGelombang::first())) {
            $calonSiswa->gelombang_id = $gelombang->id;
        }
        $calonSiswa->save();

        // Pastikan Jadwal Ujian Selalu Terbuka
        TpaJadwalSiswa::updateOrCreate(
            ['calon_siswa_id' => $calonSiswa->id],
            [
                'waktu_mulai' => now()->subDay(),
                'waktu_selesai' => now()->addDays(365),
                'is_diizinkan' => true,
                'catatan' => 'Sesi uji coba / simulasi mandiri Administrator',
            ]
        );

        return [$demoUser, $calonSiswa];
    }

    /**
     * Masuk langsung ke Portal Calon Siswa (Akun Demo).
     */
    public function loginDemoPortal(Request $request): RedirectResponse
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk mengakses portal demo siswa.');
        }

        [$demoUser, $calonSiswa] = self::getOrCreateDemoStudent();

        $adminId = $admin->id;
        Auth::login($demoUser);

        session()->put('impersonate_admin_id', $adminId);
        session()->put('is_demo_student', true);
        session()->put('impersonated_calon_siswa_id', $calonSiswa->id);

        return redirect()->route('calon-siswa.dashboard')
            ->with('info', 'Anda masuk ke Portal Calon Siswa menggunakan Akun Demo (Username: demo.siswa | Password: demo12345).');
    }

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

        [$simulasiUser, $calonSiswa] = self::getOrCreateDemoStudent();

        // Reset jika diminta, atau jika sesi lama sudah selesai/didiskualifikasi
        $shouldReset = $request->boolean('reset', false);
        $sesi = TpaSesiSiswa::where('calon_siswa_id', $calonSiswa->id)->first();

        if ($shouldReset || ! $sesi || in_array($sesi->status, ['selesai', 'didiskualifikasi'])) {
            $this->resetSimulasiData($calonSiswa);
        }

        // Login sebagai User Simulasi & Pasang Session State
        $adminId = $admin->id;
        Auth::login($simulasiUser);

        session()->put('impersonate_admin_id', $adminId);
        session()->put('is_tpa_simulation', true);
        session()->put('is_demo_student', true);
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
        $wasTpaSimulation = session('is_tpa_simulation', false);
        session()->forget(['impersonate_admin_id', 'is_tpa_simulation', 'is_demo_student', 'impersonated_calon_siswa_id']);

        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin && $admin->isAdmin()) {
                Auth::login($admin);

                $redirectRoute = $wasTpaSimulation ? 'admin.tpa.pengaturan.index' : 'admin.dashboard';
                return redirect()->route($redirectRoute)
                    ->with('success', 'Sesi demo / simulasi selesai. Anda telah kembali ke akun Administrator.');
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
