<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TpaPengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TpaPengaturanController extends Controller
{
    /**
     * Tampilkan form pengaturan umum TPA.
     */
    public function index(): View
    {
        $pengaturan = TpaPengaturan::getSettings();
        return view('admin.tpa.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Simpan pembaruan konfigurasi TPA.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'durasi_literasi_menit' => ['required', 'integer', 'min:5', 'max:240'],
            'durasi_numerasi_menit' => ['required', 'integer', 'min:5', 'max:240'],
            'max_pelanggaran' => ['required', 'integer', 'min:1', 'max:20'],
            'petunjuk_literasi' => ['nullable', 'string'],
            'petunjuk_numerasi' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $pengaturan = TpaPengaturan::getSettings();
        $pengaturan->update([
            'durasi_literasi_menit' => $validated['durasi_literasi_menit'],
            'durasi_numerasi_menit' => $validated['durasi_numerasi_menit'],
            'max_pelanggaran' => $validated['max_pelanggaran'],
            'petunjuk_literasi' => $validated['petunjuk_literasi'],
            'petunjuk_numerasi' => $validated['petunjuk_numerasi'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.tpa.pengaturan.index')
            ->with('success', 'Konfigurasi TPA berhasil diperbarui.');
    }
}
