<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranDaftarUlang;
use App\Models\PembayaranSeleksi;
use App\Services\PaymentVerificationService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function __construct(
        protected PaymentVerificationService $paymentService,
        protected PdfService $pdfService
    ) {}

    /**
     * Display listing of candidate initial selection payments.
     */
    public function seleksi(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $query = PembayaranSeleksi::with(['calonSiswa.jurusan', 'calonSiswa.program', 'verifikator'])
            ->latest('id');

        if (!empty($status) && in_array(strtoupper($status), ['PENDING', 'DIVERIFIKASI', 'DITOLAK'])) {
            $query->where('status', strtoupper($status));
        }

        if (!empty($search)) {
            $query->whereHas('calonSiswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%");
            });
        }

        $pembayarans = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => PembayaranSeleksi::where('status', 'PENDING')->count(),
            'diverifikasi' => PembayaranSeleksi::where('status', 'DIVERIFIKASI')->count(),
            'ditolak' => PembayaranSeleksi::where('status', 'DITOLAK')->count(),
            'total_masuk' => (float) PembayaranSeleksi::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar'),
        ];

        return view('admin.pembayaran.seleksi', compact('pembayarans', 'status', 'search', 'stats'));
    }

    /**
     * Display listing of candidate re-registration payments.
     */
    public function daftarUlang(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $query = PembayaranDaftarUlang::with(['calonSiswa.jurusan', 'tagihan', 'verifikator'])
            ->latest('id');

        if (!empty($status) && in_array(strtoupper($status), ['PENDING', 'DIVERIFIKASI', 'DITOLAK'])) {
            $query->where('status', strtoupper($status));
        }

        if (!empty($search)) {
            $query->whereHas('calonSiswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pendaftaran', 'like', "%{$search}%");
            });
        }

        $pembayarans = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => PembayaranDaftarUlang::where('status', 'PENDING')->count(),
            'diverifikasi' => PembayaranDaftarUlang::where('status', 'DIVERIFIKASI')->count(),
            'ditolak' => PembayaranDaftarUlang::where('status', 'DITOLAK')->count(),
            'total_masuk' => (float) PembayaranDaftarUlang::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar'),
        ];

        return view('admin.pembayaran.daftar-ulang', compact('pembayarans', 'status', 'search', 'stats'));
    }

    /**
     * Verify initial selection fee.
     */
    public function verifySeleksi(Request $request, PembayaranSeleksi $pembayaranSeleksi): RedirectResponse
    {
        $nominal = $request->input('nominal_dibayar', $pembayaranSeleksi->nominal_tagihan);

        $this->paymentService->verifySelectionPayment(
            $pembayaranSeleksi,
            auth()->user(),
            (float) $nominal
        );

        return back()->with('success', "Pembayaran seleksi calon siswa {$pembayaranSeleksi->calonSiswa?->nama_lengkap} berhasil diverifikasi.");
    }

    /**
     * Reject initial selection fee.
     */
    public function rejectSeleksi(Request $request, PembayaranSeleksi $pembayaranSeleksi): RedirectResponse
    {
        $catatan = $request->input('catatan_bendahara', 'Bukti transfer tidak valid atau tidak terbaca.');

        $this->paymentService->rejectSelectionPayment(
            $pembayaranSeleksi,
            auth()->user(),
            $catatan
        );

        return back()->with('success', "Pembayaran seleksi ditolak.");
    }
}
