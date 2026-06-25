<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TulipCrypt - Twofish Password Manager</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        theme: {
                            blue: '#2563eb',
                            violet: '#7c3aed',
                            emerald: '#059669',
                            red: '#dc2626',
                            slate: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.12) 0px, transparent 50%);
            background-attachment: fixed;
        }
        .glass {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.02),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }
        .glass-hover:hover {
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.06);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.2);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.25);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="text-slate-700 antialiased min-h-screen flex flex-col font-sans">

    <!-- Toast Notification -->
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm">
        @if(session('success'))
            <div class="glass border-emerald-500/20 text-emerald-600 p-4 rounded-xl shadow-xl flex items-center gap-3 animate-bounce">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <div class="text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="glass border-red-500/20 text-red-600 p-4 rounded-xl shadow-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i>
                <div class="text-sm font-semibold">{{ session('error') }}</div>
            </div>
        @endif
        @if(session('warning'))
            <div class="glass border-amber-500/20 text-amber-600 p-4 rounded-xl shadow-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-lg"></i>
                <div class="text-sm font-semibold">{{ session('warning') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Outer Wrapper -->
    <div class="flex flex-1 overflow-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-40 w-64 glass border-r border-white/50 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static">
            
            <!-- Logo Section -->
            <div class="h-20 flex items-center px-6 border-b border-white/30 gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-vault text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-slate-800 text-lg tracking-wider">TulipCrypt</h1>
                    <span class="text-xs text-blue-600 font-mono font-semibold tracking-tight">Twofish Manager</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 gap-3 group {{ Request::routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500/10 to-indigo-500/5 text-blue-600 border-l-4 border-blue-500 shadow-sm' : 'text-slate-500 hover:bg-white/40 hover:text-slate-800' }}">
                    <i class="fa-solid fa-chart-line text-lg {{ Request::routeIs('dashboard') ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }}"></i>
                    <span class="font-bold">Dashboard</span>
                </a>

                <a href="{{ route('credentials.index') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 gap-3 group {{ Request::routeIs('credentials.index') ? 'bg-gradient-to-r from-blue-500/10 to-indigo-500/5 text-blue-600 border-l-4 border-blue-500 shadow-sm' : 'text-slate-500 hover:bg-white/40 hover:text-slate-800' }}">
                    <i class="fa-solid fa-key text-lg {{ Request::routeIs('credentials.index') ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }}"></i>
                    <span class="font-bold">My Passwords</span>
                </a>

                <a href="{{ route('playground') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 gap-3 group {{ Request::routeIs('playground') ? 'bg-gradient-to-r from-blue-500/10 to-indigo-500/5 text-blue-600 border-l-4 border-blue-500 shadow-sm' : 'text-slate-500 hover:bg-white/40 hover:text-slate-800' }}">
                    <i class="fa-solid fa-circle-nodes text-lg {{ Request::routeIs('playground') ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }}"></i>
                    <span class="font-bold">Twofish Visualizer</span>
                </a>
            </nav>

            <!-- Bottom Session Info -->
            <div class="p-4 border-t border-white/30 bg-white/20">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 font-semibold">Sesi Kunci Twofish:</span>
                    @if(session()->has('twofish_key'))
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Locked
                        </span>
                    @endif
                </div>
                <div class="text-[10px] text-slate-400 font-mono break-all line-clamp-1 hover:line-clamp-none transition-all duration-200">
                    Key: {{ session('twofish_key') ? substr(session('twofish_key'), 0, 16) . '...' : 'Locked' }}
                </div>
            </div>

            <!-- User Profile Section -->
            <div class="p-4 border-t border-white/30 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border border-blue-200">
                        <span class="text-sm font-bold text-blue-600">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</span>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email ?? 'user@example.com' }}</p>
                    </div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 rounded-lg hover:bg-white/40 transition-all" title="Logout & Hapus Kunci">
                        <i class="fa-solid fa-power-off text-md"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div id="main-content-area" class="flex-1 flex flex-col overflow-hidden">
            <!-- Header (Mobile Top bar) -->
            <header class="h-20 border-b border-white/30 flex items-center justify-between px-6 lg:px-8 bg-white/30 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-600 hover:text-slate-800 rounded-lg lg:hidden hover:bg-white/40">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">@yield('page_title', 'Dashboard')</h2>
                        <p class="text-xs text-slate-500">Kriptografi Twofish Symmetric Encrypted Password Vault</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Session Active Timer Widget -->
                    @if(session()->has('twofish_key'))
                        <div class="hidden md:flex items-center gap-2 glass px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 border border-blue-200">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Sesi Vault Aktif</span>
                        </div>
                    @endif
                    
                    <a href="{{ route('playground') }}" class="glass hover:bg-white/80 px-4 py-2 rounded-xl text-xs font-bold text-slate-600 transition-all flex items-center gap-2 border border-white">
                        <i class="fa-solid fa-terminal text-blue-600"></i>
                        <span>Sandbox</span>
                    </a>
                </div>
            </header>

            <!-- Page Body -->
            <main id="main-content-body" class="flex-1 overflow-y-auto p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
