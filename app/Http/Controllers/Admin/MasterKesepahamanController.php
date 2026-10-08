<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterKesepahamanController extends Controller
{
    public function index(Request $request)
    {
        $programs = \App\Models\KesepahamanProgram::with(['kelompoks' => function($q) {
            $q->orderBy('urutan');
        }, 'kelompoks.poins' => function($q) {
            $q->orderBy('urutan');
        }])->get();

        return view('admin.kesepahaman.index', compact('programs'));
    }

    public function storeKelompok(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:kesepahaman_programs,id',
            'kode' => 'required|string|max:10',
            'judul' => 'required|string',
            'urutan' => 'required|integer',
        ]);

        \App\Models\KesepahamanKelompok::create($request->all());

        return back()->with('success', 'Kelompok kesepahaman berhasil ditambahkan.');
    }

    public function updateKelompok(Request $request, $id)
    {
        $kelompok = \App\Models\KesepahamanKelompok::findOrFail($id);
        
        $request->validate([
            'kode' => 'required|string|max:10',
            'judul' => 'required|string',
            'urutan' => 'required|integer',
        ]);

        $kelompok->update($request->only('kode', 'judul', 'urutan'));

        return back()->with('success', 'Kelompok kesepahaman berhasil diperbarui.');
    }

    public function destroyKelompok($id)
    {
        \App\Models\KesepahamanKelompok::findOrFail($id)->delete();
        return back()->with('success', 'Kelompok kesepahaman berhasil dihapus.');
    }

    public function storePoin(Request $request)
    {
        $request->validate([
            'kelompok_id' => 'required|exists:kesepahaman_kelompoks,id',
            'kode_poin' => 'required|string|unique:kesepahaman_poins,kode_poin',
            'nomor' => 'required|string',
            'uraian' => 'required|string',
            'urutan' => 'required|integer',
        ]);

        \App\Models\KesepahamanPoin::create($request->all());

        return back()->with('success', 'Poin kesepahaman berhasil ditambahkan.');
    }

    public function updatePoin(Request $request, $id)
    {
        $poin = \App\Models\KesepahamanPoin::findOrFail($id);
        
        $request->validate([
            'kode_poin' => 'required|string|unique:kesepahaman_poins,kode_poin,'.$poin->id,
            'nomor' => 'required|string',
            'uraian' => 'required|string',
            'urutan' => 'required|integer',
        ]);

        $poin->update($request->only('kode_poin', 'nomor', 'uraian', 'urutan'));

        return back()->with('success', 'Poin kesepahaman berhasil diperbarui.');
    }

    public function preview($id)
    {
        $program = \App\Models\KesepahamanProgram::with(['kelompoks' => function($q) {
            $q->orderBy('urutan');
        }, 'kelompoks.poins' => function($q) {
            $q->orderBy('urutan');
        }])->findOrFail($id);

        $calonSiswa = new \App\Models\CalonSiswa([
            'nomor_pendaftaran' => 'PREVIEW-SPMB-2027',
            'nama_lengkap' => 'NAMA CALON SISWA',
            'nisn' => '0000000000',
            'asal_sekolah_lainnya' => 'SMP CONTOH 1 GARUT',
        ]);
        
        $calonSiswa->setRelation('program', $program);
        
        $orangTua = new \App\Models\DataOrangTua([
            'nama_ayah' => 'NAMA AYAH CONTOH',
            'nama_ibu' => 'NAMA IBU CONTOH',
            'no_hp_ayah' => '08123456789',
        ]);
        $calonSiswa->setRelation('dataOrangtua', $orangTua);

        $kesepahamanService = app(\App\Services\KesepahamanService::class);
        $klausulData = $kesepahamanService->getKlausulByCalonSiswa($calonSiswa);
        $kelompokList = $klausulData['kelompok'];

        $pdfService = app(\App\Services\PdfService::class);
        $pdf = $pdfService->renderPdf('pdf.kesepahaman_eula', [
            'calonSiswa' => $calonSiswa,
            'eula' => null, // no agreement yet
            'programNama' => $klausulData['program_title'],
            'tahunPelajaran' => $klausulData['tahun_pelajaran'],
            'kelompokList' => $kelompokList,
            'hideKop' => false,
            'isDraft' => true,
            'tte' => [
                'penandatanganJabatan' => 'Kepala SMK Wikrama 1 Garut',
                'penandatanganNama' => 'Kunedi, S.Si., Gr.',
                'qrCodeBase64' => '',
            ],
        ]);

        return $pdf->stream("DRAFT_EULA_{$program->kode}.pdf");
    }

    public function destroyPoin($id)
    {
        \App\Models\KesepahamanPoin::findOrFail($id)->delete();
        return back()->with('success', 'Poin kesepahaman berhasil dihapus.');
    }
}
