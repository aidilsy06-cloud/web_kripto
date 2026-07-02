<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - TulipCrypt</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
        }
        .glass {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body class="text-slate-700 antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Card Wrapper -->
    <div class="w-full max-w-md bg-white/70 rounded-3xl p-8 shadow-2xl border border-white/60 relative overflow-hidden flex flex-col items-center">
        <!-- Accent Top Bar -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

        <!-- Lock/Key Icon -->
        <div class="w-16 h-16 bg-blue-100 border border-blue-200 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm mb-6 mt-2">
            <i class="fa-solid fa-envelope-open-text text-2xl"></i>
        </div>

        <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight text-center">Verifikasi Email Anda</h2>
        <p class="text-xs text-slate-400 text-center mt-2 max-w-sm">Masukkan 6 digit kode OTP yang telah dikirim ke alamat email terdaftar Anda.</p>

        <!-- Debug Alert Box (Session Notification) -->
        @if(session('warning'))
            <div class="w-full mt-6 p-4 rounded-2xl bg-amber-50/70 border border-amber-200/50 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-lg mt-0.5"></i>
                <div class="text-xs text-slate-600 leading-relaxed font-semibold">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        @if($errors->has('otp_code'))
            <div class="w-full mt-6 p-4 rounded-2xl bg-red-50/70 border border-red-200/50 flex items-start gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500 text-lg mt-0.5"></i>
                <div class="text-xs text-red-700 leading-relaxed font-bold">
                    {{ $errors->first('otp_code') }}
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('register.verify.submit') }}" method="POST" class="w-full mt-6 space-y-6">
            @csrf

            <!-- OTP Input -->
            <div class="space-y-2">
                <label for="otp_code" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider text-center">Kode OTP 6-Digit</label>
                <input type="text" 
                       name="otp_code" 
                       id="otp_code" 
                       required 
                       maxlength="6"
                       placeholder="0 0 0 0 0 0" 
                       class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 bg-white/50 text-slate-800 placeholder-slate-300 text-2xl font-bold tracking-[1em] text-center transition-all font-mono">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                <span>Verifikasi OTP</span>
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </form>

        <!-- Cancel / Back -->
        <div class="mt-6 text-center">
            <a href="{{ route('logout') }}" class="text-xs text-slate-400 hover:text-red-500 transition-colors font-bold flex items-center gap-1.5 justify-center">
                <i class="fa-solid fa-power-off"></i>
                <span>Batalkan Registrasi</span>
            </a>
        </div>
    </div>

</body>
</html>
