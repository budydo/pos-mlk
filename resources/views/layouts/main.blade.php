<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS MLK')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        /* Ensure smooth sidebar transitions */
        .sidebar {
            transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar.hidden {
            display: none !important;
        }
        
        .sidebar.fixed {
            position: fixed !important;
        }
        
        /* Desktop: flex display */
        @media (min-width: 768px) {
            .sidebar.md\:flex {
                display: flex !important;
            }
        }
        
        .sidebar-link span {
            transition: opacity 300ms ease-in-out;
        }
        
        .sidebar-link span.hidden {
            display: none !important;
        }
        
        .sidebar-nav > div.hidden {
            display: none !important;
        }
        
        .sidebar-footer.hidden {
            display: none !important;
        }
        
        /* Smooth content transition */
        #main-content {
            transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    <script>
        // State tracking untuk sidebar collapse di desktop
        let isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        let isMobileView = window.innerWidth < 768;
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggle-icon');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            isMobileView = window.innerWidth < 768;
            
            if (isMobileView) {
                // Mobile: toggle sidebar visibility dengan slide
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('hidden');
                sidebarOverlay.classList.toggle('hidden');
                
                if (!sidebar.classList.contains('hidden')) {
                    sidebar.classList.add('inset-y-0', 'left-0', 'z-50');
                } else {
                    sidebar.classList.remove('inset-y-0', 'left-0', 'z-50');
                }
            } else {
                // Desktop: toggle sidebar width
                isSidebarCollapsed = !isSidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', isSidebarCollapsed);
                updateDesktopSidebarState();
            }
        }
        
        function updateDesktopSidebarState() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggle-icon');
            const sidebarHeader = document.querySelector('.sidebar-header a');
            
            if (isSidebarCollapsed) {
                // Collapsed state - benar-benar sembunyikan sidebar
                sidebar.classList.add('hidden');
                sidebar.classList.remove('w-64', 'lg:w-72', 'md:flex');
                
                // Hide text elements
                document.querySelectorAll('.sidebar-link span').forEach(el => {
                    el.classList.add('hidden');
                });
                document.querySelectorAll('.sidebar-nav > div').forEach(el => {
                    el.classList.add('hidden');
                });
                document.querySelector('.sidebar-footer').classList.add('hidden');
                
                if (sidebarHeader) {
                    sidebarHeader.classList.add('flex-col');
                }
                
                // Icon transformation
                toggleIcon.style.transform = 'scaleX(-1)';
            } else {
                // Expanded state - tampilkan sidebar penuh
                sidebar.classList.remove('hidden');
                sidebar.classList.add('w-64', 'lg:w-72', 'md:flex');
                
                // Show text elements
                document.querySelectorAll('.sidebar-link span').forEach(el => {
                    el.classList.remove('hidden');
                });
                document.querySelectorAll('.sidebar-nav > div').forEach(el => {
                    el.classList.remove('hidden');
                });
                document.querySelector('.sidebar-footer').classList.remove('hidden');
                
                if (sidebarHeader) {
                    sidebarHeader.classList.remove('flex-col');
                }
                
                // Reset icon
                toggleIcon.style.transform = 'scaleX(1)';
            }
        }
        
        // Initialize sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            isMobileView = window.innerWidth < 768;
            
            if (isMobileView) {
                // Mobile: hide sidebar by default
                sidebar.classList.add('fixed', 'hidden', 'inset-y-0', 'left-0', 'z-50');
                sidebar.classList.remove('md:flex');
                sidebarOverlay.classList.add('hidden');
                sidebarOverlay.classList.add('md:hidden');
            } else {
                // Desktop: show sidebar
                sidebar.classList.remove('fixed', 'hidden');
                sidebar.classList.add('md:flex');
                sidebarOverlay.classList.add('hidden');
                
                // Apply saved desktop state
                if (isSidebarCollapsed) {
                    updateDesktopSidebarState();
                }
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const isNowMobile = window.innerWidth < 768;
            
            if (isNowMobile && !isMobileView) {
                // Changed from desktop to mobile
                isMobileView = true;
                sidebar.classList.add('fixed', 'hidden');
                sidebar.classList.remove('md:flex', 'w-64', 'lg:w-72');
                sidebar.classList.add('w-64', 'lg:w-72', 'inset-y-0', 'left-0', 'z-50');
                sidebarOverlay.classList.add('hidden');
                
                // Reset sidebar to expanded state for next mobile open
                if (isSidebarCollapsed) {
                    isSidebarCollapsed = false;
                    document.querySelectorAll('.sidebar-link span').forEach(el => {
                        el.classList.remove('hidden');
                    });
                    document.querySelectorAll('.sidebar-nav > div').forEach(el => {
                        el.classList.remove('hidden');
                    });
                    document.querySelector('.sidebar-footer').classList.remove('hidden');
                    document.getElementById('toggle-icon').style.transform = 'scaleX(1)';
                }
            } else if (!isNowMobile && isMobileView) {
                // Changed from mobile to desktop
                isMobileView = false;
                sidebar.classList.remove('fixed', 'hidden', 'inset-y-0', 'left-0', 'z-50');
                sidebar.classList.add('md:flex');
                sidebarOverlay.classList.add('hidden');
                
                // Apply saved desktop state
                if (isSidebarCollapsed) {
                    updateDesktopSidebarState();
                }
            }
        });

        // Profile dropdown toggle and outside click close
        function toggleProfileDropdown() {
            const menu = document.getElementById('profile-dropdown-menu');
            const btn = document.getElementById('profile-dropdown-button');
            if (!menu || !btn) return;
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
            } else {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('profile-dropdown-menu');
            const btn = document.getElementById('profile-dropdown-button');
            if (!menu || !btn) return;
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</head>
<body class="bg-soft-green">
    <div class="flex h-screen bg-soft-green overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar hidden md:flex flex-col w-64 transition-all duration-300 ease-in-out lg:w-72 shadow-lg bg-white">
            <!-- Sidebar Header -->
            <div class="sidebar-header sticky top-0 bg-linear-to-r from-accent-green to-emerald-600">
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
                    Laporan
                </div>
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }} no-underline">
                    <span>📊 Laporan Penjualan</span>
                </a>
                <a href="{{ route('financial.index') }}" class="sidebar-link {{ request()->routeIs('financial.*') ? 'active' : '' }} no-underline">
                    <span>💹 Laporan Keuangan</span>
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
        <div id="main-content" class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ease-in-out">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between px-4 md:px-6 py-3 md:py-4">
                    <div class="flex items-center gap-2">
                        <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Toggle sidebar">
                            <svg id="toggle-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                    <h1 class="text-lg md:text-xl font-bold text-dark-gray">@yield('header', 'POS MLK')</h1>
                    <div class="relative">
                        <button id="profile-dropdown-button" onclick="toggleProfileDropdown()" class="flex items-center gap-2 p-2 rounded hover:bg-gray-100" aria-expanded="false" aria-haspopup="true">
                            <span class="text-sm text-gray-600">{{ auth()->user()->name ?? 'Guest' }}</span>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="profile-dropdown-menu" class="hidden absolute right-0 mt-2 w-44 bg-white border rounded shadow-md z-50">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
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
    
    @stack('scripts')
</body>
</html>