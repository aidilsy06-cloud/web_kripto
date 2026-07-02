@extends('layouts.app')

@section('page_title', 'Laporan Pengguna Masuk')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="glass p-6 rounded-2xl">
        <h3 class="text-xl font-bold text-slate-800">Inbox Laporan Masuk</h3>
        <p class="text-xs text-slate-500 mt-1">Daftar keluhan, bug, dan kendala teknis yang dikirimkan oleh pengguna sistem TulipCrypt.</p>
    </div>

    <!-- Reports Table -->
    <div class="glass rounded-2xl border border-white/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">ID Laporan</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Subjek / Judul Laporan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Kirim</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($reports as $report)
                        <tr class="hover:bg-white/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs">#TC-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $report->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $report->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700 max-w-xs truncate">{{ $report->title }}</div>
                                <div class="text-xs text-slate-400 max-w-xs truncate">{{ $report->description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($report->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-300">
                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                @elseif($report->status === 'in_progress')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">
                                        <span class="w-1 h-1 rounded-full bg-blue-500 animate-pulse"></span> Diproses
                                    </span>
                                @elseif($report->status === 'resolved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-300">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $report->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex px-4 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-bold border border-blue-200/50 shadow-sm transition-all">
                                    Tinjau Laporan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-2">
                                    <i class="fa-solid fa-inbox text-lg"></i>
                                </div>
                                <span>Tidak ada laporan gangguan masuk saat ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
