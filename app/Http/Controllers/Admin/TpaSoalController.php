<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TpaSoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TpaSoalController extends Controller
{
    /**
     * Direktori Bank Soal TPA (Literasi & Numerasi).
     */
    public function index(Request $request): View
    {
        $babak = $request->input('babak', 'literasi');
        $search = $request->input('q');

        $query = TpaSoal::query()->where('babak', $babak);

        if ($search) {
            $query->where('pertanyaan', 'like', "%{$search}%");
        }

        $soalList = $query->orderBy('nomor_urut')->orderBy('id')->paginate(20)->withQueryString();

        $countLiterasi = TpaSoal::literasi()->count();
        $countNumerasi = TpaSoal::numerasi()->count();

        return view('admin.tpa.soal.index', compact('soalList', 'babak', 'search', 'countLiterasi', 'countNumerasi'));
    }

    /**
     * Form Tambah Soal Baru.
     */
    public function create(Request $request): View
    {
        $defaultBabak = $request->input('babak', 'literasi');
        $nextNomor = (TpaSoal::where('babak', $defaultBabak)->max('nomor_urut') ?? 0) + 1;

        return view('admin.tpa.soal.form', [
            'soal' => new TpaSoal(['babak' => $defaultBabak, 'nomor_urut' => $nextNomor, 'bobot' => 1]),
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan Soal Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'babak' => ['required', 'in:literasi,numerasi'],
            'nomor_urut' => ['required', 'integer', 'min:1'],
            'pertanyaan' => ['required', 'string'],
            'bobot' => ['required', 'integer', 'min:1', 'max:100'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'kunci_jawaban' => ['required', 'string', 'in:A,B,C,D,E'],
            'opsi_keys' => ['required', 'array', 'min:2', 'max:5'],
            'opsi_keys.*' => ['required', 'string', 'in:A,B,C,D,E'],
            'opsi_texts' => ['required', 'array', 'min:2', 'max:5'],
            'opsi_texts.*' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('tpa-soal', 'public');
        }

        // Format opsi jawaban ke struktur JSON
        $opsiJawaban = [];
        foreach ($validated['opsi_keys'] as $idx => $key) {
            $opsiJawaban[] = [
                'key' => strtoupper($key),
                'text' => trim($validated['opsi_texts'][$idx] ?? ''),
            ];
        }

        TpaSoal::create([
            'babak' => $validated['babak'],
            'nomor_urut' => $validated['nomor_urut'],
            'pertanyaan' => $validated['pertanyaan'],
            'gambar_path' => $gambarPath,
            'bobot' => $validated['bobot'],
            'kunci_jawaban' => strtoupper($validated['kunci_jawaban']),
            'opsi_jawaban' => $opsiJawaban,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.tpa.soal.index', ['babak' => $validated['babak']])
            ->with('success', 'Butir soal TPA berhasil ditambahkan.');
    }

    /**
     * Form Edit Soal.
     */
    public function edit(TpaSoal $soal): View
    {
        return view('admin.tpa.soal.form', [
            'soal' => $soal,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui Butir Soal.
     */
    public function update(Request $request, TpaSoal $soal): RedirectResponse
    {
        $validated = $request->validate([
            'babak' => ['required', 'in:literasi,numerasi'],
            'nomor_urut' => ['required', 'integer', 'min:1'],
            'pertanyaan' => ['required', 'string'],
            'bobot' => ['required', 'integer', 'min:1', 'max:100'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'hapus_gambar' => ['nullable', 'boolean'],
            'kunci_jawaban' => ['required', 'string', 'in:A,B,C,D,E'],
            'opsi_keys' => ['required', 'array', 'min:2', 'max:5'],
            'opsi_keys.*' => ['required', 'string', 'in:A,B,C,D,E'],
            'opsi_texts' => ['required', 'array', 'min:2', 'max:5'],
            'opsi_texts.*' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $gambarPath = $soal->gambar_path;

        if ($request->boolean('hapus_gambar') && $gambarPath) {
            Storage::disk('public')->delete($gambarPath);
            $gambarPath = null;
        }

        if ($request->hasFile('gambar')) {
            if ($gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }
            $gambarPath = $request->file('gambar')->store('tpa-soal', 'public');
        }

        $opsiJawaban = [];
        foreach ($validated['opsi_keys'] as $idx => $key) {
            $opsiJawaban[] = [
                'key' => strtoupper($key),
                'text' => trim($validated['opsi_texts'][$idx] ?? ''),
            ];
        }

        $soal->update([
            'babak' => $validated['babak'],
            'nomor_urut' => $validated['nomor_urut'],
            'pertanyaan' => $validated['pertanyaan'],
            'gambar_path' => $gambarPath,
            'bobot' => $validated['bobot'],
            'kunci_jawaban' => strtoupper($validated['kunci_jawaban']),
            'opsi_jawaban' => $opsiJawaban,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.tpa.soal.index', ['babak' => $soal->babak])
            ->with('success', 'Butir soal TPA berhasil diperbarui.');
    }

    /**
     * Hapus Butir Soal.
     */
    public function destroy(TpaSoal $soal): RedirectResponse
    {
        $babak = $soal->babak;

        if ($soal->gambar_path) {
            Storage::disk('public')->delete($soal->gambar_path);
        }

        $soal->delete();

        return redirect()->route('admin.tpa.soal.index', ['babak' => $babak])
            ->with('success', 'Butir soal TPA berhasil dihapus.');
    }

    /**
     * Toggle status aktif/nonaktif soal.
     */
    public function toggle(TpaSoal $soal): RedirectResponse
    {
        $soal->update(['is_active' => ! $soal->is_active]);

        return back()->with('success', 'Status butir soal berhasil diubah.');
    }
}
