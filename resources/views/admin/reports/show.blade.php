@extends('layouts.app')

@section('page_title', 'Tinjau Laporan Gangguan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Inbox Laporan</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main ticket details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Ticket Info Card -->
            <div class="glass p-6 rounded-2xl border border-white/60 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <span class="text-[10px] text-slate-400 font-mono font-bold uppercase tracking-wider">Tiket Gangguan #TC-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-1">{{ $report->title }}</h3>
                </div>

                <div class="text-sm text-slate-600 leading-relaxed bg-white/30 p-4 rounded-xl border border-white/50">
                    <p class="font-semibold text-xs text-slate-400 mb-2 uppercase tracking-wider font-mono">Penjelasan Pengguna:</p>
                    <p class="whitespace-pre-line">{{ $report->description }}</p>
                </div>

                <!-- Reply History if exists -->
                @if($report->admin_reply)
                    <div class="bg-gradient-to-r from-blue-50/50 to-indigo-50/50 border border-blue-200/50 p-4 rounded-xl space-y-2">
                        <div class="flex items-center gap-2 text-blue-700">
                            <i class="fa-solid fa-user-tie text-sm"></i>
                            <span class="text-xs font-extrabold uppercase tracking-wider font-mono">Balasan Terakhir Anda:</span>
                        </div>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $report->admin_reply }}</p>
                        <p class="text-[10px] text-slate-400 text-right font-mono">Diperbarui: {{ $report->updated_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                @endif
            </div>

            <!-- Response Form -->
            <div class="glass p-6 rounded-2xl border border-white/60 space-y-4">
                <h4 class="font-bold text-slate-800 text-md">Berikan Tanggapan / Update Status</h4>

                <form action="{{ route('admin.reports.reply', $report->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Status Selector -->
                    <div class="space-y-2">
                        <label for="status" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Perbarui Status</label>
                        <select name="status" 
                                id="status" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white/50 text-slate-800 text-sm transition-all font-semibold">
                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Antrean)</option>
                            <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses (In Progress)</option>
                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                        </select>
                    </div>

                    <!-- Reply Textarea -->
                    <div class="space-y-2">
                        <label for="admin_reply" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Tulis Balasan / Catatan Penanganan</label>
                        <textarea name="admin_reply" 
                                  id="admin_reply" 
                                  rows="5" 
                                  placeholder="Tuliskan solusi, tanggapan, atau perkembangan penanganan kendala yang dialami pengguna..." 
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white/50 text-slate-800 text-sm transition-all resize-none">{{ $report->admin_reply }}</textarea>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info Panel -->
        <div class="space-y-6">
            <div class="glass p-6 rounded-2xl border border-white/60 space-y-4 text-sm text-slate-600">
                <h4 class="font-bold text-slate-800 text-md border-b border-slate-100 pb-2">Informasi Pelapor</h4>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nama Lengkap</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $report->user->name ?? 'User Terhapus' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Alamat Email</span>
                        <p class="font-mono text-slate-700 mt-0.5 break-all">{{ $report->user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Hak Akses</span>
                        <p class="mt-0.5">
                            @if(($report->user->role ?? 'user') === 'admin')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-300">Admin</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-300">User</span>
                            @endif
                        </p>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Kredensial Terimpan</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $report->user->credentials()->count() ?? 0 }} Sandi</p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Terdaftar Sejak</span>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $report->user ? $report->user->created_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
