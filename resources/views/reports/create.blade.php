@extends('layouts.app')

@section('page_title', 'Buat Laporan Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="glass p-8 rounded-2xl border border-white/60 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-xl font-bold text-slate-800">Form Laporan Gangguan</h3>
            <p class="text-xs text-slate-500 mt-1">Berikan informasi yang sejelas-jelasnya agar administrator dapat menangani kendala Anda dengan cepat.</p>
        </div>

        <form action="{{ route('reports.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Subject/Title -->
            <div class="space-y-2">
                <label for="title" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Subjek / Judul Masalah</label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       required 
                       placeholder="Contoh: Gagal memuat visualizer Twofish atau error deskripsi password" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white/50 text-slate-800 placeholder-slate-400 text-sm transition-all">
                @error('title')
                    <p class="text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Deskripsi Lengkap Gangguan</label>
                <textarea name="description" 
                          id="description" 
                          rows="6" 
                          required 
                          placeholder="Jelaskan kronologi masalah, error yang muncul, atau langkah yang dilakukan sebelum gangguan terjadi secara detail..." 
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white/50 text-slate-800 placeholder-slate-400 text-sm transition-all resize-none"></textarea>
                @error('description')
                    <p class="text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Warning notice -->
            <div class="flex items-start gap-3 p-4 bg-blue-50/50 border border-blue-200/30 rounded-xl">
                <i class="fa-solid fa-circle-info text-blue-500 text-md mt-0.5"></i>
                <div class="text-xs text-slate-600 leading-relaxed">
                    <strong class="text-blue-700">Perhatian Keamanan:</strong> Jangan pernah mengirimkan master password Anda, kunci enkripsi, atau data sensitif mentah lainnya di dalam laporan gangguan ini demi keamanan data Anda.
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('reports.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-white/40 text-slate-600 text-sm font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
