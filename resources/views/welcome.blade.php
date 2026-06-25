<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TulipCrypt - Password Manager Twofish-256</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
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
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 10% 20%, rgba(59, 130, 246, 0.12) 0px, transparent 40%),
                radial-gradient(at 90% 80%, rgba(139, 92, 246, 0.12) 0px, transparent 40%);
            background-attachment: fixed;
        }
        .liquid-glass {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 
                0 20px 50px rgba(148, 163, 184, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            position: relative;
        }
        .liquid-glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }
        .glow-effect:hover {
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.45);
        }
    </style>
</head>
<body class="text-slate-700 antialiased min-h-screen flex flex-col font-sans">

    <!-- Top Navbar -->
    <header class="w-full h-20 flex items-center justify-between px-6 md:px-12 border-b border-white/40 bg-white/20 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-vault text-white text-lg"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-slate-800 text-lg tracking-wider">TulipCrypt</h1>
                <span class="text-[10px] text-blue-600 font-mono font-semibold block -mt-1">Twofish-256 Vault</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-500">
            <a href="#" class="text-slate-900 hover:text-blue-600 transition-colors">Home</a>
            <a href="#services" class="hover:text-blue-600 transition-colors">Service</a>
            <a href="#cryptography" class="hover:text-blue-600 transition-colors">Cryptography</a>
            <a href="#about" class="hover:text-blue-600 transition-colors">About Us</a>
        </nav>

        <!-- Access Portal Buttons -->
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold py-2 px-5 rounded-xl text-xs shadow-md transition-all">
                    Dashboard Vault
                </a>
            @else
                <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-950 font-bold text-xs py-2 px-4">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="bg-white hover:bg-white/80 border border-slate-200 text-slate-800 font-bold py-2 px-5 rounded-xl text-xs shadow-sm transition-all">
                    Daftar Baru
                </a>
            @endauth
        </div>
    </header>

    <!-- Hero Content -->
    <main class="flex-1 flex flex-col justify-center py-12 px-6 md:px-12 max-w-6xl mx-auto w-full">
        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-center">
            
            <!-- Left Info (Brand Copy) -->
            <div class="lg:col-span-3 space-y-6 text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-600 text-xs font-bold border border-blue-200">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Twofish Cryptographic Security</span>
                </div>
                
                <h2 class="text-3xl md:text-5xl font-black leading-tight text-slate-800 tracking-wide">
                    Kelola Password Anda Secara <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Aman & Terenkripsi</span>
                </h2>
                
                <p class="text-sm md:text-base text-slate-500 leading-relaxed">
                    TulipCrypt adalah platform Password Manager mandiri yang melindungi kredensial digital Anda menggunakan algoritma kunci simetris **Twofish-256**. Menghindari penyimpanan plaintext rentan pada file Excel, Word, atau catatan biasa.
                </p>

                <!-- Features list -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200"><i class="fa-solid fa-check"></i></span>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Twofish-256 CBC</h4>
                            <p class="text-xs text-slate-500 font-medium">Enkripsi tangguh 128-bit block.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200"><i class="fa-solid fa-check"></i></span>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">PBKDF2 SHA-256</h4>
                            <p class="text-xs text-slate-500 font-medium">Derivasi kunci dengan 10.000 iterasi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Info: Liquid Glass Portal Card -->
            <div class="lg:col-span-2">
                <div class="liquid-glass glow-effect rounded-3xl p-8 transition-all duration-300">
                    <div class="absolute -top-12 -left-12 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-12 -right-12 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl"></div>

                    <div class="text-center space-y-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center mx-auto shadow-md">
                            <i class="fa-solid fa-vault text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-800">Portal Vault TulipCrypt</h3>
                            <p class="text-xs text-slate-500 mt-1">Masuk untuk membuka password manager terenkripsi Anda</p>
                        </div>

                        <!-- Buttons Group -->
                        <div class="space-y-3 pt-2">
                            @auth
                                <p class="text-xs text-blue-600 font-bold font-mono">Halo, {{ Auth::user()->name }}! Sesi Anda siap.</p>
                                <a href="{{ route('dashboard') }}" 
                                   class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all text-xs flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-vault"></i>
                                    <span>Buka Dashboard Vault</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all text-xs flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-unlock-keyhole"></i>
                                    <span>Masuk ke Vault (Login)</span>
                                </a>
                                <div class="relative flex items-center justify-center my-3">
                                    <div class="border-t border-slate-200 w-full"></div>
                                    <span class="absolute bg-[#e2e8f0] px-2 text-[10px] text-slate-500 font-bold tracking-wider">ATAU</span>
                                </div>
                                <a href="{{ route('register') }}" 
                                   class="w-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 font-bold py-3 px-4 rounded-xl transition-all text-xs flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>Buat Akun Baru (Register)</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer Section -->
    <footer class="w-full py-6 text-center text-xs text-slate-400 border-t border-slate-200 bg-white/20 mt-12 font-mono">
        &copy; 2026 TulipCrypt. Dua Putaran Feistel Twofish-256 CBC.
    </footer>

</body>
</html>
