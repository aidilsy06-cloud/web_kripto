<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unlock Vault - TulipCrypt Twofish</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.12) 0px, transparent 40%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.12) 0px, transparent 40%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 10px 40px rgba(148, 163, 184, 0.08);
        }
    </style>
</head>
<body class="text-slate-700 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Unlock Card -->
        <div class="glass rounded-3xl p-8 relative overflow-hidden text-center">
            
            <!-- Lock Icon -->
            <div class="mx-auto w-16 h-16 rounded-2xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center mb-6 shadow-md">
                <i class="fa-solid fa-lock-open text-2xl animate-pulse"></i>
            </div>

            <h2 class="text-xl font-extrabold text-slate-800 mb-2">Buka Vault Anda</h2>
            <p class="text-xs text-slate-500 mb-6 font-medium leading-relaxed">
                Sesi kunci enkripsi Twofish Anda belum aktif atau telah kedaluwarsa. Silakan masukkan Master Password Anda untuk memuat kembali kunci enkripsi.
            </p>

            @if(session('warning'))
                <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-700 text-xs p-3 rounded-xl text-left flex gap-2.5 items-start">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs p-3 rounded-xl text-left flex gap-2.5 items-start">
                    <i class="fa-solid fa-circle-xmark mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('unlock.verify') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Password -->
                <div class="text-left">
                    <label for="password" class="block text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">Master Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="password" required autofocus
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="Masukkan Master Password">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all text-sm mt-2 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-vault"></i>
                    <span>Buka Vault & Derivasi Kunci</span>
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-slate-500 border-t border-slate-200 pt-6 flex justify-between">
                <span>Terhubung sebagai <strong>{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-500 font-bold hover:underline transition-all">
                        <i class="fa-solid fa-power-off mr-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-400 mt-6 font-mono">
            &copy; 2026 TulipCrypt. Menggunakan Twofish-256 Symmetric Cipher.
        </p>
    </div>

</body>
</html>
