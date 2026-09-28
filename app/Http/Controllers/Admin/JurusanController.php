<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterJurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurusanController extends Controller
{
    /**
     * Display listing of master jurusan with applicant counts.
     */
    public function index(): View
    {
        $jurusanList = MasterJurusan::withCount('calonSiswa')
            ->orderBy('kode')
            ->get();

        return view('admin.jurusan.index', compact('jurusanList'));
    }

    /**
     * Store a newly created jurusan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:master_jurusan,kode'],
            'nama' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'kode.required' => 'Kode jurusan wajib diisi.',
            'kode.unique' => 'Kode jurusan sudah terdaftar.',
            'nama.required' => 'Nama jurusan wajib diisi.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['aktif'] = true;

        $jurusan = MasterJurusan::create($validated);

        activity('master_data')
            ->performedOn($jurusan)
            ->causedBy(auth()->user())
            ->log("Menambahkan kompetensi keahlian / jurusan baru: {$jurusan->nama} ({$jurusan->kode})");

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Kompetensi keahlian {$jurusan->nama} berhasil ditambahkan.");
    }

    /**
     * Update the specified jurusan in storage.
     */
    public function update(Request $request, MasterJurusan $jurusan): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:master_jurusan,kode,' . $jurusan->id],
            'nama' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'kode.required' => 'Kode jurusan wajib diisi.',
            'kode.unique' => 'Kode jurusan sudah terdaftar.',
            'nama.required' => 'Nama jurusan wajib diisi.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));

        $jurusan->update($validated);

        activity('master_data')
            ->performedOn($jurusan)
            ->causedBy(auth()->user())
            ->log("Memperbarui kompetensi keahlian: {$jurusan->nama} ({$jurusan->kode})");

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Data jurusan {$jurusan->nama} berhasil diperbarui.");
    }

    /**
     * Toggle active status of the specified jurusan.
     */
    public function toggle(MasterJurusan $jurusan): RedirectResponse
    {
        $jurusan->aktif = ! $jurusan->aktif;
        $jurusan->save();

        $statusLabel = $jurusan->aktif ? 'diaktifkan' : 'dinonaktifkan';

        activity('master_data')
            ->performedOn($jurusan)
            ->causedBy(auth()->user())
            ->log("Status kompetensi keahlian {$jurusan->nama} telah {$statusLabel}");

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Status kompetensi keahlian {$jurusan->nama} berhasil {$statusLabel}.");
    }
}
