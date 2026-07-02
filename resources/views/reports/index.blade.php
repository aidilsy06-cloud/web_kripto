@extends('layouts.app')

@section('page_title', 'Laporan Gangguan')

@section('content')
<div class="space-y-6">
    <!-- Header Block -->
    <div class="glass p-6 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Tiket Laporan Gangguan</h3>
            <p class="text-xs text-slate-500 mt-1">Laporkan kendala teknis atau masalah keamanan yang Anda alami pada sistem TulipCrypt.</p>
        </div>
        <a href="{{ route('reports.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Buat Laporan Baru</span>
        </a>
    </div>

    <!-- Reports Listing -->
    <div class="space-y-4">
        @forelse($reports as $report)
            <div class="glass p-6 rounded-2xl border border-white/60 space-y-4 transition-all duration-300 hover:shadow-md">
                <!-- Top details -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-800 text-lg">{{ $report->title }}</h4>
                        <span class="text-[11px] text-slate-400 font-mono">ID Laporan: #TC-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }} &bull; Dikirim: {{ $report->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div>
                        @if($report->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                            </span>
                        @elseif($report->status === 'in_progress')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Sedang Diproses
                            </span>
                        @elseif($report->status === 'resolved')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="text-sm text-slate-600 leading-relaxed bg-white/30 p-4 rounded-xl border border-white/50">
                    <p class="font-semibold text-xs text-slate-400 mb-1 uppercase tracking-wider font-mono">Detail Gangguan:</p>
                    <p class="whitespace-pre-line">{{ $report->description }}</p>
                </div>

                <!-- Admin Response -->
                @if($report->admin_reply)
                    <div class="bg-gradient-to-r from-blue-50/50 to-indigo-50/50 border border-blue-200/50 p-4 rounded-xl space-y-2">
                        <div class="flex items-center gap-2 text-blue-700">
                            <i class="fa-solid fa-user-tie text-sm"></i>
                            <span class="text-xs font-extrabold uppercase tracking-wider font-mono">Tanggapan Administrator:</span>
                        </div>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $report->admin_reply }}</p>
                        <p class="text-[10px] text-slate-400 text-right font-mono">Dibalas pada: {{ $report->updated_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                @elseif($report->status === 'pending')
                    <div class="flex items-center gap-2 text-amber-600 bg-amber-50/50 border border-amber-200/30 p-3 rounded-xl text-xs font-medium">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <span>Laporan Anda sedang mengantre untuk diperiksa oleh Administrator.</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-blue-600 bg-blue-50/50 border border-blue-200/30 p-3 rounded-xl text-xs font-medium">
                        <i class="fa-solid fa-spinner animate-spin"></i>
                        <span>Administrator sedang menangani laporan gangguan ini.</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="glass p-12 rounded-2xl text-center space-y-4">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto border border-blue-100">
                    <i class="fa-solid fa-clipboard-check text-blue-500 text-2xl"></i>
                </div>
                <div class="max-w-md mx-auto">
                    <h4 class="font-bold text-slate-800 text-lg">Tidak ada laporan gangguan</h4>
                    <p class="text-slate-500 text-sm mt-1">Bagus! Sistem Anda berjalan lancar. Jika Anda menemukan bug atau gangguan keamanan, laporkan segera ke admin.</p>
                </div>
                <a href="{{ route('reports.create') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200">
                    Kirim Laporan Pertama
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
