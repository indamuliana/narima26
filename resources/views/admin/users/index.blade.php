<x-layouts.app>
    <x-slot name="title">Manajemen Pengguna & Hak Akses — SPMB Nampi</x-slot>

    <x-slot name="sidebar">
        @include('admin.partials.sidebar')
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pengguna Sistem</h1>
                <p class="text-xs text-slate-500 mt-1">Kelola akun dan hak akses: Bendahara, Kepala Sekolah, Pewawancara, Guru, dan Administrator</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-nampi-orange text-white text-xs font-bold hover:bg-orange-600 transition-all shadow-xs hover:shadow cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Pengguna Baru</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Akun</span>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-cyan-600 uppercase tracking-wider">Operator</span>
                <p class="text-xl font-black text-cyan-700 mt-1">{{ $stats['operator'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-teal-600 uppercase tracking-wider">Guru</span>
                <p class="text-xl font-black text-teal-700 mt-1">{{ $stats['guru'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Bendahara</span>
                <p class="text-xl font-black text-emerald-700 mt-1">{{ $stats['bendahara'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Kepala Sekolah</span>
                <p class="text-xl font-black text-blue-700 mt-1">{{ $stats['kepala_sekolah'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Pewawancara</span>
                <p class="text-xl font-black text-amber-700 mt-1">{{ $stats['pewawancara'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider">Administrator</span>
                <p class="text-xl font-black text-purple-700 mt-1">{{ $stats['admin'] }}</p>
            </div>
        </div>

        <!-- Filter & Search Panel -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-5">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Pengguna</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Cari nama, email, username, atau nomor telepon..."
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Filter Peran (Role)</label>
                    <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange bg-white transition-all">
                        <option value="">Semua Peran</option>
                        @foreach ($availableRoles as $key => $lbl)
                            <option value="{{ $key }}" {{ $roleFilter === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                        <option value="calon_siswa" {{ $roleFilter === 'calon_siswa' ? 'selected' : '' }}>Calon Siswa</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status Akun</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-nampi-orange focus:border-nampi-orange bg-white transition-all">
                        <option value="">Semua Status</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer text-center">
                        Filter
                    </button>
                    @if ($search || $roleFilter || $statusFilter)
                        <a href="{{ route('admin.users.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Listing -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-900">Daftar Akun Pengguna</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Menampilkan {{ $users->total() }} akun terdaftar dalam sistem</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Pengguna</th>
                            <th class="px-5 py-3.5">Kontak</th>
                            <th class="px-4 py-3.5 text-center">Hak Akses / Peran</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-center">Terdaftar</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0
                                            @if($u->role === 'admin') bg-purple-100 text-purple-700
                                            @elseif($u->role === 'operator') bg-cyan-100 text-cyan-700
                                            @elseif($u->role === 'guru') bg-teal-100 text-teal-700
                                            @elseif($u->role === 'bendahara') bg-emerald-100 text-emerald-700
                                            @elseif($u->role === 'kepala_sekolah') bg-blue-100 text-blue-700
                                            @elseif($u->role === 'pewawancara') bg-amber-100 text-amber-700
                                            @else bg-slate-100 text-slate-700
                                            @endif">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-slate-900 text-sm block">{{ $u->name }}</span>
                                                @if((int) $u->id === (int) auth()->id())
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                {{ $u->username ? '@'.$u->username : 'Tanpa username' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-0.5">
                                        <span class="text-slate-700 block font-medium">{{ $u->email }}</span>
                                        <span class="text-slate-400 text-[11px] block">{{ $u->phone ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $badgeStyles = match ($u->role) {
                                            'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'operator' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            'guru' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'bendahara' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'kepala_sekolah' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'pewawancara' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                                        {{ $u->role_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($u->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center text-slate-400 text-[11px]">
                                    {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.users.edit', $u) }}"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                                            Edit
                                        </a>

                                        @if ((int) $u->id !== (int) auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors cursor-pointer {{ $u->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold hover:bg-rose-100 transition-colors cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    Tidak ada pengguna yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-5 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
