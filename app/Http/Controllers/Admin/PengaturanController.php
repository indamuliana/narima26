<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterGelombang;
use App\Models\PengaturanSpmb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman utama Pengaturan SPMB (Tab Gelombang & Tab Pengaturan Umum).
     */
    public function index(Request $request): View
    {
        $gelombangList = MasterGelombang::withCount('calonSiswa')
            ->orderBy('id')
            ->get();

        $pengaturan = PengaturanSpmb::getSettings();
        $activeTab = $request->input('tab', 'gelombang');

        return view('admin.pengaturan.index', compact('gelombangList', 'pengaturan', 'activeTab'));
    }

    /**
     * Simpan pembaruan Pengaturan Umum SPMB (Tahun Ajaran, Status Buka Pendaftaran, WhatsApp CS).
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'is_pendaftaran_buka' => ['nullable', 'boolean'],
            'pesan_penutupan' => ['nullable', 'string', 'max:1000'],
            'wa_cs_reguler' => ['required', 'string', 'max:25'],
            'wa_cs_unggulan' => ['required', 'string', 'max:25'],
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi (misal: 2027/2028).',
            'wa_cs_reguler.required' => 'Nomor WhatsApp CS Reguler wajib diisi.',
            'wa_cs_unggulan.required' => 'Nomor WhatsApp CS Unggulan wajib diisi.',
        ]);

        $pengaturan = PengaturanSpmb::getSettings();
        $pengaturan->update([
            'tahun_ajaran' => trim($validated['tahun_ajaran']),
            'is_pendaftaran_buka' => $request->boolean('is_pendaftaran_buka'),
            'pesan_penutupan' => $validated['pesan_penutupan'] ? trim($validated['pesan_penutupan']) : null,
            'wa_cs_reguler' => preg_replace('/[^0-9]/', '', $validated['wa_cs_reguler']),
            'wa_cs_unggulan' => preg_replace('/[^0-9]/', '', $validated['wa_cs_unggulan']),
        ]);

        activity('master_settings')
            ->causedBy(auth()->user())
            ->log("Memperbarui Pengaturan Umum SPMB (Tahun Ajaran: {$pengaturan->tahun_ajaran}, Status Pendaftaran: " . ($pengaturan->is_pendaftaran_buka ? 'BUKA' : 'TUTUP') . ')');

        return redirect()->route('admin.pengaturan.index', ['tab' => 'umum'])
            ->with('success', 'Pengaturan Umum SPMB berhasil disimpan.');
    }

    /**
     * Tambah Gelombang Pendaftaran baru.
     */
    public function storeGelombang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:master_gelombang,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['required', 'date', 'after_or_equal:periode_mulai'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'kode.required' => 'Kode gelombang wajib diisi (contoh: GEL4).',
            'kode.unique' => 'Kode gelombang sudah terdaftar.',
            'nama.required' => 'Nama gelombang wajib diisi.',
            'periode_mulai.required' => 'Tanggal periode mulai wajib diisi.',
            'periode_selesai.required' => 'Tanggal periode selesai wajib diisi.',
            'periode_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $isAktif = $request->boolean('aktif');

        // Aturan Satu Gelombang Aktif: jika gelombang baru diaktifkan, nonaktifkan yang lain
        if ($isAktif) {
            MasterGelombang::where('aktif', true)->update(['aktif' => false]);
        }

        $gelombang = MasterGelombang::create([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama' => trim($validated['nama']),
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'],
            'aktif' => $isAktif,
        ]);

        activity('master_data')
            ->performedOn($gelombang)
            ->causedBy(auth()->user())
            ->log("Menambahkan gelombang baru: {$gelombang->nama} ({$gelombang->kode})");

        return redirect()->route('admin.pengaturan.index', ['tab' => 'gelombang'])
            ->with('success', "Gelombang pendaftaran {$gelombang->nama} berhasil ditambahkan.");
    }

    /**
     * Perbarui data Gelombang Pendaftaran.
     */
    public function updateGelombang(Request $request, MasterGelombang $gelombang): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:master_gelombang,kode,' . $gelombang->id],
            'nama' => ['required', 'string', 'max:100'],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['required', 'date', 'after_or_equal:periode_mulai'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'kode.required' => 'Kode gelombang wajib diisi.',
            'kode.unique' => 'Kode gelombang sudah terdaftar.',
            'nama.required' => 'Nama gelombang wajib diisi.',
            'periode_mulai.required' => 'Tanggal periode mulai wajib diisi.',
            'periode_selesai.required' => 'Tanggal periode selesai wajib diisi.',
            'periode_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $isAktif = $request->boolean('aktif');

        if ($isAktif && ! $gelombang->aktif) {
            MasterGelombang::where('id', '!=', $gelombang->id)->where('aktif', true)->update(['aktif' => false]);
        }

        $gelombang->update([
            'kode' => strtoupper(trim($validated['kode'])),
            'nama' => trim($validated['nama']),
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'],
            'aktif' => $isAktif,
        ]);

        activity('master_data')
            ->performedOn($gelombang)
            ->causedBy(auth()->user())
            ->log("Memperbarui data gelombang: {$gelombang->nama} ({$gelombang->kode})");

        return redirect()->route('admin.pengaturan.index', ['tab' => 'gelombang'])
            ->with('success', "Data {$gelombang->nama} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif gelombang (Satu gelombang aktif).
     */
    public function toggleGelombang(MasterGelombang $gelombang): RedirectResponse
    {
        if ($gelombang->aktif) {
            $gelombang->aktif = false;
            $gelombang->save();
            $msg = "{$gelombang->nama} telah dinonaktifkan.";
        } else {
            // Nonaktifkan semua gelombang lain terlebih dahulu
            MasterGelombang::where('aktif', true)->update(['aktif' => false]);
            $gelombang->aktif = true;
            $gelombang->save();
            $msg = "{$gelombang->nama} berhasil diaktifkan sebagai gelombang pendaftaran saat ini.";
        }

        activity('master_data')
            ->performedOn($gelombang)
            ->causedBy(auth()->user())
            ->log("Mengubah status aktif gelombang {$gelombang->nama}: " . ($gelombang->aktif ? 'AKTIF' : 'NONAKTIF'));

        return redirect()->route('admin.pengaturan.index', ['tab' => 'gelombang'])
            ->with('success', $msg);
    }

    /**
     * Hapus gelombang (Dicegah bila sudah ada pendaftar).
     */
    public function destroyGelombang(MasterGelombang $gelombang): RedirectResponse
    {
        $countPendaftar = $gelombang->calonSiswa()->count();

        if ($countPendaftar > 0) {
            return redirect()->route('admin.pengaturan.index', ['tab' => 'gelombang'])
                ->with('error', "Gelombang {$gelombang->nama} tidak dapat dihapus karena sudah memiliki {$countPendaftar} calon murid yang mendaftar. Silakan nonaktifkan status gelombang ini jika tidak digunakan.");
        }

        $nama = $gelombang->nama;
        $gelombang->delete();

        activity('master_data')
            ->causedBy(auth()->user())
            ->log("Menghapus data gelombang: {$nama}");

        return redirect()->route('admin.pengaturan.index', ['tab' => 'gelombang'])
            ->with('success', "Gelombang {$nama} berhasil dihapus.");
    }
}
