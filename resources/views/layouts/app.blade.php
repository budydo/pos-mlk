<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS MLK')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script>
        // Handle sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-soft-green">
    <div class="flex h-screen bg-soft-green">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-200 ease-in-out md:static md:translate-x-0 -translate-x-full lg:w-72 shadow-lg">
            <!-- Sidebar Header -->
            <div class="sidebar-header sticky top-0 bg-gradient-to-r from-accent-green to-emerald-600">
                <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline">
                    <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-accent-green font-bold text-lg">
                        POS
                    </div>
                    <div>
                        <div class="font-bold text-white text-lg">POS MLK</div>
                        <div class="text-xs text-emerald-100">Sistem POS Profesional</div>
                    </div>
                </a>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav flex-1">
                <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }} no-underline">
                    <span>🏠 Beranda</span>
                </a>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} no-underline">
                    <span>📊 Dashboard</span>
                </a>
                
                <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Penjualan
                </div>
                <a href="{{ route('cashier') }}" class="sidebar-link {{ request()->routeIs('cashier') ? 'active' : '' }} no-underline">
                    <span>🛒 Kasir</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }} no-underline">
                    <span>📝 Transaksi</span>
                </a>

                <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Manajemen
                </div>
                <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }} no-underline">
                    <span>📦 Produk</span>
                </a>
                <a href="{{ route('stock-entries.index') }}" class="sidebar-link {{ request()->routeIs('stock-entries.*') ? 'active' : '' }} no-underline">
                    <span>📚 Stok</span>
                </a>

                <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Admin
                </div>
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }} no-underline">
                    <span>👥 Pengguna</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-200">
                <div class="text-xs text-gray-500 text-center">
                    © {{ date('Y') }} POS MLK
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between px-4 md:px-6 py-3 md:py-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg md:text-xl font-bold text-dark-gray">@yield('header', 'POS MLK')</h1>
                    <div class="w-10"></div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="p-4 md:p-6 lg:p-8">
                    @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h3 class="font-bold text-red-800 mb-2">Terjadi Kesalahan</h3>
                        <ul class="text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800">
                        ✓ {{ session('success') }}
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 md:hidden hidden z-40" onclick="toggleSidebar()"></div>
    </div>

    <script>
        // Show overlay when sidebar is open
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        function updateOverlay() {
            if (!sidebar.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
            } else {
                overlay.classList.add('hidden');
            }
        }
        
        // Watch for sidebar changes
        const observer = new MutationObserver(updateOverlay);
        observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    </script>
    
    @stack('scripts')
</body>
</html>