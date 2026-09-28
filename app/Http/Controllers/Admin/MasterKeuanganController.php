<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\MasterProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterKeuanganController extends Controller
{
    /**
     * Display listing of master fees and discount policies.
     */
    public function index(): View
    {
        $biayaList = MasterBiaya::with(['program', 'gelombang'])
            ->orderBy('kategori')
            ->orderBy('id')
            ->get();

        $diskonList = Diskon::with(['calonSiswa', 'diberikanOleh', 'disetujuiOleh'])
            ->latest('id')
            ->take(30)
            ->get();

        $programList = MasterProgram::aktif()->get();
        $gelombangList = MasterGelombang::orderBy('id')->get();

        return view('admin.keuangan.index', compact('biayaList', 'diskonList', 'programList', 'gelombangList'));
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
            'program_id' => ['nullable', 'exists:master_program,id'],
            'gelombang_id' => ['nullable', 'exists:master_gelombang,id'],
            'wajib' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['wajib'] = $request->has('wajib');
        $validated['aktif'] = true;

        $biaya = MasterBiaya::create($validated);

        activity('master_finance')
            ->performedOn($biaya)
            ->causedBy(auth()->user())
            ->log("Admin menambahkan tarif master biaya: {$biaya->nama_biaya} (Rp " . number_format($biaya->nominal, 0, ',', '.') . ")");

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Tarif biaya {$biaya->nama_biaya} berhasil ditambahkan ke master keuangan.");
    }

    /**
     * Update existing master fee.
     */
    public function update(Request $request, MasterBiaya $biaya): RedirectResponse
    {
        $validated = $request->validate([
            'nama_biaya' => ['required', 'string', 'max:150'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'kategori' => ['required', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $biaya->update($validated);

        activity('master_finance')
            ->performedOn($biaya)
            ->causedBy(auth()->user())
            ->log("Admin memperbarui tarif master biaya: {$biaya->nama_biaya}");

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Tarif master biaya {$biaya->nama_biaya} berhasil diperbarui.");
    }

    /**
     * Toggle active status of master fee.
     */
    public function toggle(MasterBiaya $biaya): RedirectResponse
    {
        $biaya->aktif = ! $biaya->aktif;
        $biaya->save();

        $status = $biaya->aktif ? 'diaktifkan' : 'dinonaktifkan';

        activity('master_finance')
            ->performedOn($biaya)
            ->causedBy(auth()->user())
            ->log("Admin mengubah status master biaya {$biaya->nama_biaya} menjadi {$status}");

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Komponen biaya {$biaya->nama_biaya} berhasil {$status}.");
    }
}
