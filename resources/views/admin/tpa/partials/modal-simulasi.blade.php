<!-- Modal Coba Ujian (Simulasi Admin) -->
<div id="modalSimulasiUjian" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-slate-800 text-base">Coba Ujian TPA (Simulasi)</h3>
                    <p class="text-[11px] text-slate-500">Mencoba pengerjaan dan melihat tampilan calon murid</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalSimulasiUjian').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
        </div>

        <form method="GET" action="{{ route('admin.tpa.simulasi.start') }}" class="space-y-4 text-xs">
            <div class="space-y-2">
                <label class="font-bold text-slate-700 block">Pilih Titik Mulai Simulasi:</label>

                <!-- Pilihan 1: Ruang Tunggu -->
                <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-amber-400 hover:bg-amber-50/30 transition-all cursor-pointer">
                    <input type="radio" name="target" value="ruang-tunggu" checked class="mt-0.5 text-amber-600 focus:ring-amber-500 border-slate-300">
                    <div>
                        <strong class="text-slate-800 block text-xs">Mulai dari Ruang Tunggu (Direkomendasikan)</strong>
                        <p class="text-[11px] text-slate-500 mt-0.5">Lihat panduan teknis, aturan tata tertib, dan tombol mulai ujian layaknya murid sungguhan.</p>
                    </div>
                </label>

                <!-- Pilihan 2: Langsung Soal -->
                <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-amber-400 hover:bg-amber-50/30 transition-all cursor-pointer">
                    <input type="radio" name="target" value="ujian" class="mt-0.5 text-amber-600 focus:ring-amber-500 border-slate-300">
                    <div>
                        <strong class="text-slate-800 block text-xs">Langsung ke Lembar Soal Ujian</strong>
                        <p class="text-[11px] text-slate-500 mt-0.5">Langsung membuka soal Babak 1 (Literasi) tanpa melewati ruang tunggu.</p>
                    </div>
                </label>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center gap-2.5">
                <input type="checkbox" id="simulasi_reset" name="reset" value="1" checked
                       class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500 cursor-pointer">
                <label for="simulasi_reset" class="text-[11px] font-semibold text-slate-700 cursor-pointer select-none">
                    Mulai sesi baru (acak ulang soal & bersihkan jawaban sebelumnya)
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalSimulasiUjian').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black transition-all shadow-xs cursor-pointer flex items-center gap-1.5">
                    <span>Mulai Simulasi Ujian &rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>
