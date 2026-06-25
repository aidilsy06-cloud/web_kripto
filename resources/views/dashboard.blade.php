@extends('layouts.app')

@section('page_title', 'Dashboard Ringkasan')

@section('content')
<div class="space-y-8">

    <!-- Overview Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Passwords -->
        <div class="glass rounded-2xl p-6 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-slate-500">Total Akun Tersimpan</span>
                <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                    <i class="fa-solid fa-folder-closed text-lg"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $total }}</span>
                <span class="text-xs text-slate-400 font-medium">Platform</span>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-mono font-medium">Enkripsi: Twofish-256 CBC</p>
        </div>

        <!-- Security Health Score -->
        <div class="glass rounded-2xl p-6 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-slate-500">Skor Keamanan Vault</span>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                    <i class="fa-solid fa-heart-pulse text-lg"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold @if($securityScore >= 80) text-emerald-600 @elseif($securityScore >= 50) text-amber-600 @else text-red-600 @endif">{{ $securityScore }}%</span>
                <span class="text-xs text-slate-400 font-medium">Kesehatan</span>
            </div>
            <div class="w-full bg-slate-200/60 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="h-1.5 rounded-full bg-gradient-to-r from-red-500 via-amber-500 to-emerald-500 transition-all duration-1000" style="width: {{ $securityScore }}%"></div>
            </div>
        </div>

        <!-- Reused Passwords -->
        <div class="glass rounded-2xl p-6 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-slate-500">Kebocoran & Duplikasi</span>
                <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold @if($reusedCount > 0) text-red-600 @else text-emerald-600 @endif">{{ $reusedCount }}</span>
                <span class="text-xs text-slate-400 font-medium">Reused</span>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">Dianalisis secara aman di memori</p>
        </div>

        <!-- Strong Passwords -->
        <div class="glass rounded-2xl p-6 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-slate-500">Password Sangat Kuat</span>
                <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                    <i class="fa-solid fa-shield-check text-lg"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $strong }}</span>
                <span class="text-xs text-slate-400 font-medium">/ {{ $total }} Akun</span>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">Memenuhi standar kekuatan modern</p>
        </div>
    </div>

    <!-- Main Section: Graph and Password Generator -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Password Strength Chart & Security recommendations -->
        <div class="glass rounded-3xl p-6 lg:col-span-1 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 mb-4"><i class="fa-solid fa-circle-pie text-blue-500 mr-2"></i>Analisis Kekuatan</h3>
                
                @if($total > 0)
                    <div class="relative w-44 h-44 mx-auto my-4">
                        <canvas id="strengthChart"></canvas>
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400">
                        <i class="fa-solid fa-box-open text-3xl mb-3 block"></i>
                        <p class="text-xs font-semibold">Belum ada data untuk dianalisis.</p>
                    </div>
                @endif
            </div>

            <!-- Audit Recommendations -->
            <div class="border-t border-slate-200 pt-4 space-y-3 mt-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Rekomendasi Keamanan</h4>
                
                @if($weak > 0)
                    <div class="flex items-start gap-2 text-xs text-red-700 bg-red-50 border border-red-200 p-2.5 rounded-xl">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                        <span>Terdapat <strong>{{ $weak }} password lemah</strong>. Disarankan untuk segera menggantinya dengan kombinasi acak.</span>
                    </div>
                @endif

                @if($reusedCount > 0)
                    <div class="flex items-start gap-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 p-2.5 rounded-xl">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-500"></i>
                        <span>Anda menggunakan password yang sama pada beberapa akun. Hindari ini untuk mencegah efek domino peretasan.</span>
                    </div>
                @endif

                @if($weak == 0 && $reusedCount == 0 && $total > 0)
                    <div class="flex items-start gap-2 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 p-2.5 rounded-xl">
                        <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500"></i>
                        <span>Hebat! Semua password Anda kuat dan unik. Tingkat keamanan vault Anda optimal.</span>
                    </div>
                @endif

                @if($total == 0)
                    <div class="flex items-start gap-2 text-xs text-slate-500 bg-slate-50 border border-slate-200 p-2.5 rounded-xl">
                        <i class="fa-solid fa-circle-info mt-0.5 text-blue-500"></i>
                        <span>Mulai tambahkan akun digital Anda ke dalam vault untuk memantau kesehatan keamanan.</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Interactive Password Generator -->
        <div class="glass rounded-3xl p-6 lg:col-span-2 flex flex-col justify-between" x-data="passwordGenerator()">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 mb-2"><i class="fa-solid fa-gears text-blue-500 mr-2"></i>Generator Password Acak</h3>
                <p class="text-xs text-slate-500 mb-6">Buat password yang kuat secara instan di peramban lokal Anda sebelum disimpan.</p>

                <!-- Password Output Box -->
                <div class="relative bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center justify-between gap-4 mb-6">
                    <span x-text="password" class="font-mono text-slate-800 text-base md:text-lg break-all select-all tracking-wide font-bold"></span>
                    
                    <button @click="copyPassword()" class="glass border border-white hover:border-blue-300 p-2.5 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-white transition-all flex items-center gap-1.5" title="Copy to clipboard">
                        <i :class="copied ? 'fa-solid fa-check text-emerald-500' : 'fa-solid fa-copy'"></i>
                        <span x-text="copied ? 'Disalin' : 'Salin'" class="text-xs font-bold"></span>
                    </button>
                </div>

                <!-- Control Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Length Slider -->
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-500 mb-2">
                            <span>PANJANG PASSWORD</span>
                            <span x-text="length + ' Karakter'" class="font-mono text-blue-600 font-bold"></span>
                        </div>
                        <input type="range" min="8" max="32" x-model="length" @input="generate()"
                               class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                    </div>

                    <!-- Strength Indicator -->
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-500 mb-2">
                            <span>KEKUATAN HASIL</span>
                            <span x-text="strengthText" :class="strengthClass" class="font-bold"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <div class="h-1.5 rounded-full" :class="strengthVal >= 1 ? 'bg-red-500' : 'bg-slate-200'"></div>
                            <div class="h-1.5 rounded-full" :class="strengthVal >= 2 ? 'bg-amber-500' : 'bg-slate-200'"></div>
                            <div class="h-1.5 rounded-full" :class="strengthVal >= 3 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                        </div>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="uppercase" @change="generate()" class="rounded border-slate-200 text-blue-600 focus:ring-blue-500/30">
                        <span class="text-xs text-slate-500 font-bold">A-Z (Kapital)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="lowercase" @change="generate()" class="rounded border-slate-200 text-blue-600 focus:ring-blue-500/30">
                        <span class="text-xs text-slate-500 font-bold">a-z (Kecil)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="numbers" @change="generate()" class="rounded border-slate-200 text-blue-600 focus:ring-blue-500/30">
                        <span class="text-xs text-slate-500 font-bold">0-9 (Angka)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="symbols" @change="generate()" class="rounded border-slate-200 text-blue-600 focus:ring-blue-500/30">
                        <span class="text-xs text-slate-500 font-bold">@#$% (Simbol)</span>
                    </label>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end gap-3 border-t border-slate-200 pt-4 mt-6">
                <button @click="generate()" class="glass border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-arrows-rotate text-blue-500"></i>
                    <span>Generate Ulang</span>
                </button>
                <a href="{{ route('credentials.index') }}?add=true" class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                    <i class="fa-solid fa-plus animate-pulse"></i>
                    <span>Simpan Ke Vault</span>
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Password Generator Logic -->
<script>
    function passwordGenerator() {
        return {
            password: '',
            length: 16,
            uppercase: true,
            lowercase: true,
            numbers: true,
            symbols: true,
            copied: false,
            
            init() {
                this.generate();
            },
            
            generate() {
                const upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                const lowerChars = 'abcdefghijklmnopqrstuvwxyz';
                const numberChars = '0123456789';
                const symbolChars = '!@#$%^&*()_+~`|}{[]:;?><,./-';
                
                let allowedChars = '';
                if (this.uppercase) allowedChars += upperChars;
                if (this.lowercase) allowedChars += lowerChars;
                if (this.numbers) allowedChars += numberChars;
                if (this.symbols) allowedChars += symbolChars;
                
                if (allowedChars === '') {
                    this.password = 'Pilih setidaknya 1 opsi';
                    return;
                }
                
                let genPassword = '';
                for (let i = 0; i < this.length; i++) {
                    const randomIndex = Math.floor(Math.random() * allowedChars.length);
                    genPassword += allowedChars[randomIndex];
                }
                this.password = genPassword;
                this.copied = false;
            },
            
            get strengthVal() {
                if (this.password === 'Pilih setidaknya 1 opsi') return 0;
                let val = 0;
                if (this.length >= 8) val = 1;
                if (this.length >= 12 && (this.uppercase || this.symbols) && this.numbers) val = 2;
                if (this.length >= 16 && this.uppercase && this.lowercase && this.numbers && this.symbols) val = 3;
                return val;
            },
            
            get strengthText() {
                const val = this.strengthVal;
                if (val === 1) return 'LEMAH';
                if (val === 2) return 'SEDANG';
                if (val === 3) return 'SANGAT KUAT';
                return 'TIDAK VALID';
            },
            
            get strengthClass() {
                const val = this.strengthVal;
                if (val === 1) return 'text-red-600';
                if (val === 2) return 'text-amber-600';
                if (val === 3) return 'text-emerald-600';
                return 'text-slate-400';
            },
            
            copyPassword() {
                if (this.password === 'Pilih setidaknya 1 opsi') return;
                navigator.clipboard.writeText(this.password);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        }
    }
</script>

<!-- Strength Chart JS Setup -->
@if($total > 0)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('strengthChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Lemah', 'Sedang', 'Kuat'],
                datasets: [{
                    data: [{{ $weak }}, {{ $medium }}, {{ $strong }}],
                    backgroundColor: [
                        '#dc2626', // Red
                        '#d97706', // Amber
                        '#059669'  // Emerald
                    ],
                    borderColor: 'rgba(255, 255, 255, 0.9)',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#475569', // slate-600
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11,
                                weight: 'bold'
                            },
                            padding: 15
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
