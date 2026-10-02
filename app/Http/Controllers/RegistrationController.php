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
use Illuminate\Http\JsonResponse;
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
        
        $selectedSekolah = null;
        if (old('asal_sekolah_id') && old('asal_sekolah_id') !== 'lainnya') {
            $selectedSekolah = MasterSekolahAsal::find(old('asal_sekolah_id'));
        }

        return view('pendaftaran.index', compact('programs', 'jurusans', 'gelombangAktif', 'selectedSekolah'));
    }

    /**
     * Endpoint API internal untuk mencari daftar asal sekolah.
     */
    public function searchSekolah(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        $sekolahs = MasterSekolahAsal::aktif()
            ->when($query, function ($q, $query) {
                return $q->where(function ($sub) use ($query) {
                    $sub->where('nama_sekolah', 'like', "%{$query}%")
                        ->orWhere('npsn', 'like', "%{$query}%");
                });
            })
            ->orderBy('nama_sekolah')
            ->limit(50)
            ->get(['id', 'npsn', 'nama_sekolah', 'kokab']);

        return response()->json($sekolahs);
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
            'program_nama' => $result['calon_siswa']->program?->nama_program ?? $result['calon_siswa']->program?->nama ?? '',
        ]);

        return redirect()->route('pendaftaran.sukses', $result['calon_siswa']->nomor_pendaftaran);
    }

    /**
     * Tampilkan halaman konfirmasi pendaftaran berhasil beserta informasi akun.
     */
    public function sukses(string $nomorPendaftaran): View
    {
        $calonSiswa = CalonSiswa::where('nomor_pendaftaran', $nomorPendaftaran)
            ->with(['user', 'program', 'jurusan', 'jurusan2', 'gelombang', 'pembayaranSeleksi'])
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
