<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup 2FA - TulipCrypt</title>
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
    </style>
</head>
<body class="text-slate-700 antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Card Wrapper -->
    <div class="w-full max-w-lg bg-white/70 rounded-3xl p-8 shadow-2xl border border-white/60 relative overflow-hidden flex flex-col items-center">
        <!-- Accent Top Bar -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <!-- Shield Icon -->
        <div class="w-16 h-16 bg-indigo-100 border border-indigo-200 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm mb-6 mt-2">
            <i class="fa-solid fa-shield-halved text-2xl"></i>
        </div>

        <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight text-center">Autentikasi Dua Faktor (2FA)</h2>
        <p class="text-xs text-slate-400 text-center mt-2 max-w-md">Tingkatkan keamanan akun Anda menggunakan Google Authenticator untuk verifikasi login.</p>

        <!-- Step Instructions -->
        <div class="w-full mt-6 space-y-4 text-xs text-slate-600">
            <!-- Step 1 -->
            <div class="flex gap-3 items-start bg-white/40 p-3.5 rounded-xl border border-white/50">
                <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0">1</span>
                <div>
                    <strong class="text-slate-800">Unduh Aplikasi</strong>
                    <p class="text-slate-500 mt-0.5">Buka App Store (iOS) atau Google Play Store (Android), cari dan pasang aplikasi <strong class="text-slate-700">Google Authenticator</strong>.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4 items-center bg-white/40 p-4 rounded-2xl border border-white/50 justify-center">
                <div class="shrink-0 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code 2FA" class="w-36 h-36">
                </div>
                <div class="space-y-2">
                    <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0">2</span>
                    <div>
                        <strong class="text-slate-800">Pindai / Scan QR Code</strong>
                        <p class="text-slate-500 mt-0.5">Buka aplikasi Google Authenticator, pilih tombol tambah (+), lalu arahkan kamera ke QR Code di samping.</p>
                    </div>
                    <div class="border-t border-slate-100 pt-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kunci Rahasia (Manual)</span>
                        <code class="text-[11px] font-mono text-indigo-600 font-bold block mt-0.5 select-all">{{ $secret }}</code>
                    </div>
                </div>
            </div>
        </div>

        @if($errors->has('code'))
            <div class="w-full mt-6 p-4 rounded-2xl bg-red-50/70 border border-red-200/50 flex items-start gap-3">
                <i class="fa-solid fa-circle-xmark text-red-500 text-lg mt-0.5"></i>
                <div class="text-xs text-red-700 leading-relaxed font-bold">
                    {{ $errors->first('code') }}
                </div>
            </div>
        @endif

        <!-- Verification Form -->
        <form action="{{ route('google2fa.enable') }}" method="POST" class="w-full mt-6 space-y-4">
            @csrf

            <div class="space-y-2 bg-white/40 p-4 rounded-2xl border border-white/50">
                <label for="code" class="block text-xs font-bold text-slate-600 uppercase tracking-wider text-center">3. Konfirmasi Kode Authenticator</label>
                <p class="text-[10px] text-slate-400 text-center mb-2">Masukkan 6 digit kode yang tertera di aplikasi Google Authenticator Anda.</p>
                
                <input type="text" 
                       name="code" 
                       id="code" 
                       required 
                       placeholder="000 000" 
                       maxlength="6"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white/50 text-slate-800 placeholder-slate-300 text-xl font-bold tracking-[0.5em] text-center transition-all font-mono">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                <span>Aktifkan 2FA & Lanjutkan</span>
            </button>
        </form>

        <!-- Cancel / Setup Later -->
        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-xs text-slate-400 hover:text-slate-600 transition-colors font-bold flex items-center gap-1.5 justify-center">
                <span>Atur Nanti (Lewati)</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

</body>
</html>
