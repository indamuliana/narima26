<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPembayaranSeleksiRequest;
use App\Http\Requests\VerifyPembayaranSeleksiRequest;
use App\Models\PembayaranSeleksi;
use App\Services\PaymentVerificationService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PembayaranSeleksiController extends Controller
{
    public function __construct(
        protected PaymentVerificationService $paymentService
    ) {}

    /**
     * Tampilkan daftar seluruh transaksi pembayaran seleksi dengan filter & metrik.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');
        $query = PembayaranSeleksi::with(['calonSiswa.jurusan', 'calonSiswa.jurusan2', 'calonSiswa.program', 'verifikator'])
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

        $pembayarans = $query->paginate(15)->withQueryString();

        // Metrik Ringkasan Kas Seleksi
        $countPending = PembayaranSeleksi::where('status', 'PENDING')->count();
        $countDiverifikasi = PembayaranSeleksi::where('status', 'DIVERIFIKASI')->count();
        $countDitolak = PembayaranSeleksi::where('status', 'DITOLAK')->count();
        $totalDana = PembayaranSeleksi::where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');

        return view('bendahara.pembayaran-seleksi.index', compact(
            'pembayarans',
            'status',
            'search',
            'countPending',
            'countDiverifikasi',
            'countDitolak',
            'totalDana'
        ));
    }

    /**
     * Tampilkan detail bukti pembayaran dan formulir verifikasi.
     */
    public function show(PembayaranSeleksi $pembayaranSeleksi): View
    {
        $pembayaranSeleksi->loadMissing(['calonSiswa.program', 'calonSiswa.jurusan', 'calonSiswa.gelombang', 'verifikator']);

        return view('bendahara.pembayaran-seleksi.show', compact('pembayaranSeleksi'));
    }

    /**
     * Aksi verifikasi terima pembayaran seleksi.
     */
    public function verify(VerifyPembayaranSeleksiRequest $request, PembayaranSeleksi $pembayaranSeleksi): RedirectResponse
    {
        $nominal = $request->filled('nominal_diterima') ? (float) $request->nominal_diterima : null;
        $this->paymentService->verifySelectionPayment(
            pembayaran: $pembayaranSeleksi,
            bendahara: auth()->user(),
            nominalDiterima: $nominal,
            catatan: $request->catatan
        );

        return redirect()->route('bendahara.pembayaran-seleksi.index')
            ->with('success', "Pembayaran seleksi calon siswa {$pembayaranSeleksi->calonSiswa->nama_lengkap} berhasil diverifikasi!");
    }

    /**
     * Aksi tolak pembayaran seleksi dengan catatan alasan.
     */
    public function reject(RejectPembayaranSeleksiRequest $request, PembayaranSeleksi $pembayaranSeleksi): RedirectResponse
    {
        $this->paymentService->rejectSelectionPayment(
            pembayaran: $pembayaranSeleksi,
            bendahara: auth()->user(),
            alasan: $request->alasan
        );

        return redirect()->route('bendahara.pembayaran-seleksi.index')
            ->with('warning', "Pembayaran seleksi calon siswa {$pembayaranSeleksi->calonSiswa->nama_lengkap} ditolak. Calon siswa akan diminta unggah ulang bukti transfer.");
    }

    /**
     * Cetak Kwitansi Resmi Pembayaran Seleksi PDF (ber-KOP resmi).
     */
    public function cetakKwitansi(PembayaranSeleksi $pembayaranSeleksi, PdfService $pdfService): Response
    {
        if ($pembayaranSeleksi->status !== 'DIVERIFIKASI') {
            abort(403, 'Kwitansi hanya dapat dicetak setelah pembayaran diverifikasi.');
        }

        $pdf = $pdfService->generateBuktiPembayaran($pembayaranSeleksi, 'Biaya Pendaftaran Seleksi');

        return $pdf->download("Kwitansi_Seleksi_{$pembayaranSeleksi->calonSiswa->nomor_pendaftaran}.pdf");
    }
}
