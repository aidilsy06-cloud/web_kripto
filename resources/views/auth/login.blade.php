<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TulipCrypt Twofish</title>
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
        <!-- Logo Header -->
        <div class="text-center mb-8 flex flex-col items-center gap-3">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-vault text-white text-2xl animate-pulse"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-slate-800 text-2xl tracking-wider">TULIPCRYPT</h1>
                <p class="text-sm text-slate-500">Twofish Encrypted Password Manager</p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="glass rounded-3xl p-8 relative overflow-hidden">
            <!-- Decorative light source -->
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>

            <h2 class="text-xl font-extrabold text-slate-800 mb-6">Selamat Datang Kembali</h2>

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm p-3 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Master Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" 
                           class="rounded border-slate-200 text-blue-600 focus:ring-blue-500/30">
                    <label for="remember" class="ml-2 text-xs text-slate-500 select-none">Ingat perangkat ini</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-450 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all text-sm mt-2 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-unlock-keyhole"></i>
                    <span>Masuk & Dekripsi Vault</span>
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-slate-500 border-t border-slate-200 pt-6">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-bold hover:underline transition-all">Daftar sekarang</a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-400 mt-6 font-mono">
            &copy; 2026 TulipCrypt. Menggunakan Twofish-256 Symmetric Cipher.
        </p>
    </div>

</body>
</html>
