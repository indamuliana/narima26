@props(['calonSiswa'])

@php
    $normalizeWa = function ($phone) {
        if (!$phone) return null;
        $clean = preg_replace('/[^0-9]/', '', (string)$phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }
        return (strlen($clean) >= 10) ? $clean : null;
    };

    $nama = $calonSiswa->nama_lengkap ?? 'Calon Siswa';
    $noDaftar = $calonSiswa->nomor_pendaftaran ?? '-';

    $phoneSiswaRaw = $calonSiswa->no_hp_siswa;
    $phoneSiswa = $normalizeWa($phoneSiswaRaw);
    $textSiswa = rawurlencode("Assalamu'alaikum / Halo Ananda {$nama} (No. Pendaftaran: {$noDaftar}), kami dari Panitia SPMB SMK Wikrama 1 Garut ingin mengonfirmasi terkait pendaftaran Anda.");
    $linkSiswa = $phoneSiswa ? "https://wa.me/{$phoneSiswa}?text={$textSiswa}" : null;

    $ortu = $calonSiswa->relationLoaded('dataOrangtua') ? $calonSiswa->dataOrangtua : $calonSiswa->dataOrangtua;
    
    $phoneAyahRaw = $calonSiswa->no_hp_ayah ?? $ortu?->no_hp_ayah;
    $phoneAyah = $normalizeWa($phoneAyahRaw);
    $textAyah = rawurlencode("Assalamu'alaikum Wr. Wb. Yth. Bapak dari Ananda {$nama} (No. Pendaftaran: {$noDaftar}), kami dari Panitia SPMB SMK Wikrama 1 Garut ingin mengonfirmasi terkait pendaftaran putra/putri Bapak.");
    $linkAyah = $phoneAyah ? "https://wa.me/{$phoneAyah}?text={$textAyah}" : null;

    $phoneIbuRaw = $calonSiswa->no_hp_ibu ?? $ortu?->no_hp_ibu;
    $phoneIbu = $normalizeWa($phoneIbuRaw);
    $textIbu = rawurlencode("Assalamu'alaikum Wr. Wb. Yth. Ibu dari Ananda {$nama} (No. Pendaftaran: {$noDaftar}), kami dari Panitia SPMB SMK Wikrama 1 Garut ingin mengonfirmasi terkait pendaftaran putra/putri Ibu.");
    $linkIbu = $phoneIbu ? "https://wa.me/{$phoneIbu}?text={$textIbu}" : null;

    $phoneWaliRaw = $ortu?->no_hp_wali;
    $phoneWali = $normalizeWa($phoneWaliRaw);
    $textWali = rawurlencode("Assalamu'alaikum Wr. Wb. Yth. Wali dari Ananda {$nama} (No. Pendaftaran: {$noDaftar}), kami dari Panitia SPMB SMK Wikrama 1 Garut ingin mengonfirmasi terkait pendaftaran ananda.");
    $linkWali = $phoneWali ? "https://wa.me/{$phoneWali}?text={$textWali}" : null;
@endphp

<div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button 
        type="button" 
        @click="open = !open" 
        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 transition-colors cursor-pointer"
        title="Hubungi Kontak Calon Siswa & Orang Tua via WhatsApp">
        <svg class="w-3.5 h-3.5 text-emerald-600 fill-current shrink-0" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
        <span>WA</span>
        <svg class="w-3 h-3 text-emerald-600 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Popover Menu -->
    <div 
        x-show="open" 
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-1.5 w-60 rounded-xl bg-white shadow-xl ring-1 ring-black/10 divide-y divide-slate-100 z-50 text-left overflow-hidden"
        style="display: none;">
        
        <div class="px-3 py-2 bg-slate-50 border-b border-slate-100">
            <p class="text-[11px] font-bold text-slate-800 truncate">{{ $nama }}</p>
            <p class="text-[10px] text-slate-500 font-mono">No. Reg: {{ $noDaftar }}</p>
        </div>

        <div class="py-1">
            <!-- Siswa -->
            @if($linkSiswa)
                <a href="{{ $linkSiswa }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors group">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">S</span>
                        <div>
                            <p class="font-semibold text-slate-800 leading-tight">Hubungi Siswa</p>
                            <p class="text-[10px] text-slate-500 font-mono leading-tight">{{ $phoneSiswaRaw }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-0.5">
                        Chat
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>
            @else
                <div class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-400 opacity-60">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-[10px]">S</span>
                        <div>
                            <p class="font-medium text-slate-500 leading-tight">Hubungi Siswa</p>
                            <p class="text-[10px] text-slate-400 leading-tight">Belum diisi</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-300">-</span>
                </div>
            @endif

            <!-- Ayah -->
            @if($linkAyah)
                <a href="{{ $linkAyah }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors group">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">A</span>
                        <div>
                            <p class="font-semibold text-slate-800 leading-tight">Hubungi Ayah</p>
                            <p class="text-[10px] text-slate-500 font-mono leading-tight">{{ $phoneAyahRaw }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-0.5">
                        Chat
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>
            @else
                <div class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-400 opacity-60">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-[10px]">A</span>
                        <div>
                            <p class="font-medium text-slate-500 leading-tight">Hubungi Ayah</p>
                            <p class="text-[10px] text-slate-400 leading-tight">Belum diisi</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-300">-</span>
                </div>
            @endif

            <!-- Ibu -->
            @if($linkIbu)
                <a href="{{ $linkIbu }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors group">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-[10px]">I</span>
                        <div>
                            <p class="font-semibold text-slate-800 leading-tight">Hubungi Ibu</p>
                            <p class="text-[10px] text-slate-500 font-mono leading-tight">{{ $phoneIbuRaw }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-0.5">
                        Chat
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>
            @else
                <div class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-400 opacity-60">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-[10px]">I</span>
                        <div>
                            <p class="font-medium text-slate-500 leading-tight">Hubungi Ibu</p>
                            <p class="text-[10px] text-slate-400 leading-tight">Belum diisi</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-300">-</span>
                </div>
            @endif

            <!-- Wali (Opsional jika ada) -->
            @if($phoneWaliRaw)
                <a href="{{ $linkWali }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors group">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-[10px]">W</span>
                        <div>
                            <p class="font-semibold text-slate-800 leading-tight">Hubungi Wali</p>
                            <p class="text-[10px] text-slate-500 font-mono leading-tight">{{ $phoneWaliRaw }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-0.5">
                        Chat
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>
            @endif
        </div>
    </div>
</div>
