<?php

namespace App\Http\Controllers;

use App\Models\DokumenVerifikasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiDokumenController extends Controller
{
    /**
     * Tampilkan halaman sertifikat verifikasi keabsahan dokumen elektronik.
     */
    public function show(string $kode): View
    {
        $dokumen = DokumenVerifikasi::with([
            'calonSiswa.jurusan',
            'calonSiswa.program',
            'calonSiswa.sekolahAsal',
        ])
        ->where('kode_verifikasi', $kode)
        ->first();

        if ($dokumen) {
            // Catat audit pemindaian dokumen
            $dokumen->increment('scan_count');
            $dokumen->update(['last_scanned_at' => now()]);

            return view('public.verifikasi_dokumen', [
                'isValid' => $dokumen->is_valid,
                'dokumen' => $dokumen,
                'kode'    => $kode,
            ]);
        }

        return view('public.verifikasi_dokumen', [
            'isValid' => false,
            'dokumen' => null,
            'kode'    => $kode,
        ]);
    }
}
