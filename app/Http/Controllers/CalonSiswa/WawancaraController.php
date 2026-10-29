<?php

namespace App\Http\Controllers\CalonSiswa;

use App\Enums\SpmbStatus;
use App\Http\Controllers\Controller;
use App\Services\PhoneNumberService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WawancaraController extends Controller
{
    public function __construct(
        protected PhoneNumberService $phoneService
    ) {}

    /**
     * Halaman Utama Modul Wawancara Calon Siswa.
     */
    public function index(Request $request): View
    {
        $calonSiswa = auth()->user()->calonSiswa;

        if (!$calonSiswa) {
            abort(404, 'Data pendaftaran calon siswa tidak ditemukan.');
        }

        $calonSiswa->loadMissing([
            'program',
            'jurusan',
            'gelombang',
            'kesepahaman',
            'latestWawancara.pewawancara',
        ]);

        $statusVal = $calonSiswa->status_spmb?->value ?? (is_string($calonSiswa->status_spmb) ? $calonSiswa->status_spmb : 'REGISTRASI');

        // Pengecekan 1: Kelengkapan data
        $isDataComplete = ($calonSiswa->status_data === 'LENGKAP') || in_array($statusVal, [
            SpmbStatus::DATA_LENGKAP->value,
            SpmbStatus::MENUNGGU_WAWANCARA->value,
            SpmbStatus::SUDAH_DIWAWANCARA->value,
            SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ], true);

        // Pengecekan 2: Persetujuan lembar kesepahaman (EULA)
        $isKesepahamanAgreed = $calonSiswa->relationLoaded('kesepahaman')
            ? $calonSiswa->kesepahaman->where('setuju', true)->isNotEmpty()
            : $calonSiswa->kesepahaman()->where('setuju', true)->exists();

        // Prasyarat terpenuhi jika data lengkap dan kesepahaman sudah disetujui
        $isPrerequisiteMet = $isDataComplete && $isKesepahamanAgreed;

        // Ambil data wawancara terbaru
        $wawancara = $calonSiswa->latestWawancara;
        $pewawancara = $wawancara?->pewawancara;

        // Status penjadwalan
        $isScheduled = $isPrerequisiteMet && $wawancara && !empty($wawancara->pewawancara_id);
        $isFinished = in_array($statusVal, [
            SpmbStatus::SUDAH_DIWAWANCARA->value,
            SpmbStatus::MENUNGGU_KEPUTUSAN->value,
            SpmbStatus::DITERIMA->value,
            SpmbStatus::MENUNGGU_DAFTAR_ULANG->value,
            SpmbStatus::DAFTAR_ULANG_DIVERIFIKASI->value,
            SpmbStatus::RESMI_TERDAFTAR->value,
        ], true) || ($wawancara && $wawancara->status === 'SELESAI');

        // Format tanggal wawancara
        $tanggalWawancaraFormatted = null;
        if ($wawancara?->tanggal_wawancara) {
            $carbonDate = Carbon::parse($wawancara->tanggal_wawancara);
            $tanggalWawancaraFormatted = $carbonDate->locale('id')->isoFormat('dddd, D MMMM Y');
        }

        // WhatsApp Click to Chat Guru Pewawancara
        $waUrl = null;
        $pewawancaraPhone = $pewawancara?->phone;
        if ($pewawancaraPhone && $this->phoneService->isValid($pewawancaraPhone)) {
            $namaSiswa = $calonSiswa->nama_lengkap ?? 'Calon Siswa';
            $noPendaftaran = $calonSiswa->nomor_pendaftaran ?? '-';
            $namaGuru = $pewawancara?->name ?? 'Bapak/Ibu Pewawancara';
            $tglStr = $tanggalWawancaraFormatted ? " pada {$tanggalWawancaraFormatted}" : "";
            
            $msg = "Assalamu'alaikum Wr. Wb. Yth. Bapak/Ibu {$namaGuru}, saya {$namaSiswa} (No. Pendaftaran: {$noPendaftaran}) calon siswa SMK Wikrama 1 Garut ingin mengonfirmasi terkait jadwal sesi wawancara kami{$tglStr}. Terima kasih.";
            $waUrl = $this->phoneService->toWhatsappUrl($pewawancaraPhone, $msg);
        }

        return view('calon-siswa.wawancara.index', compact(
            'calonSiswa',
            'isDataComplete',
            'isKesepahamanAgreed',
            'isPrerequisiteMet',
            'wawancara',
            'pewawancara',
            'isScheduled',
            'isFinished',
            'tanggalWawancaraFormatted',
            'pewawancaraPhone',
            'waUrl'
        ));
    }
}
