<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterCalonSiswaRequest;
use App\Models\CalonSiswa;
use App\Models\MasterGelombang;
use App\Models\MasterJurusan;
use App\Models\MasterProgram;
use App\Models\MasterSekolahAsal;
use App\Services\PdfService;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    /**
     * Tampilkan formulir pendaftaran calon siswa baru.
     */
    public function create(): View
    {
        $programs = MasterProgram::aktif()->get();
        $jurusans = MasterJurusan::aktif()->get();
        $gelombangAktif = MasterGelombang::aktif()->first();
        $sekolahAsal = MasterSekolahAsal::aktif()->orderBy('nama_sekolah')->get();

        return view('pendaftaran.index', compact('programs', 'jurusans', 'gelombangAktif', 'sekolahAsal'));
    }

    /**
     * Proses penyimpanan registrasi calon siswa dan pembuatan akun.
     */
    public function store(RegisterCalonSiswaRequest $request): RedirectResponse
    {
        $result = $this->registrationService->register($request->validated());

        // Simpan info kredensial ke flash session agar dapat ditampilkan sekali di halaman sukses
        session()->flash('registration_result', [
            'nomor_pendaftaran' => $result['calon_siswa']->nomor_pendaftaran,
            'username' => $result['user']->username,
            'password_plain' => $result['password_plain'],
            'nama_lengkap' => $result['calon_siswa']->nama_lengkap,
            'email' => $result['user']->email,
            'nominal_tagihan' => $result['pembayaran_seleksi']->nominal_tagihan,
        ]);

        return redirect()->route('pendaftaran.sukses', $result['calon_siswa']->nomor_pendaftaran);
    }

    /**
     * Tampilkan halaman konfirmasi pendaftaran berhasil beserta informasi akun.
     */
    public function sukses(string $nomorPendaftaran): View
    {
        $calonSiswa = CalonSiswa::where('nomor_pendaftaran', $nomorPendaftaran)
            ->with(['user', 'program', 'jurusan', 'gelombang', 'pembayaranSeleksi'])
            ->firstOrFail();

        $sessionData = session('registration_result');

        return view('pendaftaran.sukses', compact('calonSiswa', 'sessionData'));
    }

    /**
     * Cetak Kartu Tanda Peserta / Bukti Registrasi SPMB PDF ber-KOP resmi.
     */
    public function cetakAkun(string $nomorPendaftaran, PdfService $pdfService): Response
    {
        $calonSiswa = CalonSiswa::where('nomor_pendaftaran', $nomorPendaftaran)->firstOrFail();
        $pdf = $pdfService->generateKartuPendaftaran($calonSiswa);

        return $pdf->download("Kartu_Pendaftaran_{$nomorPendaftaran}.pdf");
    }

    /**
     * Login instan calon siswa setelah registrasi langsung menuju dashboard.
     */
    public function loginDirect(string $nomorPendaftaran): RedirectResponse
    {
        $calonSiswa = CalonSiswa::where('nomor_pendaftaran', $nomorPendaftaran)->firstOrFail();

        if ($calonSiswa->user && $calonSiswa->user->is_active) {
            Auth::login($calonSiswa->user);
            return redirect()->route('calon-siswa.dashboard');
        }

        return redirect()->route('login');
    }
}
