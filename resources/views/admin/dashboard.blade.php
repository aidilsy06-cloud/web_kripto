@extends('layouts.app')

@section('page_title', 'Admin Dashboard')

@section('content')
<div class="space-y-8" x-data="{ showAddUser: false }">
    <!-- Welcome Header -->
    <div class="glass p-6 rounded-2xl">
        <h3 class="text-xl font-bold text-slate-800">Selamat Datang di Portal Pemantauan Admin</h3>
        <p class="text-xs text-slate-500 mt-1">Gunakan panel ini untuk mengawasi status sistem, memantau data pengguna terdaftar, serta menangani tiket laporan gangguan.</p>
    </div>

    <!-- Metrics grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Metric 1: Total Users -->
        <div class="glass p-6 rounded-2xl flex items-center gap-5 border border-white/60">
            <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider font-mono">Total Pengguna</span>
                <h4 class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $totalUsers }}</h4>
            </div>
        </div>

        <!-- Metric 2: Encrypted Passwords -->
        <div class="glass p-6 rounded-2xl flex items-center gap-5 border border-white/60">
            <div class="w-12 h-12 bg-indigo-100 border border-indigo-200 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider font-mono">Password Terenkripsi</span>
                <h4 class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $totalCredentials }}</h4>
            </div>
        </div>

        <!-- Metric 3: Pending Tickets -->
        <div class="glass p-6 rounded-2xl flex items-center gap-5 border border-white/60">
            <div class="w-12 h-12 bg-amber-100 border border-amber-200 rounded-xl flex items-center justify-center text-amber-600 shadow-sm">
                <i class="fa-solid fa-hourglass-half text-xl"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider font-mono">Laporan Pending</span>
                <h4 class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $totalReportsPending }}</h4>
            </div>
        </div>

        <!-- Metric 4: Solved Tickets -->
        <div class="glass p-6 rounded-2xl flex items-center gap-5 border border-white/60">
            <div class="w-12 h-12 bg-emerald-100 border border-emerald-200 rounded-xl flex items-center justify-center text-emerald-600 shadow-sm">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider font-mono">Laporan Selesai</span>
                <h4 class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $totalReportsResolved }}</h4>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-2xl border border-white/60 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-white/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h4 class="font-bold text-slate-800 text-lg">Daftar Pengguna Sistem</h4>
                <p class="text-xs text-slate-400 mt-0.5">Seluruh akun pengguna terdaftar beserta statistik penyimpanan mereka.</p>
            </div>
            <button @click="showAddUser = true" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nama & Email</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4 text-center">Password Tersimpan</th>
                        <th class="px-6 py-4 text-center">Jumlah Laporan</th>
                        <th class="px-6 py-4">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($users as $user)
                        <tr class="hover:bg-white/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs">#USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                <div class="text-xs text-slate-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-300">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-300">
                                        User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">{{ $user->credentials_count }}</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">{{ $user->reports_count }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $user->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini? Semua sandi terenkripsi dan laporan milik user ini akan ikut dihapus secara permanen!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200/50 text-xs font-bold transition-all shadow-sm">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium italic">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div x-show="showAddUser" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-start justify-center p-4 sm:p-10 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border-t-4 border-blue-500 overflow-hidden my-auto"
             @click.away="showAddUser = false">
            
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-blue-500"></i>
                    <span>Tambah Pengguna Baru</span>
                </h3>
                <button @click="showAddUser = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Name -->
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required placeholder="Nama Lengkap" 
                           class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm bg-slate-50/50">
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" name="email" id="email" required placeholder="nama@email.com" 
                           class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm bg-slate-50/50">
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Master Password</label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter" 
                           class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm bg-slate-50/50">
                </div>

                <!-- Role -->
                <div class="space-y-1.5">
                    <label for="role" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Hak Akses / Peran</label>
                    <select name="role" id="role" required 
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm bg-slate-50/50 font-semibold text-slate-700">
                        <option value="user">User Biasa (Regular User)</option>
                        <option value="admin">Administrator (Admin)</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="showAddUser = false" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all duration-200">
                        Tambah Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
