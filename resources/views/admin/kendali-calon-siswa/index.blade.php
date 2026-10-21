<x-layouts.app>
    <x-slot name="title">Kendali Calon Murid — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6" x-data="{
        // State Modal Pop-up Edit Catatan
        modalOpen: false,
        modalTitle: '',
        modalType: '', // 'cs' atau 'pewawancara'
        modalCandidateId: null,
        modalCandidateName: '',
        modalCandidateNomor: '',
        modalValue: '',
        modalIsDone: false,
        modalStatus: 'idle', // 'idle', 'saving', 'saved', 'error'
        modalActionUrl: '',

        openNoteModal(type, candidateId, name, nomor, value, isDone, url) {
            this.modalType = type;
            this.modalTitle = type === 'cs' ? 'Catatan Khusus CS (Panitia)' : 'Catatan untuk Pewawancara';
            this.modalCandidateId = candidateId;
            this.modalCandidateName = name;
            this.modalCandidateNomor = nomor;
            this.modalValue = value || '';
            this.modalIsDone = !!isDone;
            this.modalStatus = 'idle';
            this.modalActionUrl = url;
            this.modalOpen = true;
            this.$nextTick(() => {
                if (this.$refs.modalTextarea) {
                    this.$refs.modalTextarea.focus();
                }
            });
        },

        saveModalNote() {
            this.modalStatus = 'saving';
            let field = this.modalType === 'cs' ? 'catatan_admin' : 'catatan_untuk_pewawancara';

            fetch(this.modalActionUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: field,
                    value: this.modalValue,
                    is_done: this.modalIsDone ? 1 : 0
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.modalStatus = 'saved';
                    window.dispatchEvent(new CustomEvent('note-updated', {
                        detail: {
                            candidateId: this.modalCandidateId,
                            type: this.modalType,
                            value: this.modalValue,
                            isDone: this.modalIsDone
                        }
                    }));
                    setTimeout(() => {
                        this.modalOpen = false;
                        this.modalStatus = 'idle';
                    }, 400);
                } else {
                    this.modalStatus = 'error';
                }
            })
            .catch(() => {
                this.modalStatus = 'error';
            });
        }
    }">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-800">Kendali Calon Murid</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200">
                        Khusus Admin
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Pusat kendali operasional, koordinasi catatan CS, instruksi pewawancara, serta komunikasi kontak cepat WhatsApp.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.kendali-calon-siswa.export.xls', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-colors shadow-xs cursor-pointer"
                   title="Export tabel kendali ke Microsoft Excel (.xls)">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Export XLS</span>
                </a>
                <a href="{{ route('admin.kendali-calon-siswa.export.pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
                   title="Export rekapitulasi kendali ke dokumen PDF">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('admin.calon-siswa.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Ke Direktori Calon Murid</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.kendali-calon-siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-8 gap-3">
                <!-- Search Keyword -->
                <div class="sm:col-span-2 md:col-span-2 lg:col-span-2 xl:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Peserta</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Nama, NISN, No. Pendaftaran, Promotor..."
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status SPMB</label>
                    <select name="status_spmb" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="SEMUA">Semua Status</option>
                        @foreach ($statusList as $st)
                            <option value="{{ $st->value }}" {{ request('status_spmb') === $st->value ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $st->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jurusan Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jurusan</label>
                    <select name="jurusan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Gelombang Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Gelombang</label>
                    <select name="gelombang_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Gelombang</option>
                        @foreach ($gelombangList as $gel)
                            <option value="{{ $gel->id }}" {{ request('gelombang_id') == $gel->id ? 'selected' : '' }}>
                                {{ $gel->nama_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Program Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Program</label>
                    <select name="program_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Program</option>
                        @foreach ($programList as $prog)
                            <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                                {{ $prog->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Gender</label>
                    <select name="jenis_kelamin" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-nampi-orange/30">
                        <option value="">Semua Gender</option>
                        <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>

                <!-- Actions / Buttons -->
                <div class="flex items-end gap-2 md:col-span-2 lg:col-span-1 xl:col-span-1">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if (request()->hasAny(['q', 'status_spmb', 'jurusan_id', 'gelombang_id', 'program_id', 'jenis_kelamin']))
                        <a href="{{ route('admin.kendali-calon-siswa.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Summary Badge -->
        <div class="flex items-center justify-between text-xs text-slate-500 px-1">
            <span>Menampilkan <strong>{{ $calonSiswaList->count() }}</strong> dari total <strong>{{ $totalCount }}</strong> calon murid</span>
            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <select onchange="window.location.href = this.value" class="px-2 py-1 text-xs rounded-lg border border-slate-200">
                    @foreach ([10, 20, 50, 100] as $count)
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => $count]) }}" {{ request('per_page', 20) == $count ? 'selected' : '' }}>
                            {{ $count }} baris
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table Kendali -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3.5 text-center">No</th>
                            <th class="px-4 py-3.5 min-w-[190px]">Nama Calon Murid</th>
                            <th class="px-3 py-3.5 min-w-[130px]">Jurusan</th>
                            <th class="px-3 py-3.5">Gelombang</th>
                            <th class="px-3 py-3.5 text-center">Biaya Seleksi</th>
                            <th class="px-3 py-3.5 text-center">Status SPMB</th>
                            <th class="px-3 py-3.5 text-center">Program</th>
                            <th class="px-3 py-3.5 min-w-[140px]">Pemberi Rekomendasi</th>
                            <th class="px-3 py-3.5 w-[180px] min-w-[180px] max-w-[180px]">Catatan Khusus CS</th>
                            <th class="px-3 py-3.5 w-[180px] min-w-[180px] max-w-[180px]">Catatan Pewawancara</th>
                            <th class="px-3 py-3.5 text-center min-w-[100px]">WhatsApp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($calonSiswaList as $index => $cs)
                            @php
                                $bayarSeleksi = $cs->pembayaranSeleksi;
                                $statusStr = is_string($cs->status_spmb) ? $cs->status_spmb : ($cs->status_spmb?->value ?? '-');
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors align-top">
                                <!-- No -->
                                <td class="px-3 py-4 text-center font-bold text-slate-400">
                                    {{ $calonSiswaList->firstItem() + $index }}
                                </td>

                                <!-- Nama Calon Murid -->
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('admin.calon-siswa.show', $cs) }}" class="font-bold text-slate-900 text-sm hover:text-nampi-orange transition-colors">
                                            {{ $cs->nama_lengkap }}
                                        </a>
                                        @if($cs->jenis_kelamin === 'L')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200" title="Laki-laki">
                                                L
                                            </span>
                                        @elseif($cs->jenis_kelamin === 'P')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-pink-50 text-pink-700 border border-pink-200" title="Perempuan">
                                                P
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        {{ $cs->nomor_pendaftaran }} &bull; NISN: {{ $cs->nisn }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $cs->sekolah_asal_text }}
                                    </p>
                                </td>

                                <!-- Jurusan -->
                                <td class="px-3 py-4">
                                    <span class="font-bold text-slate-800 block">{{ $cs->jurusan?->nama_jurusan ?? '-' }}</span>
                                    @if($cs->jurusan2)
                                        <span class="text-[11px] text-slate-500 block">Pil. 2: {{ $cs->jurusan2->nama_jurusan }}</span>
                                    @endif
                                </td>

                                <!-- Gelombang -->
                                <td class="px-3 py-4 text-slate-700">
                                    <span class="font-medium">{{ $cs->gelombang?->nama_gelombang ?? '-' }}</span>
                                </td>

                                <!-- Biaya Seleksi (Lunas/Belum Lunas) -->
                                <td class="px-3 py-4 text-center">
                                    @if ($bayarSeleksi && $bayarSeleksi->status === 'DIVERIFIKASI')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ✓ Lunas
                                        </span>
                                    @elseif ($bayarSeleksi && $bayarSeleksi->status === 'PENDING')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            ⏳ Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>

                                <!-- Status SPMB -->
                                <td class="px-3 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ str_replace('_', ' ', $statusStr) }}
                                    </span>
                                </td>

                                <!-- Program -->
                                <td class="px-3 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains(strtolower($cs->program?->nama_program ?? ''), 'unggul') ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $cs->program?->nama_program ?? 'Reguler' }}
                                    </span>
                                </td>

                                <!-- Pemberi Rekomendasi -->
                                <td class="px-3 py-4">
                                    @if($cs->referensi_nama || $cs->referensi_jenis)
                                        <p class="font-bold text-slate-800 text-[11px]">{{ $cs->referensi_nama ?: '-' }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">
                                            {{ str_replace('_', ' ', $cs->referensi_jenis ?? 'REFERENSI') }}
                                        </p>
                                        @if($cs->referensi_rayon)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] bg-slate-100 text-slate-600 font-mono">
                                                Rayon: {{ $cs->referensi_rayon }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">- Mandiri -</span>
                                    @endif
                                </td>

                                <!-- Catatan Khusus CS (Badge Pill Button) -->
                                <td class="px-3 py-4 w-[180px] min-w-[180px] max-w-[180px] align-top" x-data="{
                                    text: {{ json_encode($cs->catatan_admin ?? '') }},
                                    isDone: {{ $cs->is_catatan_admin_done ? 'true' : 'false' }},
                                    init() {
                                        window.addEventListener('note-updated', (e) => {
                                            if (e.detail.candidateId == {{ $cs->id }} && e.detail.type === 'cs') {
                                                this.text = e.detail.value;
                                                this.isDone = e.detail.isDone;
                                            }
                                        });
                                    }
                                }">
                                    <button
                                        type="button"
                                        @click="openNoteModal('cs', {{ $cs->id }}, '{{ addslashes($cs->nama_lengkap) }}', '{{ $cs->nomor_pendaftaran }}', text, isDone, '{{ route('admin.kendali-calon-siswa.update-catatan', $cs) }}')"
                                        class="w-full inline-flex items-start gap-1.5 p-2 rounded-xl text-xs font-medium transition-all cursor-pointer shadow-2xs hover:shadow-xs group text-left break-words"
                                        :class="{
                                            'bg-emerald-50 text-emerald-900 border border-emerald-300 hover:bg-emerald-100 hover:border-emerald-400': isDone,
                                            'bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100 hover:border-amber-400': !isDone && text,
                                            'bg-slate-50 text-slate-500 border border-dashed border-slate-300 hover:bg-slate-100 hover:border-slate-400': !text
                                        }"
                                        :title="'Klik untuk mengedit catatan CS'">
                                        <span class="shrink-0 flex items-center justify-center mt-0.5">
                                            <template x-if="isDone">
                                                <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">✓</span>
                                            </template>
                                            <template x-if="!isDone && text">
                                                <span class="text-amber-600 text-xs font-bold shrink-0">⏳</span>
                                            </template>
                                            <template x-if="!text">
                                                <span class="text-slate-400 text-xs font-bold shrink-0">+</span>
                                            </template>
                                        </span>
                                        <span class="flex-1 min-w-0 text-[11px] leading-relaxed whitespace-pre-line break-words"
                                              x-text="text ? text : 'Catatan CS'"></span>
                                    </button>
                                </td>

                                <!-- Catatan untuk Pewawancara (Badge Pill Button) -->
                                <td class="px-3 py-4 w-[180px] min-w-[180px] max-w-[180px] align-top" x-data="{
                                    text: {{ json_encode($cs->catatan_untuk_pewawancara ?? '') }},
                                    isDone: {{ $cs->is_catatan_pewawancara_done ? 'true' : 'false' }},
                                    init() {
                                        window.addEventListener('note-updated', (e) => {
                                            if (e.detail.candidateId == {{ $cs->id }} && e.detail.type === 'pewawancara') {
                                                this.text = e.detail.value;
                                                this.isDone = e.detail.isDone;
                                            }
                                        });
                                    }
                                }">
                                    <button
                                        type="button"
                                        @click="openNoteModal('pewawancara', {{ $cs->id }}, '{{ addslashes($cs->nama_lengkap) }}', '{{ $cs->nomor_pendaftaran }}', text, isDone, '{{ route('admin.kendali-calon-siswa.update-catatan', $cs) }}')"
                                        class="w-full inline-flex items-start gap-1.5 p-2 rounded-xl text-xs font-medium transition-all cursor-pointer shadow-2xs hover:shadow-xs group text-left break-words"
                                        :class="{
                                            'bg-emerald-50 text-emerald-900 border border-emerald-300 hover:bg-emerald-100 hover:border-emerald-400': isDone,
                                            'bg-indigo-50 text-indigo-900 border border-indigo-300 hover:bg-indigo-100 hover:border-indigo-400': !isDone && text,
                                            'bg-slate-50 text-slate-500 border border-dashed border-slate-300 hover:bg-slate-100 hover:border-slate-400': !text
                                        }"
                                        :title="'Klik untuk mengedit catatan pewawancara'">
                                        <span class="shrink-0 flex items-center justify-center mt-0.5">
                                            <template x-if="isDone">
                                                <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">✓</span>
                                            </template>
                                            <template x-if="!isDone && text">
                                                <span class="text-indigo-600 text-xs font-bold shrink-0">⏳</span>
                                            </template>
                                            <template x-if="!text">
                                                <span class="text-slate-400 text-xs font-bold shrink-0">+</span>
                                            </template>
                                        </span>
                                        <span class="flex-1 min-w-0 text-[11px] leading-relaxed whitespace-pre-line break-words"
                                              x-text="text ? text : 'Pewawancara'"></span>
                                    </button>
                                </td>

                                <!-- WhatsApp Contact Dropdown -->
                                <td class="px-3 py-4 text-center">
                                    <x-whatsapp-contact-dropdown :calonSiswa="$cs" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                        <p class="font-bold text-slate-600">Tidak ada data calon murid</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($calonSiswaList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $calonSiswaList->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Edit Catatan CS & Pewawancara -->
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true"
             @keydown.escape.window="modalOpen = false">
            <!-- Backdrop -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 @click="modalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200/80">

                    <!-- Modal Header -->
                    <div class="px-6 pt-6 pb-4 border-b border-slate-100 bg-slate-50/50 flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-lg shrink-0 shadow-2xs"
                                 :class="modalType === 'cs' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800'">
                                <span x-text="modalType === 'cs' ? '💬' : '📝'"></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800" x-text="modalTitle"></h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    <span class="font-bold text-slate-800" x-text="modalCandidateName"></span>
                                    &bull; <span class="font-mono text-slate-400" x-text="modalCandidateNomor"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Isi Catatan:
                            </label>
                            <textarea
                                x-ref="modalTextarea"
                                x-model="modalValue"
                                @keydown.ctrl.enter="saveModalNote()"
                                rows="4"
                                :placeholder="modalType === 'cs' ? 'Tuliskan catatan khusus dari panitia/CS untuk calon murid ini...' : 'Tuliskan instruksi atau hal penting yang perlu diperhatikan pewawancara...'"
                                class="w-full text-xs p-3 rounded-2xl border border-slate-200 focus:border-nampi-orange focus:ring-4 focus:ring-nampi-orange/15 outline-none resize-y transition bg-slate-50/30 hover:bg-white focus:bg-white text-slate-800 leading-relaxed font-sans"
                            ></textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Tips: Tekan <span class="font-mono font-semibold">Ctrl + Enter</span> untuk simpan cepat.</p>
                        </div>

                        <!-- Checkbox / Toggle Done -->
                        <div class="p-3.5 rounded-2xl border transition-all flex items-center justify-between"
                             :class="modalIsDone ? 'bg-emerald-50/80 border-emerald-300' : 'bg-slate-50 border-slate-200'">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="modal-done-checkbox" x-model="modalIsDone"
                                       class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                                <label for="modal-done-checkbox" class="text-xs font-bold cursor-pointer select-none"
                                       :class="modalIsDone ? 'text-emerald-900' : 'text-slate-700'">
                                    Tandai Catatan Selesai (Done)
                                </label>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full"
                                  :class="modalIsDone ? 'bg-emerald-200 text-emerald-800' : 'bg-slate-200 text-slate-600'">
                                <span x-show="modalIsDone">✓ Badge Hijau</span>
                                <span x-show="!modalIsDone">⏳ Pending</span>
                            </span>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <span class="text-xs font-mono">
                            <span x-show="modalStatus === 'saving'" class="text-amber-600 font-semibold flex items-center gap-1">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                Menyimpan...
                            </span>
                            <span x-show="modalStatus === 'saved'" class="text-emerald-600 font-bold">
                                ✓ Berhasil disimpan!
                            </span>
                            <span x-show="modalStatus === 'error'" class="text-rose-600 font-bold">
                                ✕ Gagal menyimpan, coba lagi.
                            </span>
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="modalOpen = false"
                                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" @click="saveModalNote()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-xs transition cursor-pointer"
                                    :class="modalIsDone ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-900 hover:bg-slate-800'">
                                <span x-show="modalIsDone">✓</span>
                                <span>Simpan Catatan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
