<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\MasterBiaya;
use App\Models\MasterGelombang;
use App\Models\MasterProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterBiayaController extends Controller
{
    /**
     * Display list of all master fees with optional filtering.
     */
    public function index(Request $request): View
    {
        $q        = $request->input('q');
        $kategori = $request->input('kategori');
        $status   = $request->input('status');

        $query = MasterBiaya::with(['program', 'gelombang'])
            ->orderBy('kategori')
            ->orderBy('id');

        if (! empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_biaya', 'like', "%{$q}%")
                    ->orWhere('kode_biaya', 'like', "%{$q}%");
            });
        }

        if (! empty($kategori)) {
            $query->where('kategori', $kategori);
        }

        if ($status !== null && $status !== '') {
            $query->where('aktif', (bool) $status);
        }

        $biayaList   = $query->get();
        $programList = MasterProgram::orderBy('nama')->get();
        $gelombangList = MasterGelombang::orderBy('nama')->get();

        return view('bendahara.master-biaya.index', compact(
            'biayaList', 'programList', 'gelombangList', 'q', 'kategori', 'status'
        ));
    }

    /**
     * Store new master fee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_biaya'   => ['required', 'string', 'max:50', 'unique:master_biaya,kode_biaya'],
            'nama_biaya'   => ['required', 'string', 'max:150'],
            'kategori'     => ['required', 'string', 'max:50'],
            'program_id'   => ['nullable', 'exists:master_program,id'],
            'gelombang_id' => ['nullable', 'exists:master_gelombang,id'],
            'nominal'      => ['required', 'numeric', 'min:0'],
            'wajib'        => ['nullable', 'boolean'],
            'keterangan'   => ['nullable', 'string', 'max:500'],
        ]);

        $validated['wajib'] = $request->has('wajib');
        $validated['aktif'] = true;

        MasterBiaya::create($validated);

        return redirect()->route('bendahara.master-biaya.index')
            ->with('success', "Komponen biaya {$validated['nama_biaya']} berhasil ditambahkan.");
    }

    /**
     * Update existing master fee (including kategori, program, gelombang).
     */
    public function update(Request $request, MasterBiaya $masterBiaya): RedirectResponse
    {
        $validated = $request->validate([
            'nama_biaya'   => ['required', 'string', 'max:150'],
            'kategori'     => ['sometimes', 'string', 'max:50'],
            'program_id'   => ['nullable', 'exists:master_program,id'],
            'gelombang_id' => ['nullable', 'exists:master_gelombang,id'],
            'nominal'      => ['required', 'numeric', 'min:0'],
            'keterangan'   => ['nullable', 'string', 'max:500'],
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
