@extends('layouts.app')

@section('page_title', 'My Passwords Vault')

@section('content')
<div x-data="vaultManager()" x-init="checkUrlParams()" class="space-y-6">

    <!-- Top Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
        <!-- Search bar -->
        <form action="{{ route('credentials.index') }}" method="GET" class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full bg-white border border-slate-200 rounded-2xl py-3 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-medium text-sm shadow-sm"
                   placeholder="Cari platform atau username...">
            @if($search)
                <a href="{{ route('credentials.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-circle-xmark"></i>
                </a>
            @endif
        </form>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Export Backup Button -->
            <a href="{{ route('credentials.backup.export') }}" 
               class="px-4 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 hover:border-slate-300 text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-download text-blue-500"></i>
                <span>Ekspor Backup</span>
            </a>

            <!-- Import Backup Trigger -->
            <button @click="openImportModal()" 
                    class="px-4 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 hover:border-slate-300 text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-upload text-indigo-500"></i>
                <span>Impor Backup</span>
            </button>

            <!-- Add Password Button -->
            <button @click="openAddModal()" 
                    class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold py-3 px-6 rounded-2xl shadow-md transition-all text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Akun Baru</span>
            </button>
        </div>
    </div>

    <!-- Credentials Table -->
    <div class="glass rounded-3xl overflow-hidden border border-white/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="px-6 py-4">Platform</th>
                        <th class="px-6 py-4">Username / Email</th>
                        <th class="px-6 py-4">Twofish Ciphertext (Password)</th>
                        <th class="px-6 py-4 text-center">Kekuatan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($credentials as $cred)
                        <tr class="hover:bg-white/40 transition-colors duration-150 group">
                            <!-- Platform -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                                        <i class="fa-solid fa-globe text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-800 block">{{ $cred->platform_name }}</span>
                                        @if($cred->platform_url)
                                            <a href="{{ $cred->platform_url }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-bold mt-0.5">
                                                <span class="truncate max-w-[150px]">{{ parse_url($cred->platform_url, PHP_URL_HOST) }}</span>
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">Tidak ada URL</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Username -->
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $cred->username }}
                            </td>

                            <!-- Ciphertext -->
                            <td class="px-6 py-4 font-mono text-xs text-slate-400 max-w-[200px]">
                                <div class="flex items-center gap-2">
                                    <span class="truncate">{{ $cred->password_encrypted }}</span>
                                    <button @click="copyText('{{ $cred->password_encrypted }}')" 
                                            class="text-slate-400 hover:text-slate-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                            title="Salin Ciphertext">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Strength -->
                            <td class="px-6 py-4 text-center">
                                @if($cred->strength === 'strong')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Kuat</span>
                                @elseif($cred->strength === 'medium')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">Sedang</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">Lemah</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Decrypt/Show -->
                                    <button @click="decryptPassword({{ $cred->id }}, '{{ $cred->platform_name }}', '{{ $cred->username }}')" 
                                            class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl border border-blue-100 hover:border-blue-500 transition-all shadow-sm"
                                            title="Dekripsi & Tampilkan">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button @click="openEditModal({{ json_encode($cred) }})"
                                            class="p-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl border border-indigo-100 hover:border-indigo-500 transition-all shadow-sm"
                                            title="Edit Akun">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('credentials.destroy', $cred->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini dari vault?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl border border-red-100 hover:border-red-500 transition-all shadow-sm"
                                                title="Hapus Akun">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-key text-4xl mb-3 block text-slate-300"></i>
                                <p class="font-bold text-slate-500">Vault Kosong</p>
                                <p class="text-xs text-slate-400 mt-1">Anda belum menyimpan password apapun menggunakan algoritma Twofish.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ADD PASSWORD MODAL -->
    <div x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex justify-center items-start p-4 sm:p-10 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div class="bg-white border-t-4 border-t-blue-600 border-x border-b border-slate-100 w-full max-w-lg rounded-3xl p-8 relative overflow-hidden shadow-2xl my-auto" @click.away="addModalOpen = false">

            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Tambah Password Baru</h3>
                    <p class="text-xs text-slate-500 font-semibold">Menggunakan enkripsi Twofish-256 CBC</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mb-6 font-semibold border-b border-slate-100 pb-4">Password akan otomatis dienkripsi dengan Twofish sebelum disimpan secara aman.</p>

            <form action="{{ route('credentials.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nama Platform</label>
                    <input type="text" name="platform_name" required placeholder="Contoh: Instagram, Gmail, Shopee"
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Platform URL (Opsional)</label>
                    <input type="url" name="platform_url" placeholder="https://instagram.com"
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Username atau Email Akun</label>
                    <input type="text" name="username" required placeholder="aidil123"
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Password Asli (Plaintext)</label>
                    <input type="text" name="password" required placeholder="Masukkan password asli Anda"
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-mono font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Catatan Tambahan (Opsional, ikut dienkripsi)</label>
                    <textarea name="notes" placeholder="Catatan rahasia lainnya..." rows="2"
                              class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                    <button type="button" @click="addModalOpen = false" class="border border-slate-200 hover:bg-slate-50 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 transition-all">Batal</button>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 hover:shadow-lg hover:shadow-blue-500/20 hover:-translate-y-0.5 active:translate-y-0">Simpan & Enkripsi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT PASSWORD MODAL -->
    <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex justify-center items-start p-4 sm:p-10 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div class="bg-white border-t-4 border-t-indigo-600 border-x border-b border-slate-100 w-full max-w-lg rounded-3xl p-8 relative overflow-hidden shadow-2xl my-auto" @click.away="editModalOpen = false">

            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Edit Akun Vault</h3>
                    <p class="text-xs text-slate-500 font-semibold">Perbarui data kredensial terenkripsi</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mb-6 font-semibold border-b border-slate-100 pb-4">Ubah data akun di bawah ini. Biarkan password kosong jika tidak ingin diubah.</p>

            <form :action="editActionUrl" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nama Platform</label>
                    <input type="text" name="platform_name" x-model="editData.platform_name" required
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Platform URL</label>
                    <input type="url" name="platform_url" x-model="editData.platform_url"
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Username atau Email Akun</label>
                    <input type="text" name="username" x-model="editData.username" required
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Password Asli Baru (Kosongkan jika tidak diganti)</label>
                    <input type="text" name="password" placeholder="Masukkan password baru untuk mengganti password saat ini"
                           class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-mono font-semibold shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Catatan Baru (Biarkan seperti semula jika tidak diubah)</label>
                    <textarea name="notes" placeholder="Catatan rahasia baru... (Harus didekripsi untuk melihat catatan sebelumnya)" rows="2"
                              class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold shadow-inner"></textarea>
                    <p class="text-[10px] text-slate-400 font-semibold mt-1">Catatan: Untuk keamanan, Anda harus menulis ulang catatan baru. Catatan lama diabaikan jika kolom ini diisi.</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                    <button type="button" @click="editModalOpen = false" class="border border-slate-200 hover:bg-slate-50 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 transition-all">Batal</button>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 hover:shadow-lg hover:shadow-blue-500/20 hover:-translate-y-0.5 active:translate-y-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CRYPTOGRAPHY DECRYPTION PROCESS MODAL (WOW Factor with Master Password Verification) -->
    <div x-show="decryptModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex justify-center items-start p-4 sm:p-10 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div class="bg-white border-t-4 border-t-blue-600 border-x border-b border-slate-100 w-full max-w-xl rounded-3xl p-8 relative overflow-hidden shadow-2xl my-auto" @click.away="decryptModalOpen = false">
            
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fa-solid fa-microchip text-lg animate-pulse"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Dekripsi Algoritma Twofish</h3>
                        <p class="text-xs text-slate-500 font-semibold">Proses dekripsi simetris aman</p>
                    </div>
                </div>
                <span class="text-xs font-mono text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100 font-bold" x-text="decryptState.platform"></span>
            </div>

            <!-- Master Password Verification Form (Revision 2) -->
            <div x-show="!decryptState.passwordVerified" class="space-y-5 py-4">
                <div class="text-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm mx-auto mb-3">
                        <i class="fa-solid fa-lock-open text-xl"></i>
                    </div>
                    <p class="text-sm font-extrabold text-slate-800">Verifikasi Keamanan Vault</p>
                    <p class="text-xs text-slate-500 mt-1 font-semibold">Masukkan Master Password akun Anda untuk mendekripsi akun ini.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Master Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" x-model="decryptMasterPassword" placeholder="Masukkan Master Password Anda" @keyup.enter="confirmPasswordAndDecrypt()"
                               class="w-full bg-slate-50 border border-slate-200/80 focus:border-blue-500 rounded-xl py-3 pl-10 pr-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all font-semibold text-sm shadow-sm">
                    </div>
                    <div x-show="decryptState.errorMsg" class="text-red-600 text-xs mt-2 block font-bold" x-text="decryptState.errorMsg"></div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                    <button type="button" @click="decryptModalOpen = false" class="border border-slate-200 hover:bg-slate-50 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 transition-all">Batal</button>
                    <button type="button" @click="confirmPasswordAndDecrypt()" :disabled="!decryptMasterPassword"
                            class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md transition-all disabled:opacity-50 hover:shadow-lg hover:shadow-blue-500/20 hover:-translate-y-0.5 active:translate-y-0">
                        Verifikasi
                    </button>
                </div>
            </div>

            <div x-show="decryptState.passwordVerified">
                <!-- Terminal Visualizer for cryptographic logs (Keep it dark for contrast!) -->
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-4 font-mono text-[11px] text-slate-300 leading-relaxed mb-6 max-h-64 overflow-y-auto shadow-inner">
                    <template x-for="log in decryptLogs">
                        <div class="mb-1">
                            <span class="text-blue-400 font-bold">$</span> 
                            <span x-text="log.text" :class="log.color"></span>
                        </div>
                    </template>
                    <!-- Loading indicator in terminal -->
                    <div x-show="decryptState.running" class="flex items-center gap-2 text-blue-400 mt-2">
                        <span class="animate-ping w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Menjalankan proses Twofish...</span>
                    </div>
                </div>

                <!-- Plaintext Result (Revealed only after logs finish) -->
                <div x-show="!decryptState.running && decryptState.success" class="space-y-4 bg-slate-50 border border-slate-200/60 rounded-2xl p-5 animate-fade-in">
                    <!-- Username -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Username / Email</span>
                        <span x-text="decryptState.username" class="text-slate-800 font-extrabold text-sm"></span>
                    </div>

                    <!-- Password -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Password Asli</span>
                        <div class="flex items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
                            <span x-show="decryptState.revealed" x-text="decryptState.plaintext" class="font-mono text-slate-800 text-base select-all tracking-wide font-extrabold"></span>
                            <span x-show="!decryptState.revealed" class="font-mono text-slate-400 text-base tracking-widest">••••••••••••</span>
                            
                            <div class="flex gap-2">
                                <button @click="decryptState.revealed = !decryptState.revealed" class="text-slate-500 hover:text-slate-800 transition-all text-xs font-semibold p-2 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 shadow-sm">
                                    <i :class="decryptState.revealed ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                </button>
                                <button @click="copyText(decryptState.plaintext, 'pwd')" class="text-slate-500 hover:text-blue-600 transition-all text-xs font-bold p-2 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 shadow-sm flex items-center gap-1">
                                    <i :class="decryptState.pwdCopied ? 'fa-solid fa-check text-emerald-500' : 'fa-solid fa-copy'"></i>
                                    <span x-text="decryptState.pwdCopied ? 'Disalin' : 'Salin'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div x-show="decryptState.notes">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Catatan Rahasia Tambahan</span>
                        <div class="bg-white border border-slate-200 rounded-xl p-3.5 text-xs text-slate-600 leading-relaxed whitespace-pre-line relative shadow-sm">
                            <p x-text="decryptState.notes" class="font-semibold"></p>
                            <button @click="copyText(decryptState.notes, 'note')" class="absolute top-3 right-3 text-xs text-slate-400 hover:text-slate-700">
                                <i :class="decryptState.noteCopied ? 'fa-solid fa-check text-emerald-500' : 'fa-solid fa-copy'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-6">
                    <button type="button" @click="decryptModalOpen = false" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md">Tutup Vault</button>
                </div>
            </div>
        </div>
    </div>

    <!-- IMPORT BACKUP MODAL -->
    <div x-show="importModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex justify-center items-start p-4 sm:p-10 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div class="bg-white border-t-4 border-t-indigo-600 border-x border-b border-slate-100 w-full max-w-md rounded-3xl p-8 relative overflow-hidden shadow-2xl my-auto" @click.away="importModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-upload text-indigo-500"></i>
                    <span>Impor Backup Terenkripsi</span>
                </h3>
                <button @click="importModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('credentials.backup.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">File Backup (.tulipbackup)</label>
                    <input type="file" name="backup_file" required accept=".tulipbackup" 
                           class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl p-1 bg-slate-50/50 focus:outline-none">
                </div>

                <div class="flex items-start gap-3 p-4 bg-indigo-50/50 border border-indigo-200/30 rounded-xl">
                    <i class="fa-solid fa-shield-halved text-indigo-500 text-md mt-0.5"></i>
                    <div class="text-xs text-slate-600 leading-relaxed">
                        <strong class="text-indigo-700">Dekripsi Sesi Otomatis:</strong> File backup dienkripsi penuh secara simetris. Pengimporan menggunakan kunci sesi aktif Anda untuk memproses dan menyimpannya kembali.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="importModalOpen = false" class="border border-slate-200 hover:bg-slate-50 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 transition-all">Batal</button>
                    <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md">Impor Sekarang</button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Alpine JS Controller -->
<script>
    function vaultManager() {
        return {
            addModalOpen: false,
            editModalOpen: false,
            decryptModalOpen: false,
            importModalOpen: false,
            
            editData: {
                id: '',
                platform_name: '',
                platform_url: '',
                username: '',
            },
            editActionUrl: '',

            decryptMasterPassword: '',
            decryptTargetId: null,

            decryptState: {
                platform: '',
                username: '',
                plaintext: '',
                notes: '',
                running: false,
                success: false,
                revealed: false,
                pwdCopied: false,
                noteCopied: false,
                passwordVerified: false,
                errorMsg: '',
            },

            decryptLogs: [],

            init() {
                this.checkUrlParams();
                
                // Watch modal states to dynamically elevate main content z-index
                this.$watch('addModalOpen', value => this.toggleOverlay());
                this.$watch('editModalOpen', value => this.toggleOverlay());
                this.$watch('decryptModalOpen', value => this.toggleOverlay());
                this.$watch('importModalOpen', value => this.toggleOverlay());
            },

            toggleOverlay() {
                const isOpen = this.addModalOpen || this.editModalOpen || this.decryptModalOpen || this.importModalOpen;
                const mainContent = document.getElementById('main-content-area');
                const mainBody = document.getElementById('main-content-body');
                if (mainContent) {
                    if (isOpen) {
                        mainContent.classList.add('relative', 'z-50');
                        if (mainBody) mainBody.classList.add('relative', 'z-50');
                    } else {
                        mainContent.classList.remove('relative', 'z-50');
                        if (mainBody) mainBody.classList.remove('relative', 'z-50');
                    }
                }
            },

            checkUrlParams() {
                // If url has ?add=true, trigger the add modal automatically
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('add') === 'true') {
                    this.openAddModal();
                }
            },

            openAddModal() {
                this.addModalOpen = true;
            },

            openImportModal() {
                this.importModalOpen = true;
            },

            openEditModal(cred) {
                this.editData = {
                    id: cred.id,
                    platform_name: cred.platform_name,
                    platform_url: cred.platform_url,
                    username: cred.username,
                };
                this.editActionUrl = `/credentials/${cred.id}`;
                this.editModalOpen = true;
            },

            decryptPassword(id, platform, username) {
                this.decryptModalOpen = true;
                this.decryptTargetId = id;
                this.decryptMasterPassword = '';
                this.decryptLogs = [];
                
                this.decryptState.passwordVerified = false;
                this.decryptState.errorMsg = '';
                this.decryptState.running = false;
                this.decryptState.success = false;
                this.decryptState.revealed = false;
                this.decryptState.platform = platform;
                this.decryptState.username = username;
                this.decryptState.plaintext = '';
                this.decryptState.notes = '';
                this.decryptState.pwdCopied = false;
                this.decryptState.noteCopied = false;
            },

            async confirmPasswordAndDecrypt() {
                if (!this.decryptMasterPassword) return;

                this.decryptState.errorMsg = '';
                this.decryptState.passwordVerified = true;
                this.decryptState.running = true;
                this.decryptLogs = [];

                // Log steps simulated
                this.addLog("Memulai request dekripsi untuk platform: " + this.decryptState.platform, "text-slate-400");
                await this.sleep(400);

                this.addLog("Mengambil data ciphertext dan IV dari SQLite...", "text-slate-400");
                await this.sleep(400);

                this.addLog("Mengirim data & Master Password ke controller...", "text-slate-400");
                
                try {
                    const response = await fetch(`/credentials/${this.decryptTargetId}/decrypt`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            master_password: this.decryptMasterPassword
                        })
                    });

                    const data = await response.json();
                    
                    if (response.status === 401) {
                        this.addLog("[ERROR] Kunci enkripsi Twofish di sesi server kedaluwarsa. Vault terkunci!", "text-red-400 font-bold");
                        this.decryptState.running = false;
                        await this.sleep(1000);
                        window.location.href = "{{ route('unlock') }}";
                        return;
                    }

                    if (response.status === 403) {
                        this.addLog("[ERROR] Verifikasi gagal: Master Password salah!", "text-red-400 font-bold");
                        this.decryptState.running = false;
                        await this.sleep(1200);
                        // Flip back to password prompt
                        this.decryptState.passwordVerified = false;
                        this.decryptState.errorMsg = data.error || 'Master Password salah.';
                        return;
                    }

                    if (!response.ok || data.error) {
                        this.addLog("[ERROR] Gagal mendekripsi: " + (data.error || 'Unknown error'), "text-red-400 font-bold");
                        this.decryptState.running = false;
                        await this.sleep(1200);
                        this.decryptState.passwordVerified = false;
                        this.decryptState.errorMsg = data.error || 'Gagal memproses dekripsi.';
                        return;
                    }

                    this.addLog("Sesi kunci Twofish valid (PBKDF2 SHA-256 derived).", "text-blue-400");
                    await this.sleep(300);

                    this.addLog("Menginisialisasi Twofish-256 Engine dalam Mode CBC...", "text-slate-400");
                    await this.sleep(400);

                    this.addLog("Memecah ciphertext menggunakan Initialization Vector (IV)...", "text-slate-400");
                    await this.sleep(300);

                    this.addLog("Melakukan MDS Matrix Multiplication & Feistel Network XOR pada block cipher...", "text-blue-400");
                    await this.sleep(450);

                    this.addLog("Melakukan permutasi S-Boxes...", "text-blue-400");
                    await this.sleep(300);

                    this.addLog("[SUKSES] Plaintext berhasil dipulihkan dari Ciphertext!", "text-emerald-400 font-bold");
                    
                    this.decryptState.plaintext = data.password;
                    this.decryptState.notes = data.notes;
                    this.decryptState.running = false;
                    this.decryptState.success = true;

                } catch (err) {
                    this.addLog("[ERROR] Koneksi jaringan atau server terputus.", "text-red-400 font-bold");
                    this.decryptState.running = false;
                    await this.sleep(1200);
                    this.decryptState.passwordVerified = false;
                    this.decryptState.errorMsg = 'Koneksi terputus.';
                }
            },

            addLog(text, color = "text-slate-400") {
                this.decryptLogs.push({ text: `[${new Date().toLocaleTimeString()}] ${text}`, color: color });
            },

            sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            },

            copyText(text, type = '') {
                navigator.clipboard.writeText(text);
                if (type === 'pwd') {
                    this.decryptState.pwdCopied = true;
                    setTimeout(() => this.decryptState.pwdCopied = false, 2000);

                    // Auto clear clipboard after 30 seconds
                    if (window.clipboardTimeout) {
                        clearTimeout(window.clipboardTimeout);
                    }
                    window.clipboardTimeout = setTimeout(() => {
                        navigator.clipboard.writeText('');
                        alert('Clipboard dibersihkan otomatis demi keamanan.');
                    }, 30000);
                } else if (type === 'note') {
                    this.decryptState.noteCopied = true;
                    setTimeout(() => this.decryptState.noteCopied = false, 2000);
                } else {
                    alert('Ciphertext berhasil disalin!');
                }
            }
        }
    }
</script>
@endsection
