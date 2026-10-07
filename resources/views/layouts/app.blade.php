<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} — Nampi SPMB</title>

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
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50 selection:bg-nampi-orange selection:text-white overflow-x-hidden">

    @include('components.impersonation-banner')

    <div class="min-h-screen flex flex-col lg:flex-row overflow-x-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden hidden"></div>

        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col lg:static lg:inset-0 shrink-0">
            <!-- Brand & Logo -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Wikrama 1 Garut" class="h-10 w-auto bg-white p-1 rounded-md">
                    <div class="flex flex-col">
                        <span class="text-lg font-bold tracking-tight text-white">NAMPI</span>
                        <span class="text-[10px] uppercase font-bold text-nampi-orange tracking-widest">SPMB WIKRAMA</span>
                    </div>
                </div>
                <button type="button" onclick="toggleSidebar()" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 cursor-pointer transition-colors" title="Sembunyikan Menu Samping (Ctrl+B)">
                    <svg class="w-5 h-5 hidden lg:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <svg class="w-5 h-5 lg:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                {{ $sidebar ?? '' }}

                @if(!isset($sidebar))
                    <!-- Default Navigation Placeholder -->
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endif
            </div>

            <!-- User Info / Logout Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-nampi-orange/20 text-nampi-orange font-bold flex items-center justify-center shrink-0 text-sm">
                            {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white truncate">
                                {{ auth()->check() ? auth()->user()->name : 'Tamu / Calon Siswa' }}
                            </p>
                            <p class="text-[10px] text-slate-400 truncate">
                                {{ auth()->check() ? (auth()->user()->role ?? 'User') : 'Calon Siswa' }}
                            </p>
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('logout') }}" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors" title="Keluar dari akun">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header (Navbar) -->
            <header class="h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 cursor-pointer transition-colors" title="Sembunyikan/Tampilkan Menu (Ctrl+B)">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    @if (isset($header))
                        <div>{{ $header }}</div>
                    @else
                        <h2 class="text-xl font-bold text-slate-800">{{ $title ?? 'Dashboard' }}</h2>
                    @endif
                </div>

                <div class="flex items-center gap-2.5">
                    @if (auth()->check() && !auth()->user()->isCalonSiswa())
                        @php
                            $dashboardRoute = auth()->user()->getDashboardRoute();
                        @endphp
                        <a href="{{ route($dashboardRoute) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ request()->routeIs($dashboardRoute) ? 'bg-orange-50 text-nampi-orange border border-orange-200' : 'bg-slate-100 text-slate-700 hover:bg-orange-500 hover:text-white border border-slate-200' }}"
                           title="Kembali ke Dashboard">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard Admin</span>
                        </a>
                    @endif

                    <span class="hidden sm:inline-flex text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <a href="https://wa.me/628112232880" target="_blank" rel="noopener noreferrer">Bantuan CS</a>
                    </span>
                    @auth
                        <div class="flex items-center gap-2 pl-3 border-l border-slate-200">
                            <span class="hidden md:inline-block text-xs font-semibold text-slate-700">
                                {{ auth()->user()->name }}
                            </span>
                            <a href="{{ route('logout') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors" title="Keluar dari sistem">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </a>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <!-- Flash Alerts -->
                @if (session('success'))
                    <x-alert type="success" class="mb-6">
                        {{ session('success') }}
                    </x-alert>
                @endif

                @if (session('error'))
                    <x-alert type="error" class="mb-6">
                        {{ session('error') }}
                    </x-alert>
                @endif

                @if ($errors->any())
                    <x-alert type="error" title="Terdapat beberapa kendala:" class="mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200/80 px-4 py-3.5 sm:px-6 text-center text-xs text-slate-500">
                <span>Nampi 2026 2.5</span>
            </footer>
        </div>
    </div>

    <!-- Floating WhatsApp Helpdesk -->
    <x-whatsapp-helpdesk />

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (!sidebar) return;

            if (window.innerWidth >= 1024) {
                // Desktop toggle (hide/unhide sidebar)
                const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
                sidebar.classList.toggle('lg:-ml-64', isCollapsed);
                try {
                    localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
                } catch (e) {}
            } else {
                // Mobile toggle (slide in/out sidebar with backdrop)
                const isOpen = sidebar.classList.toggle('sidebar-open');
                if (backdrop) {
                    backdrop.classList.toggle('hidden', !isOpen);
                }
            }
        }

        // Apply saved desktop state immediately
        (function() {
            try {
                if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) {
                        sidebar.classList.add('sidebar-collapsed', 'lg:-ml-64');
                    } else {
                        document.addEventListener('DOMContentLoaded', function() {
                            const sb = document.getElementById('sidebar');
                            if (sb) sb.classList.add('sidebar-collapsed', 'lg:-ml-64');
                        });
                    }
                }
            } catch (e) {}

            // Shortcut Keyboard: Ctrl + B
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) || document.activeElement?.isContentEditable) {
                        return;
                    }
                    e.preventDefault();
                    toggleSidebar();
                }
            });

            // Sinkronisasi saat resize layar
            window.addEventListener('resize', function() {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                if (!sidebar) return;

                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('sidebar-open');
                    if (backdrop) backdrop.classList.add('hidden');
                    if (localStorage.getItem('sidebar_collapsed') === 'true') {
                        sidebar.classList.add('sidebar-collapsed', 'lg:-ml-64');
                    } else {
                        sidebar.classList.remove('sidebar-collapsed', 'lg:-ml-64');
                    }
                } else {
                    sidebar.classList.remove('sidebar-collapsed', 'lg:-ml-64');
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
