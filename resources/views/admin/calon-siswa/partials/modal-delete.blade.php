<!-- Modal Konfirmasi Hapus Calon Siswa (Admin Only) -->
<div x-show="showDeleteModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title-delete"
     role="dialog"
     aria-modal="true"
     @keydown.escape.window="showDeleteModal = false; confirmText = ''">

    <!-- Backdrop with blur -->
    <div x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @click="showDeleteModal = false; confirmText = ''"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-rose-100"
             @click.outside="showDeleteModal = false; confirmText = ''">

            <!-- Red Header Accent -->
            <div class="bg-gradient-to-r from-rose-50 via-red-50 to-orange-50 px-6 py-5 border-b border-rose-100/80">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-rose-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                            Tindakan Destruktif &bull; Admin Only
                        </span>
                        <h3 class="text-base font-black text-slate-800 mt-1">Konfirmasi Hapus Pendaftar</h3>
                        <p class="text-xs text-slate-500">Data pendaftar akan dimusnahkan secara permanen dari basis data.</p>
                    </div>
                </div>
            </div>

            <form :action="deleteActionUrl" method="POST" class="p-6 space-y-4">
                @csrf
                @method('DELETE')

                <!-- Candidate Info Card -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Calon Murid:</span>
                        <span class="font-mono text-slate-500 text-[11px]" x-text="candidateNomor"></span>
                    </div>
                    <p class="font-bold text-slate-900 text-sm" x-text="candidateName"></p>
                    <p class="text-xs text-slate-500 font-mono mt-0.5" x-show="candidateNisn" x-text="'NISN: ' + candidateNisn"></p>
                </div>

                <!-- Warning Points -->
                <div class="rounded-xl bg-amber-50/80 border border-amber-200 p-3 text-xs text-amber-900 space-y-1">
                    <p class="font-bold text-amber-950 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Dampak penghapusan permanen:</span>
                    </p>
                    <ul class="list-disc list-inside text-[11px] text-amber-800/90 pl-1 space-y-0.5">
                        <li>Seluruh berkas fisik (pas foto, KK, ijazah, bukti bayar) dihapus dari server.</li>
                        <li>Akun login calon siswa dihapus dari sistem.</li>
                        <li>Nomor pendaftaran dan NISN dapat didaftarkan kembali jika dibutuhkan.</li>
                    </ul>
                </div>

                <!-- Type 'HAPUS' Confirmation -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Ketik kata <span class="font-mono font-black text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded border border-rose-200">HAPUS</span> untuk konfirmasi:
                    </label>
                    <input type="text"
                           name="konfirmasi"
                           x-model="confirmText"
                           required
                           autocomplete="off"
                           placeholder='Ketik "HAPUS"'
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold tracking-wider rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500/40 focus:border-rose-500 transition-all uppercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400">
                    <p class="text-[11px] text-slate-400">Harus huruf kapital tepat sesuai tulisan di atas.</p>
                </div>

                <!-- Optional Reason -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Alasan / Catatan Penghapusan <span class="font-normal text-slate-400">(Opsional untuk Audit Log)</span>:
                    </label>
                    <textarea name="alasan"
                              rows="2"
                              placeholder="Misal: Pendaftaran ganda, calon murid membatalkan pendaftaran, atau salah jurusan..."
                              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all text-slate-700"></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button"
                            @click="showDeleteModal = false; confirmText = ''"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="confirmText !== 'HAPUS'"
                            :class="confirmText === 'HAPUS' ? 'bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white cursor-pointer shadow-sm shadow-rose-500/20' : 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200'"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus Permanen</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
