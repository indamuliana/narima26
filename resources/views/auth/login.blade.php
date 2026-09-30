<x-layouts.guest>
    <x-slot name="title">Masuk — Nampi SPMB SMK Wikrama 1 Garut</x-slot>

    <div class="min-h-[calc(100vh-160px)] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-nampi-cream/30 via-slate-50 to-slate-100">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Brand Logo -->
            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="inline-flex flex-col items-center group">
                    <img class="h-16 w-auto object-contain transition-transform group-hover:scale-105" src="{{ asset('images/logo.png') }}" alt="Logo SMK Wikrama 1 Garut">
                    <div class="mt-3 text-center">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">NAMPI SPMB</h2>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mt-0.5">SMK Wikrama 1 Garut</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-200/90 sm:px-10 relative">

                <div class="mb-6 text-center">
                    <h3 class="text-lg font-bold text-slate-800">Masuk ke Akun Anda</h3>
                    <p class="text-xs text-slate-500 mt-1">Gunakan Email atau NISN untuk calon siswa</p>
                </div>

                @auth
                    <!-- Active Session Notification -->
                    <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 shadow-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white font-bold flex items-center justify-center shrink-0 text-sm shadow-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200/80 text-amber-800 uppercase tracking-wider">
                                    Sesi Aktif
                                </span>
                                <h4 class="text-sm font-bold text-slate-900 truncate mt-1">{{ Auth::user()->name }}</h4>
                                <p class="text-xs text-slate-600">
                                    Peran: <strong class="text-amber-900">{{ ucfirst(Auth::user()->role) }}</strong> 
                                    <span class="text-slate-400">({{ Auth::user()->email ?? Auth::user()->username }})</span>
                                </p>
                                
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-nampi-orange hover:bg-nampi-orange-hover transition shadow-xs">
                                        <span>Buka Dashboard</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                    <a href="{{ route('logout') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-100 hover:bg-rose-200 transition">
                                        <span>Keluar (Logout)</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2.5 pt-2 border-t border-amber-200/70 text-[11px] text-amber-800">
                            Ingin berganti akun? Masukkan akun lain pada form di bawah atau pilih akun demo.
                        </p>
                    </div>
                @endauth

                <!-- Session Flash Alert -->
                @if (session('status'))
                    <x-alert type="info" class="mb-5">
                        {{ session('status') }}
                    </x-alert>
                @endif

                @if ($errors->any())
                    <x-alert type="error" class="mb-5">
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                <!-- Form Login -->
                <form class="space-y-5" action="{{ route('login') }}" method="POST">
                    @csrf

                    <div>
                        <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Email / NISN / Username
                        </label>
                        <div class="relative">
                            <input id="login" 
                                   name="login" 
                                   type="text" 
                                   value="{{ old('login') }}" 
                                   required 
                                   autofocus 
                                   placeholder="Contoh: 0012345678 atau admin@wikrama.sch.id"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-nampi-orange/50 focus:border-nampi-orange transition-all">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Password
                            </label>
                        </div>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   required 
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-nampi-orange/50 focus:border-nampi-orange transition-all">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                            <input id="remember" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 rounded border-slate-300 text-nampi-orange focus:ring-nampi-orange">
                            <span>Ingat saya</span>
                        </label>
                        <span class="text-slate-400 text-[11px]">(NISN & No. HP untuk Calon Siswa)</span>
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" size="lg" class="w-full shadow-md hover:shadow-lg">
                            Masuk ke Sistem
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </x-button>
                    </div>
                </form>

                <!-- Demo Credentials Helper -->
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="text-center mb-3">
                        <button type="button" 
                                onclick="toggleDemoHelper()" 
                                class="text-xs font-semibold text-nampi-orange hover:text-nampi-orange-hover flex items-center justify-center gap-1 mx-auto cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Lihat Akun Demo (5 Role Pengujian)</span>
                        </button>
                    </div>

                    <div id="demo-helper" class="hidden text-xs bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-2">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <div>
                                <span class="font-bold text-slate-800">Admin</span>
                                <span class="block font-mono text-[11px] text-slate-500">admin@wikrama.sch.id</span>
                            </div>
                            <button type="button" onclick="fillDemo('admin@wikrama.sch.id', 'admin123')" class="px-2.5 py-1 text-[11px] font-semibold text-nampi-orange bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 cursor-pointer transition">
                                Isi Otomatis
                            </button>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <div>
                                <span class="font-bold text-slate-800">Bendahara</span>
                                <span class="block font-mono text-[11px] text-slate-500">bendahara@wikrama.sch.id</span>
                            </div>
                            <button type="button" onclick="fillDemo('bendahara@wikrama.sch.id', 'bendahara123')" class="px-2.5 py-1 text-[11px] font-semibold text-nampi-orange bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 cursor-pointer transition">
                                Isi Otomatis
                            </button>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <div>
                                <span class="font-bold text-slate-800">Pewawancara</span>
                                <span class="block font-mono text-[11px] text-slate-500">pewawancara@wikrama.sch.id</span>
                            </div>
                            <button type="button" onclick="fillDemo('pewawancara@wikrama.sch.id', 'pewawancara123')" class="px-2.5 py-1 text-[11px] font-semibold text-nampi-orange bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 cursor-pointer transition">
                                Isi Otomatis
                            </button>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <div>
                                <span class="font-bold text-slate-800">Kepala Sekolah</span>
                                <span class="block font-mono text-[11px] text-slate-500">kepsek@wikrama.sch.id</span>
                            </div>
                            <button type="button" onclick="fillDemo('kepsek@wikrama.sch.id', 'kepsek123')" class="px-2.5 py-1 text-[11px] font-semibold text-nampi-orange bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 cursor-pointer transition">
                                Isi Otomatis
                            </button>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <div>
                                <span class="font-bold text-slate-800">Calon Siswa</span>
                                <span class="block font-mono text-[11px] text-slate-500">0012345678 (NISN)</span>
                            </div>
                            <button type="button" onclick="fillDemo('0012345678', 'siswa123')" class="px-2.5 py-1 text-[11px] font-semibold text-nampi-orange bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 cursor-pointer transition">
                                Isi Otomatis
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Registration link -->
                <div class="mt-6 text-center text-xs text-slate-500">
                    Belum mendaftar sebagai calon peserta didik?
                    <a href="{{ url('/register') }}" class="font-bold text-nampi-orange hover:underline ml-1">
                        Daftar Baru di Sini
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDemoHelper() {
            const el = document.getElementById('demo-helper');
            if (el) el.classList.toggle('hidden');
        }

        function fillDemo(login, password) {
            const loginEl = document.getElementById('login');
            const passEl = document.getElementById('password');
            if (loginEl) {
                loginEl.value = login;
                loginEl.focus();
            }
            if (passEl) {
                passEl.value = password;
            }
        }
    </script>
</x-layouts.guest>
