<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\MasterDiskon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class MasterDiskonController extends Controller
{
    public function index()
    {
        $diskonList = MasterDiskon::latest()->paginate(10);
        return view('bendahara.master-diskon.index', compact('diskonList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_diskon' => ['required', 'string', 'max:255'],
            'metode_diskon' => ['required', 'in:nominal,persentase'],
            'nilai_diskon' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean']
        ]);

        MasterDiskon::create($validated);

        return redirect()->route('bendahara.master-diskon.index')->with('success', 'Master diskon berhasil ditambahkan.');
    }

    public function update(Request $request, MasterDiskon $masterDiskon): RedirectResponse
    {
        $validated = $request->validate([
            'nama_diskon' => ['required', 'string', 'max:255'],
            'metode_diskon' => ['required', 'in:nominal,persentase'],
            'nilai_diskon' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean']
        ]);

        $masterDiskon->update($validated);

        return redirect()->route('bendahara.master-diskon.index')->with('success', 'Master diskon berhasil diperbarui.');
    }

    public function destroy(MasterDiskon $masterDiskon): RedirectResponse
    {
        $masterDiskon->delete();
        return redirect()->route('bendahara.master-diskon.index')->with('success', 'Master diskon berhasil dihapus.');
    }
}

