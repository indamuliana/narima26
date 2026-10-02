<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Nampi - SPMB SMK Wikrama 1 Garut' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>

    <!-- Scripts and Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Alpine) {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js';
                s.defer = true;
                document.head.appendChild(s);
            }
        });
    </script>
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased selection:bg-nampi-orange selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand & Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Wikrama 1 Garut" class="h-12 w-auto object-contain transition-transform group-hover:scale-105">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">NAMPI</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold bg-nampi-orange/15 text-nampi-orange uppercase">SPMB</span>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 tracking-wide">SMK WIKRAMA 1 GARUT</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/') }}" class="text-sm font-medium text-slate-700 hover:text-nampi-orange transition-colors">Beranda</a>
                    <a href="{{ url('/#program') }}" class="text-sm font-medium text-slate-600 hover:text-nampi-orange transition-colors">Program & Jurusan</a>
                    <a href="{{ url('/#alur') }}" class="text-sm font-medium text-slate-600 hover:text-nampi-orange transition-colors">Alur Pendaftaran</a>
                    <a href="{{ url('/#biaya') }}" class="text-sm font-medium text-slate-600 hover:text-nampi-orange transition-colors">Biaya</a>
                    <a href="{{ url('/#faq') }}" class="text-sm font-medium text-slate-600 hover:text-nampi-orange transition-colors">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 max-w-[160px] truncate" title="{{ Auth::user()->name }}">
                                {{ Auth::user()->name }}
                            </span>
                            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 text-sm font-semibold text-white bg-nampi-orange hover:bg-nampi-orange-hover rounded-lg shadow-xs transition-all flex items-center gap-1.5">
                                <span>Dashboard</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <a href="{{ route('logout') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Keluar dari akun">
                                Keluar
                            </a>
                        </div>
                    @else
                        <a href="{{ url('/login') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-nampi-orange hover:bg-slate-50 rounded-lg transition-colors">
                            Masuk
                        </a>
                        <a href="{{ url('/register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-nampi-orange hover:bg-nampi-orange-hover rounded-lg shadow-xs hover:shadow transition-all">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden items-center">
                    <button type="button" onclick="toggleMobileMenu()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 pt-2 pb-4 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Beranda</a>
            <a href="{{ url('/#program') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50">Program & Jurusan</a>
            <a href="{{ url('/#alur') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50">Alur Pendaftaran</a>
            <a href="{{ url('/#biaya') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50">Biaya</a>
            <a href="{{ url('/#faq') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50">FAQ</a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                @auth
                    <div class="px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs">
                        <span class="text-slate-500">Masuk sebagai:</span>
                        <div class="font-bold text-slate-800">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-2.5 text-sm font-semibold text-white bg-nampi-orange hover:bg-nampi-orange-hover rounded-lg">Buka Dashboard</a>
                    <a href="{{ route('logout') }}" class="w-full text-center px-4 py-2.5 text-sm font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg">Keluar (Logout)</a>
                @else
                    <a href="{{ url('/login') }}" class="w-full text-center px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">Masuk</a>
                    <a href="{{ url('/register') }}" class="w-full text-center px-4 py-2.5 text-sm font-semibold text-white bg-nampi-orange hover:bg-nampi-orange-hover rounded-lg">Daftar Sekarang</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: About -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Wikrama 1 Garut" class="h-10 w-auto bg-white p-1 rounded-md">
                        <div>
                            <span class="text-lg font-bold text-white tracking-wide">NAMPI</span>
                            <p class="text-xs text-slate-400">Sistem Penerimaan Murid Baru SMK Wikrama 1 Garut</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                        Membangun generasi cerdas, berkarakter, berakhlak mulia, dan siap kerja melalui pendidikan vokasi unggul berstandar industri.
                    </p>
                    <div class="flex items-center gap-3 text-xs text-slate-400 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 text-emerald-400 font-medium">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Sistem Online Aktif
                        </span>
                        <span>Tahun Ajaran 2027/2028</span>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Navigasi SPMB</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ url('/') }}" class="hover:text-nampi-orange transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/#program') }}" class="hover:text-nampi-orange transition-colors">Program Unggulan & Reguler</a></li>
                        <li><a href="{{ url('/#alur') }}" class="hover:text-nampi-orange transition-colors">Alur Registrasi</a></li>
                        <li><a href="{{ url('/#biaya') }}" class="hover:text-nampi-orange transition-colors">Informasi Biaya</a></li>
                        <li><a href="{{ url('/#faq') }}" class="hover:text-nampi-orange transition-colors">Tanya Jawab (FAQ)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact & Address -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Kontak & Lokasi</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Jl. Otto Iskandardinata, Garut, Jawa Barat.
                    </p>
                    <p class="text-sm text-slate-400">
                        WhatsApp: <a href="https://wa.me/628112232880" class="text-nampi-orange hover:underline font-medium">+62 811-2232-880</a>
                    </p>
                    <p class="text-sm text-slate-400">
                        Website: <a href="https://smkwikrama1garut.sch.id" target="_blank" class="hover:underline">smkwikrama1garut.sch.id</a>
                    </p>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} SMK Wikrama 1 Garut | <strong>Nampi<strong> by Inda Muliana (GNU GPL v3.0.)</p>
                <p class="flex items-center gap-1">
                    Dikembangkan untuk kemudahan pendaftaran murid baru.
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Helpdesk -->
    <x-whatsapp-helpdesk />

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const menu = document.getElementById('mobile-menu');
            if (menu) {
                menu.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        menu.classList.add('hidden');
                    });
                });
            }
        });
    </script>
</body>
</html>
