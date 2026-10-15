@props([
    'phone' => null,
    'message' => null,
    'forceMenu' => false,
])

@php
    $phoneService = app(\App\Services\PhoneNumberService::class);
    $csReguler = $phoneService->getCsRegulerNumber();
    $csUnggulan = $phoneService->getCsUnggulanNumber();

    $user = auth()->user();
    $directUrl = null;

    if ($phone) {
        $directUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $phone) . ($message ? "?text=" . urlencode($message) : "");
    } elseif ($user && !$forceMenu) {
        if ($user->isCalonSiswa()) {
            $isUnggulan = $phoneService->isUnggulan($user->calonSiswa);
            $targetPhone = $isUnggulan ? $csUnggulan : $csReguler;
            $nama = $user->calonSiswa?->nama_lengkap ?? $user->name;
            $progName = $isUnggulan ? 'Program Unggulan' : 'Program Reguler';
            $msg = $message ?? "Halo CS SPMB SMK Wikrama 1 Garut, saya {$nama} (Calon Siswa {$progName}) ingin bertanya.";
            $directUrl = "https://wa.me/{$targetPhone}?text=" . urlencode($msg);
        } else {
            // Admin / Staff -> Default CS Reguler
            $msg = $message ?? "Halo CS SPMB SMK Wikrama 1 Garut, saya {$user->name} ({$user->role_label}) memerlukan bantuan.";
            $directUrl = "https://wa.me/{$csReguler}?text=" . urlencode($msg);
        }
    }

    $msgReguler = 'Halo Admin SPMB SMK Wikrama 1 Garut, saya ingin berkonsultasi seputar pendaftaran siswa baru.';
    $msgUnggulan = 'Halo Admin SPMB SMK Wikrama 1 Garut, saya ingin konsultasi seputar Program Unggulan (Asrama, Santri, Belajar Malam).';
    $csRegulerUrl = "https://wa.me/{$csReguler}?text=" . urlencode($msgReguler);
    $csUnggulanUrl = "https://wa.me/{$csUnggulan}?text=" . urlencode($msgUnggulan);
@endphp

@if ($directUrl)
    {{-- Single Direct Button untuk Pengguna Login (Siswa/Admin) atau saat parameter phone diisi --}}
    <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 group">
        <a href="{{ $directUrl }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="flex items-center gap-2.5 bg-[#128C7E] hover:bg-[#075E54] text-white font-semibold py-3 px-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span class="text-sm tracking-wide hidden sm:inline-block">CS</span>
            <span class="relative flex h-2.5 w-2.5 -mr-1">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
            </span>
        </a>
    </div>
@else
    {{-- Popover Dual CS Menu untuk Pengunjung Umum / Guest --}}
    <div x-data="{ openHelpdeskMenu: false }" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50">
        <!-- Floating Popover -->
        <div x-show="openHelpdeskMenu" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             @click.outside="openHelpdeskMenu = false"
             class="absolute bottom-16 right-0 w-72 sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 p-3.5 mb-2 text-left"
             style="display: none;">
             
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                <div>
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm">Hubungi Customer Service</h4>
                    <p class="text-[11px] text-slate-500">Pilih layanan konsultasi panitia:</p>
                </div>
                <button @click="openHelpdeskMenu = false" type="button" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-2">
                <!-- Opsi 1: Konsultasi Pendaftaran (Reguler) -->
                <a href="{{ $csRegulerUrl }}" target="_blank" rel="noopener noreferrer" 
                   class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-emerald-50 border border-slate-100 hover:border-emerald-300 transition group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-xs text-slate-800 group-hover:text-emerald-700">Konsultasi Pendaftaran</div>
                        <div class="text-[11px] text-slate-500 truncate">Program Reguler &amp; Umum</div>
                        <div class="text-[10px] font-mono text-emerald-600 font-semibold mt-0.5">+62 811-2232-880</div>
                    </div>
                </a>

                <!-- Opsi 2: Konsultasi Asrama (Unggulan) -->
                <a href="{{ $csUnggulanUrl }}" target="_blank" rel="noopener noreferrer" 
                   class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-amber-50 border border-slate-100 hover:border-amber-300 transition group">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-xs text-slate-800 group-hover:text-amber-700">Konsultasi Asrama</div>
                        <div class="text-[11px] text-slate-500 truncate">Program Unggulan, Santri, Belajar Malam</div>
                        <div class="text-[10px] font-mono text-amber-600 font-semibold mt-0.5">+62 811-2280-2880</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Floating Button Trigger -->
        <button type="button"
                @click="openHelpdeskMenu = !openHelpdeskMenu"
                class="flex items-center gap-2.5 bg-[#128C7E] hover:bg-[#075E54] text-white font-semibold py-3 px-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span class="text-sm tracking-wide hidden sm:inline-block">CS</span>
            <span class="relative flex h-2.5 w-2.5 -mr-1">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
            </span>
        </button>
    </div>
@endif

