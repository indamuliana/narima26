<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\MasterBiaya;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterBiayaController extends Controller
{
    /**
     * Display list of all master fees.
     */
    public function index(): View
    {
        $biayaList = MasterBiaya::orderBy('kategori')->orderBy('id')->get();

        return view('bendahara.master-biaya.index', compact('biayaList'));
    }

    /**
     * Store new master fee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_biaya' => ['required', 'string', 'max:50', 'unique:master_biaya,kode_biaya'],
            'nama_biaya' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:50'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'wajib' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['wajib'] = $request->has('wajib');
        $validated['aktif'] = true;

        MasterBiaya::create($validated);

        return redirect()->route('bendahara.master-biaya.index')
            ->with('success', "Komponen biaya {$validated['nama_biaya']} berhasil ditambahkan.");
    }

    /**
     * Update existing master fee.
     */
    public function update(Request $request, MasterBiaya $masterBiaya): RedirectResponse
    {
        $validated = $request->validate([
            'nama_biaya' => ['required', 'string', 'max:150'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $masterBiaya->update($validated);

        return redirect()->route('bendahara.master-biaya.index')
            ->with('success', "Tarif master biaya {$masterBiaya->nama_biaya} berhasil diperbarui.");
    }

    /**
     * Toggle active status.
     */
    public function toggle(MasterBiaya $masterBiaya): RedirectResponse
    {
        $masterBiaya->update(['aktif' => ! $masterBiaya->aktif]);

        $statusText = $masterBiaya->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('bendahara.master-biaya.index')
            ->with('success', "Master biaya {$masterBiaya->nama_biaya} telah {$statusText}.");
    }
}
