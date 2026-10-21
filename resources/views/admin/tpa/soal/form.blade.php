<x-layouts.app>
    <x-slot name="title">{{ $isEdit ? 'Edit Butir Soal TPA' : 'Tambah Butir Soal TPA' }} — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.tpa.soal.index', ['babak' => $soal->babak ?? 'literasi']) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Bank Soal</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800">
                    {{ $isEdit ? 'Edit Butir Soal TPA' : 'Tambah Butir Soal TPA' }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Lengkapi teks pertanyaan, gambar pendukung (opsional), pilihan jawaban, dan kunci jawaban</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ $isEdit ? route('admin.tpa.soal.update', $soal) : route('admin.tpa.soal.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- Metadata Soal -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Umum Soal</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Babak -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Babak Ujian <span class="text-rose-500">*</span>
                        </label>
                        <select name="babak" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                            <option value="literasi" {{ old('babak', $soal->babak) === 'literasi' ? 'selected' : '' }}>Babak 1: Literasi</option>
                            <option value="numerasi" {{ old('babak', $soal->babak) === 'numerasi' ? 'selected' : '' }}>Babak 2: Numerasi</option>
                        </select>
                    </div>

                    <!-- Nomor Urut -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Nomor Urut Soal <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="nomor_urut" min="1" required
                               value="{{ old('nomor_urut', $soal->nomor_urut) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                    </div>

                    <!-- Bobot Poin -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Bobot Skor (Poin) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="bobot" min="1" max="100" required
                               value="{{ old('bobot', $soal->bobot ?? 1) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none">
                        <p class="text-[11px] text-slate-400">Poin yang diperoleh jika jawaban benar</p>
                    </div>
                </div>

                <!-- Teks Pertanyaan -->
                <div class="space-y-1.5 pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Isi Pertanyaan Soal <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="pertanyaan" rows="5" required
                              class="w-full p-4 rounded-xl border border-slate-200 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none leading-relaxed"
                              placeholder="Ketik teks pertanyaan atau stimulus soal di sini...">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                </div>

                <!-- Upload Gambar -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Gambar Pendukung (Opsional)
                    </label>

                    @if ($isEdit && $soal->gambar_path)
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl border border-slate-200 w-fit">
                            <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Gambar Soal" class="w-24 h-20 object-contain bg-white rounded-lg border border-slate-200">
                            <div class="space-y-1">
                                <p class="text-xs font-semibold text-slate-700">Gambar Saat Ini</p>
                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-medium cursor-pointer">
                                    <input type="checkbox" name="hapus_gambar" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus gambar ini</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <input type="file" name="gambar" accept="image/png,image/jpeg,image/webp"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400">Format diterima: JPG, PNG, WEBP. Maksimal 4 MB.</p>
                </div>
            </div>

            <!-- Opsi Pilihan Ganda -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Opsi Pilihan Jawaban</h2>
                        <p class="text-xs text-slate-400">Pilih salah satu lingkaran radio sebagai <strong>Kunci Jawaban Benar</strong></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="tambahOpsi()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Opsi</span>
                        </button>
                        <button type="button" onclick="kurangiOpsi()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-colors cursor-pointer">
                            <span>- Hapus Opsi Terakhir</span>
                        </button>
                    </div>
                </div>

                @php
                    $existingOptions = old('opsi_keys') ?
                        collect(old('opsi_keys'))->map(fn($k, $i) => ['key' => $k, 'text' => old('opsi_texts')[$i] ?? ''])->toArray() :
                        ($soal->opsi_jawaban ?? [
                            ['key' => 'A', 'text' => ''],
                            ['key' => 'B', 'text' => ''],
                            ['key' => 'C', 'text' => ''],
                            ['key' => 'D', 'text' => ''],
                        ]);
                    $selectedKey = old('kunci_jawaban', $soal->kunci_jawaban ?? 'A');
                @endphp

                <div id="container-opsi" class="space-y-4">
                    @foreach ($existingOptions as $opt)
                        <div class="opsi-row flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 transition-colors" data-key="{{ $opt['key'] }}">
                            <div class="pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer" title="Jadikan Kunci Jawaban Benar">
                                    <input type="radio" name="kunci_jawaban" value="{{ $opt['key'] }}"
                                           {{ $selectedKey === $opt['key'] ? 'checked' : '' }} required
                                           class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-white font-black text-xs">
                                        {{ $opt['key'] }}
                                    </span>
                                </label>
                                <input type="hidden" name="opsi_keys[]" value="{{ $opt['key'] }}" class="input-key">
                            </div>
                            <div class="flex-1">
                                <input type="text" name="opsi_texts[]" required value="{{ $opt['text'] }}"
                                       placeholder="Tuliskan teks jawaban untuk opsi {{ $opt['key'] }}..."
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $soal->is_active ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500">
                    <span class="text-xs font-semibold text-slate-700">Status Soal Aktif (Dapat dikerjakan siswa)</span>
                </label>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.tpa.soal.index', ['babak' => $soal->babak ?? 'literasi']) }}"
                       class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-colors shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ $isEdit ? 'Perbarui Soal' : 'Simpan Butir Soal' }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const ALPHABET = ['A', 'B', 'C', 'D', 'E'];

        function tambahOpsi() {
            const container = document.getElementById('container-opsi');
            const rows = container.getElementsByClassName('opsi-row');
            if (rows.length >= 5) {
                alert('Maksimal 5 pilihan opsi (A s.d. E).');
                return;
            }

            const nextKey = ALPHABET[rows.length];
            const newDiv = document.createElement('div');
            newDiv.className = 'opsi-row flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 transition-colors';
            newDiv.setAttribute('data-key', nextKey);
            newDiv.innerHTML = `
                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer" title="Jadikan Kunci Jawaban Benar">
                        <input type="radio" name="kunci_jawaban" value="${nextKey}" required
                               class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-white font-black text-xs">
                            ${nextKey}
                        </span>
                    </label>
                    <input type="hidden" name="opsi_keys[]" value="${nextKey}" class="input-key">
                </div>
                <div class="flex-1">
                    <input type="text" name="opsi_texts[]" required value=""
                           placeholder="Tuliskan teks jawaban untuk opsi ${nextKey}..."
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                </div>
            `;
            container.appendChild(newDiv);
        }

        function kurangiOpsi() {
            const container = document.getElementById('container-opsi');
            const rows = container.getElementsByClassName('opsi-row');
            if (rows.length <= 2) {
                alert('Minimal 2 pilihan opsi jawaban.');
                return;
            }
            container.removeChild(rows[rows.length - 1]);
        }
    </script>
</x-layouts.app>
