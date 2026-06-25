<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - TulipCrypt Twofish</title>
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

    <div class="w-full max-w-md my-8">
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

        <!-- Register Card -->
        <div class="glass rounded-3xl p-8 relative overflow-hidden">
            <!-- Decorative light source -->
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>

            <h2 class="text-xl font-extrabold text-slate-800 mb-2">Daftar Akun Baru</h2>
            <p class="text-xs text-slate-500 mb-6">Akun Anda akan diamankan dengan master key Twofish.</p>

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="Nama Lengkap">
                    </div>
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Master Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="Min. 8 karakter">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Konfirmasi Master Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full bg-white/70 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm"
                               placeholder="Ulangi password">
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 flex gap-2.5 items-start text-slate-600">
                    <i class="fa-solid fa-shield-halved text-blue-500 mt-0.5"></i>
                    <p class="text-[10px] leading-normal font-medium">
                        <strong class="text-blue-700 block mb-0.5">Penting:</strong>
                        Kami akan menghasilkan salt acak unik 24-byte untuk akun Anda. Master password Anda akan digunakan bersama salt ini untuk menurunkan kunci Twofish-256 via PBKDF2. Jangan lupakan master password Anda!
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-405 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all text-sm mt-2 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Daftar & Buat Vault</span>
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500 border-t border-slate-200 pt-4">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-bold hover:underline transition-all">Masuk di sini</a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-400 mt-6 font-mono">
            &copy; 2026 TulipCrypt. Menggunakan Twofish-256 Symmetric Cipher.
        </p>
    </div>

</body>
</html>
