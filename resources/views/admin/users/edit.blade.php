<x-layouts.app>
    <x-slot name="title">Edit Pengguna: {{ $user->name }} — SPMB Nampi</x-slot>

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
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Akun Pengguna</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $user->role_label }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Perbarui data profil, peran akses, atau atur ulang kata sandi</p>
            </div>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

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

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
            @csrf
            @method('PUT')

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
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Username Login <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nomor WhatsApp / HP <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
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

                @if ((int) $user->id === (int) auth()->id())
                    <div class="p-3 mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>Ini adalah akun Administrator Anda sendiri. Peran dan status aktif dikunci untuk menjaga integritas akses sistem.</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    @php $currRole = old('role', $user->role); @endphp

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-teal-500
                        {{ $currRole === 'guru' ? 'border-teal-500 bg-teal-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="guru" {{ $currRole === 'guru' ? 'checked' : '' }}
                                   {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }} required
                                   class="text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-bold text-slate-900">Guru</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Akses Dashboard Utama, Direktori Calon Murid (detail & cetak PDF), Ekspor CSV/PDF, serta Laporan Rekapitulasi.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-emerald-500
                        {{ $currRole === 'bendahara' ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="bendahara" {{ $currRole === 'bendahara' ? 'checked' : '' }}
                                   {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }} required
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold text-slate-900">Bendahara</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Verifikasi pembayaran seleksi dan daftar ulang, cetak kwitansi sah, pantau arus kas kasir.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-blue-500
                        {{ $currRole === 'kepala_sekolah' ? 'border-blue-500 bg-blue-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="kepala_sekolah" {{ $currRole === 'kepala_sekolah' ? 'checked' : '' }}
                                   {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }} required
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-900">Kepala Sekolah</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Akses sidang pleno kelulusan, penetapan kelulusan, penerbitan SK kelulusan, dan pengawasan kuota.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-amber-500
                        {{ $currRole === 'pewawancara' ? 'border-amber-500 bg-amber-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="pewawancara" {{ $currRole === 'pewawancara' ? 'checked' : '' }}
                                   {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }} required
                                   class="text-amber-600 focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-900">Pewawancara</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Penilaian antrean wawancara Calon Murid & orang tua, pengisian rubrik instrumen, dan penentuan rekomendasi.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-purple-500 sm:col-span-2
                        {{ $currRole === 'admin' ? 'border-purple-500 bg-purple-50/30' : 'border-slate-200' }}">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <input type="radio" name="role" value="admin" {{ $currRole === 'admin' ? 'checked' : '' }}
                                   {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }} required
                                   class="text-purple-600 focus:ring-purple-500">
                            <span class="text-xs font-bold text-slate-900">Administrator Sistem</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                            Hak akses penuh ke seluruh modul sistem: Master Jurusan, Keuangan, Alokasi Pewawancara, Audit Trail, dan Manajemen Pengguna.
                        </p>
                    </label>

                    @if ($user->role === 'calon_siswa')
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all border-slate-300 bg-slate-50 sm:col-span-2">
                            <div class="flex items-center gap-2.5 mb-1.5">
                                <input type="radio" name="role" value="calon_siswa" checked class="text-slate-600">
                                <span class="text-xs font-bold text-slate-900">Calon Murid</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed pl-5">
                                Akun Calon Murid terikat dengan data pendaftaran SPMB.
                            </p>
                        </label>
                    @endif
                </div>

                @if ((int) $user->id === (int) auth()->id())
                    <input type="hidden" name="role" value="admin">
                @endif
            </div>

            <!-- Section 3: Reset Kata Sandi -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-nampi-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Ubah Kata Sandi (Password)</span>
                </h2>
                <p class="text-xs text-slate-500 mb-3">Kosongkan jika Anda tidak ingin mengubah kata sandi pengguna ini.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Password Baru
                        </label>
                        <input type="password" id="password" name="password"
                               placeholder="Minimal 6 karakter"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Ketik ulang password baru"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <label class="inline-flex items-center gap-2 {{ (int) $user->id === (int) auth()->id() ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }}
                               class="rounded border-slate-300 text-nampi-orange focus:ring-nampi-orange">
                        <span class="text-xs font-bold text-slate-800">Status Akun Aktif</span>
                    </label>
                    @if ((int) $user->id === (int) auth()->id())
                        <input type="hidden" name="is_active" value="1">
                    @endif
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
