<x-layouts.app>
    <x-slot name="title">Tambah Pengguna Baru — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Daftar Pengguna</span>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Pengguna Baru</h1>
                <p class="text-xs text-slate-500 mt-1">Buat akun untuk Bendahara, Kepala Sekolah, Pewawancara, Guru, atau Administrator</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                <p class="font-bold mb-1">Terjadi kesalahan pada isian form:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
            @csrf

            <!-- Section 1: Profil Pengguna -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-nampi-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Informasi Profil & Identitas</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Dra. Hj. Siti Nurhasanah, M.Pd"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Username Login <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                               placeholder="Contoh: sitinurhasanah"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               placeholder="Contoh: sitinur@wikrama.sch.id"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nomor WhatsApp / HP <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Peran & Hak Akses -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-nampi-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Penetapan Peran (Role) & Hak Akses</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-teal-500
                        {{ old('role') === 'guru' ? 'border-teal-500 bg-teal-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="guru" {{ old('role') === 'guru' ? 'checked' : '' }} required
                                   class="text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-bold text-slate-900">Guru</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-700">Role Baru</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Akses Dashboard Utama, Direktori Calon Siswa (beserta detail & cetak PDF), Ekspor CSV/PDF, serta Laporan Rekapitulasi.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-emerald-500
                        {{ old('role') === 'bendahara' ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="bendahara" {{ old('role') === 'bendahara' ? 'checked' : '' }} required
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold text-slate-900">Bendahara</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Verifikasi pembayaran seleksi dan daftar ulang, cetak kwitansi sah, pantau arus kas kasir.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-blue-500
                        {{ old('role') === 'kepala_sekolah' ? 'border-blue-500 bg-blue-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="kepala_sekolah" {{ old('role') === 'kepala_sekolah' ? 'checked' : '' }} required
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-900">Kepala Sekolah</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Akses sidang pleno kelulusan, penetapan status kelulusan, penerbitan SK kelulusan, dan pengawasan kuota.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-amber-500
                        {{ old('role') === 'pewawancara' ? 'border-amber-500 bg-amber-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="pewawancara" {{ old('role') === 'pewawancara' ? 'checked' : '' }} required
                                   class="text-amber-600 focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-900">Pewawancara</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Penilaian antrean wawancara calon siswa & orang tua, pengisian rubrik instrumen, dan penentuan rekomendasi.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-purple-500 sm:col-span-2
                        {{ old('role') === 'admin' ? 'border-purple-500 bg-purple-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required
                                   class="text-purple-600 focus:ring-purple-500">
                            <span class="text-xs font-bold text-slate-900">Administrator Sistem</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Hak akses penuh ke seluruh modul sistem: Master Jurusan, Master Keuangan, Alokasi Pewawancara, Audit Trail, dan Manajemen Pengguna.
                        </p>
                    </label>
                </div>
            </div>

            <!-- Section 3: Keamanan & Password -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-nampi-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Kata Sandi (Password)</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                               placeholder="Minimal 6 karakter"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Ulangi Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Ketik ulang password"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                               class="rounded border-slate-300 text-nampi-orange focus:ring-nampi-orange">
                        <span class="text-xs font-bold text-slate-800">Akun Langsung Aktif</span>
                    </label>
                    <p class="text-[11px] text-slate-400 pl-6">Pengguna dapat langsung login dengan kredensial yang dibuat.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-all shadow-xs cursor-pointer">
                    Simpan & Buat Akun
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
