@extends('layouts.app')

@section('page_title', 'Twofish Cryptography Visualizer & Sandbox')

@section('content')
<div class="space-y-8" x-data="playgroundManager()">
    
    <!-- Tab Controls -->
    <div class="flex gap-4 border-b border-slate-200 pb-1">
        <button @click="tab = 'sandbox'" 
                :class="tab === 'sandbox' ? 'border-blue-600 text-slate-800 font-extrabold' : 'border-transparent text-slate-400 hover:text-slate-800'"
                class="border-b-2 px-4 py-2 text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-flask text-blue-500"></i>
            <span>Symmetric Sandbox</span>
        </button>
        <button @click="tab = 'visualizer'" 
                :class="tab === 'visualizer' ? 'border-blue-600 text-slate-800 font-extrabold' : 'border-transparent text-slate-400 hover:text-slate-800'"
                class="border-b-2 px-4 py-2 text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-bezier-curve text-blue-500"></i>
            <span>Struktur Algoritma Twofish</span>
        </button>
    </div>

    <!-- SANDBOX TAB -->
    <div x-show="tab === 'sandbox'" class="grid grid-cols-1 xl:grid-cols-2 gap-8" x-cloak>
        
        <!-- Left: Interactive Encryption & Decryption Panels -->
        <div class="space-y-6">
            
            <!-- Encryption Sandbox Card -->
            <div class="glass rounded-3xl p-6 border border-white">
                <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <span>Twofish Encryption Sandbox</span>
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Plaintext Data</label>
                        <textarea x-model="encryptData.plaintext" rows="2" placeholder="Masukkan teks rahasia yang ingin dienkripsi..."
                                  class="w-full bg-white border border-slate-200 rounded-2xl py-3 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-mono text-sm shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Kunci Enkripsi (Custom Key)</label>
                            <input type="text" x-model="encryptData.key" placeholder="Contoh: kunciRahasiaku123"
                                   class="w-full bg-white border border-slate-200 rounded-2xl py-3 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all text-sm shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Ukuran Kunci (Key Size)</label>
                            <select class="w-full bg-white border border-slate-200 rounded-2xl py-3 px-4 text-slate-700 focus:outline-none focus:border-blue-500/50 transition-all text-sm shadow-sm">
                                <option value="256">256-bit (Twofish-256)</option>
                                <option value="192">192-bit (Twofish-192)</option>
                                <option value="128">128-bit (Twofish-128)</option>
                            </select>
                        </div>
                    </div>

                    <button @click="runEncrypt()" :disabled="!encryptData.plaintext || !encryptData.key"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white font-bold py-3 rounded-2xl shadow-md transition-all text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-play"></i>
                        <span>Enkripsi Teks</span>
                    </button>
                </div>
            </div>

            <!-- Decryption Sandbox Card -->
            <div class="glass rounded-3xl p-6 border border-white">
                <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shadow-sm">
                        <i class="fa-solid fa-lock-open"></i>
                    </span>
                    <span>Twofish Decryption Sandbox</span>
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Ciphertext (Base64)</label>
                            <input type="text" x-model="decryptData.ciphertext" placeholder="Masukkan ciphertext..."
                                   class="w-full bg-white border border-slate-200 rounded-2xl py-3 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-mono text-xs shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">IV (Base64)</label>
                            <input type="text" x-model="decryptData.iv" placeholder="Masukkan IV..."
                                   class="w-full bg-white border border-slate-200 rounded-2xl py-3 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all font-mono text-xs shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Kunci Dekripsi (Custom Key)</label>
                        <input type="text" x-model="decryptData.key" placeholder="Masukkan Kunci"
                               class="w-full bg-white border border-slate-200 rounded-2xl py-3 px-4 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500/50 transition-all text-sm shadow-sm">
                    </div>

                    <button @click="runDecrypt()" :disabled="!decryptData.ciphertext || !decryptData.iv || !decryptData.key"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3 rounded-2xl shadow-md transition-all text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-play"></i>
                        <span>Dekripsi Teks</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Right: Cryptographic Sandbox Visual Log -->
        <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[500px] border border-white">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-terminal text-blue-600"></i>
                    <span>Output Log Sandbox & Analisis</span>
                </h3>
                <p class="text-xs text-slate-500 mb-4 font-semibold">Saksikan detail matematis ketika data dienkripsi/dekripsi secara nyata.</p>

                <!-- Visual Log Console (Keep it dark for coding console layout!) -->
                <div class="bg-[#0f172a] rounded-2xl border border-white/10 p-4 font-mono text-[11px] text-slate-300 leading-relaxed min-h-[250px] max-h-[350px] overflow-y-auto shadow-inner">
                    <template x-for="log in logs">
                        <div class="mb-1">
                            <span class="text-blue-400 font-bold">$</span>
                            <span x-text="log.text" :class="log.color"></span>
                        </div>
                    </template>
                    <div x-show="logs.length === 0" class="h-48 flex items-center justify-center text-slate-500 font-semibold">
                        <span>Menunggu input... Silakan tekan tombol Enkripsi/Dekripsi.</span>
                    </div>
                </div>
            </div>

            <!-- Sandbox Results Card -->
            <div class="border-t border-slate-200 pt-4 mt-4" x-show="results.visible" x-transition>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Hasil Output</h4>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3 shadow-sm">
                    <template x-if="results.type === 'encrypt'">
                        <div class="space-y-2">
                            <div>
                                <span class="text-[10px] text-slate-500 block font-bold">CIPHERTEXT (BASE64)</span>
                                <div class="flex items-center gap-2 bg-white border border-slate-200 p-2.5 rounded-xl mt-0.5 shadow-sm">
                                    <span x-text="results.ciphertext" class="font-mono text-xs text-slate-800 truncate flex-1 select-all font-bold"></span>
                                    <button @click="copyToClipboard(results.ciphertext)" class="text-slate-400 hover:text-blue-600"><i class="fa-solid fa-copy"></i></button>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block font-bold">INITIALIZATION VECTOR (IV BASE64)</span>
                                <div class="flex items-center gap-2 bg-white border border-slate-200 p-2.5 rounded-xl mt-0.5 shadow-sm">
                                    <span x-text="results.iv" class="font-mono text-xs text-slate-800 truncate flex-1 select-all font-bold"></span>
                                    <button @click="copyToClipboard(results.iv)" class="text-slate-400 hover:text-blue-600"><i class="fa-solid fa-copy"></i></button>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block font-bold">DERIVED KEY (256-BIT PBKDF2 SHA-256 HEX)</span>
                                <div class="bg-white border border-slate-200 p-2.5 rounded-xl mt-0.5 font-mono text-[10px] text-blue-600 break-all select-all shadow-sm font-bold">
                                    <span x-text="results.derived_key"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="results.type === 'decrypt'">
                        <div>
                            <span class="text-[10px] text-slate-500 block font-bold">PLAINTEXT (HASIL DEKRIPSI)</span>
                            <div class="bg-white p-3 rounded-xl border border-slate-200 mt-0.5 font-mono text-sm text-emerald-600 select-all font-bold shadow-sm">
                                <span x-text="results.plaintext"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- VISUALIZER TAB (Twofish Architecture Diagram) -->
    <div x-show="tab === 'visualizer'" class="glass rounded-3xl p-6 lg:p-8 space-y-8 border border-white" x-cloak>
        <div>
            <h3 class="text-lg font-extrabold text-slate-800 mb-2"><i class="fa-solid fa-gears text-blue-500 mr-2"></i>Struktur Arsitektur Block Cipher Twofish</h3>
            <p class="text-xs text-slate-500 font-semibold">Arahkan kursor (*hover*) pada bagian komponen di bawah untuk memahami prinsip kerjanya.</p>
        </div>

        <!-- Interactive Architecture Flowchart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Schematic -->
            <div class="lg:col-span-2 space-y-6 bg-slate-100/50 p-6 rounded-2xl border border-slate-200/60 relative">
                
                <!-- Interactive Steps -->
                <div class="flex flex-col items-center gap-4 text-center">
                    
                    <!-- Plaintext Input (128-bit) -->
                    <div @mouseover="stepHover('plaintext')" 
                         class="w-56 glass p-3 rounded-xl border border-slate-350 hover:border-blue-500/50 cursor-pointer transition-all duration-200">
                        <span class="block text-[10px] text-blue-600 font-mono font-bold">128-bit BLOCK</span>
                        <span class="font-extrabold text-slate-800 text-xs">Plaintext Input</span>
                    </div>

                    <i class="fa-solid fa-arrow-down text-slate-400"></i>

                    <!-- Input Division (4 Sub-blocks) -->
                    <div @mouseover="stepHover('division')"
                         class="grid grid-cols-4 gap-2 w-full max-w-md">
                        <div class="glass p-2 rounded-lg border border-slate-250 hover:border-blue-500/50 text-[10px] font-mono font-bold text-slate-600">R0 (32b)</div>
                        <div class="glass p-2 rounded-lg border border-slate-250 hover:border-blue-500/50 text-[10px] font-mono font-bold text-slate-600">R1 (32b)</div>
                        <div class="glass p-2 rounded-lg border border-slate-250 hover:border-blue-500/50 text-[10px] font-mono font-bold text-slate-600">R2 (32b)</div>
                        <div class="glass p-2 rounded-lg border border-slate-250 hover:border-blue-500/50 text-[10px] font-mono font-bold text-slate-600">R3 (32b)</div>
                    </div>

                    <i class="fa-solid fa-arrow-down text-slate-400"></i>

                    <!-- Feistel Round Block -->
                    <div class="w-full max-w-lg border border-slate-250 rounded-2xl p-4 bg-white/40 relative">
                        <span class="absolute top-2 left-3 text-[9px] text-blue-600 font-mono font-bold">PUTARAN FEISTEL (16 ROUNDS)</span>
                        
                        <div class="flex flex-col sm:flex-row items-stretch justify-center gap-4 mt-4">
                            <!-- S-Box -->
                            <div @mouseover="stepHover('sbox')"
                                 class="flex-1 glass p-3 rounded-xl border border-slate-200 hover:border-blue-500/50 cursor-pointer transition-all">
                                <span class="block text-[9px] text-slate-400 font-mono font-bold">CONFUSION</span>
                                <span class="font-extrabold text-xs text-slate-800">4x Key-dependent S-Boxes</span>
                            </div>

                            <!-- MDS Matrix -->
                            <div @mouseover="stepHover('mds')"
                                 class="flex-1 glass p-3 rounded-xl border border-slate-200 hover:border-blue-500/50 cursor-pointer transition-all">
                                <span class="block text-[9px] text-slate-400 font-mono font-bold">DIFFUSION</span>
                                <span class="font-extrabold text-xs text-slate-800">4x4 MDS Matrix</span>
                            </div>

                            <!-- PHT -->
                            <div @mouseover="stepHover('pht')"
                                 class="flex-1 glass p-3 rounded-xl border border-slate-200 hover:border-blue-500/50 cursor-pointer transition-all">
                                <span class="block text-[9px] text-slate-400 font-mono font-bold">MIXING</span>
                                <span class="font-extrabold text-xs text-slate-800">Pseudo-Hadamard (PHT)</span>
                            </div>
                        </div>

                        <!-- Key Addition / XOR -->
                        <div class="mt-4 flex items-center justify-center gap-4">
                            <div @mouseover="stepHover('keyschedule')"
                                 class="w-48 glass p-2.5 rounded-xl border border-slate-200 hover:border-blue-500/50 cursor-pointer transition-all text-xs">
                                <span class="block text-[9px] text-slate-400 font-mono font-bold">ROUND KEYS</span>
                                <span class="font-bold text-slate-800">XOR Subkeys Addition</span>
                            </div>
                        </div>
                    </div>

                    <i class="fa-solid fa-arrow-down text-slate-400"></i>

                    <!-- Output (128-bit) -->
                    <div @mouseover="stepHover('ciphertext')"
                         class="w-56 glass p-3 rounded-xl border border-slate-350 hover:border-blue-500/50 cursor-pointer transition-all">
                        <span class="block text-[10px] text-blue-600 font-mono font-bold">128-bit CIPHERTEXT</span>
                        <span class="font-extrabold text-slate-800 text-xs">Ciphertext Output</span>
                    </div>

                </div>

            </div>

            <!-- Right 1 Col: Explanations Panel -->
            <div class="lg:col-span-1 glass rounded-2xl p-5 flex flex-col justify-start border border-slate-200 bg-white/40">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi Komponen</h4>
                <div class="space-y-4">
                    <h5 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <span class="w-1.5 h-3 rounded-full bg-blue-500"></span>
                        <span x-text="activeStep.title"></span>
                    </h5>
                    <p class="text-xs text-slate-500 leading-relaxed font-semibold" x-html="activeStep.description"></p>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Playground Alpine logic -->
<script>
    function playgroundManager() {
        return {
            tab: 'sandbox',
            
            encryptData: {
                plaintext: '',
                key: '',
            },
            
            decryptData: {
                ciphertext: '',
                iv: '',
                key: '',
            },

            logs: [],

            results: {
                visible: false,
                type: '', // 'encrypt' or 'decrypt'
                ciphertext: '',
                iv: '',
                derived_key: '',
                plaintext: '',
            },

            activeStep: {
                title: 'Arahkan Kursor',
                description: 'Arahkan kursor Anda pada skema diagram arsitektur Twofish di sebelah kiri untuk melihat penjelasan matematis dan teknis detail dari setiap komponen.'
            },

            steps: {
                plaintext: {
                    title: 'Plaintext Input (128-bit)',
                    description: 'Twofish bekerja pada blok data berukuran tetap **128-bit (16 byte)**. Setiap string input yang dimasukkan akan dikonversi menjadi biner, dibagi menjadi blok-blok 128-bit, dan diisi menggunakan padding khusus jika panjangnya tidak mencukupi.'
                },
                division: {
                    title: 'Pecah Input (Sub-block Division)',
                    description: 'Di awal putaran, blok plaintext 128-bit dipecah menjadi **empat sub-block berukuran 32-bit (R0, R1, R2, R3)**. Keempat bagian ini akan saling ber-XOR dan bertukar posisi dalam skema jaringan Feistel.'
                },
                sbox: {
                    title: 'Key-dependent S-Boxes (Confusion)',
                    description: 'Tidak seperti AES yang menggunakan S-Box statis tetap, Twofish menggunakan **empat S-Box 8x8 bit dinamis** yang diturunkan langsung dari kunci enkripsi (*Key-dependent*). Ini sangat menyulitkan penyerang melakukan analisis diferensial karena sifat substitusi berubah total setiap kali kunci diganti.'
                },
                mds: {
                    title: 'Matriks MDS 4x4 (Diffusion)',
                    description: 'Output dari S-Box dikalikan dengan **Matriks Maximum Distance Separable (MDS) 4x4** menggunakan perkalian aritmatika Galois Field (GF). MDS memastikan tingkat penyebaran bit (*diffusion*) yang tinggi—artinya, perubahan 1 byte input akan langsung mendistorsi seluruh byte lainnya.'
                },
                pht: {
                    title: 'Pseudo-Hadamard Transform (PHT)',
                    description: 'PHT adalah operasi pencampuran cepat 32-bit: \\(X\' = X + Y \\pmod{2^{32}}\\) dan \\(Y\' = X + 2Y \\pmod{2^{32}}\\). PHT menggabungkan output dari dua matriks MDS secara modular, meningkatkan efisiensi dan kekuatan pengacakan pada perangkat keras modern.'
                },
                keyschedule: {
                    title: 'Round Subkey Addition (XOR)',
                    description: 'Twofish mengadopsi struktur Feistel yang diubah. Sebelum dan sesudah putaran utama, serta di tengah putaran, sub-block ditambahkan dengan round subkeys (yang dihasilkan oleh skema *Key Schedule*) menggunakan XOR. Total ada 40 subkey yang digunakan selama enkripsi.'
                },
                ciphertext: {
                    title: 'Ciphertext Output (128-bit)',
                    description: 'Setelah melalui **16 putaran Feistel**, sub-block disusun kembali menjadi satu blok 128-bit. Ini menghasilkan ciphertext akhir dalam bentuk biner terenkripsi penuh yang tidak dapat dibaca, lalu dikonversi menjadi Base64 untuk penyimpanan database.'
                }
            },

            stepHover(stepKey) {
                if (this.steps[stepKey]) {
                    this.activeStep = this.steps[stepKey];
                }
            },

            async runEncrypt() {
                this.logs = [];
                this.results.visible = false;
                
                this.addLog("Memulai sandbox Twofish enkripsi...", "text-white font-bold");
                await this.sleep(300);

                this.addLog("Plaintext: " + this.encryptData.plaintext, "text-slate-300");
                this.addLog("Master Key: " + this.encryptData.key, "text-slate-350");
                await this.sleep(400);

                this.addLog("Mulai derivasi kunci via PBKDF2 (SHA-256, 10,005 Iterations)...", "text-slate-300");
                this.addLog("Menggunakan salt sandbox statis: 'static_sandbox_salt_for_twofish'", "text-slate-400 font-mono");
                
                try {
                    const response = await fetch('/sandbox/encrypt', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            plaintext: this.encryptData.plaintext,
                            key: this.encryptData.key
                        })
                    });

                    const data = await response.json();

                    if (!response.ok || data.error) {
                        this.addLog("[ERROR] Gagal enkripsi: " + (data.error || 'Terjadi kesalahan'), "text-red-400 font-bold");
                        return;
                    }

                    this.addLog("Derivasi Kunci Sukses! Derived Key (Hex): " + data.derived_key_hex, "text-blue-400");
                    await this.sleep(450);

                    this.addLog("Menghasilkan Initialization Vector (IV) 16-byte acak...", "text-slate-300");
                    this.addLog("IV (Base64): " + data.iv, "text-slate-400 font-mono");
                    await this.sleep(350);

                    this.addLog("Menjalankan Twofish block cipher 16 putaran CBC mode...", "text-blue-400");
                    await this.sleep(500);

                    this.addLog("Menerapkan padding PKCS#7 pada plaintext...", "text-slate-350");
                    await this.sleep(300);

                    this.addLog("[SUKSES] Enkripsi selesai!", "text-emerald-400 font-bold");

                    this.results.type = 'encrypt';
                    this.results.ciphertext = data.ciphertext;
                    this.results.iv = data.iv;
                    this.results.derived_key = data.derived_key_hex;
                    this.results.visible = true;

                    // Automatically populate Decrypt tab for convenience
                    this.decryptData.ciphertext = data.ciphertext;
                    this.decryptData.iv = data.iv;
                    this.decryptData.key = this.encryptData.key;

                } catch (err) {
                    this.addLog("[ERROR] Terjadi kegagalan jaringan.", "text-red-400 font-bold");
                }
            },

            async runDecrypt() {
                this.logs = [];
                this.results.visible = false;

                this.addLog("Memulai sandbox Twofish dekripsi...", "text-white font-bold");
                await this.sleep(300);

                this.addLog("Ciphertext (Base64): " + this.decryptData.ciphertext, "text-slate-400 font-mono");
                this.addLog("IV (Base64): " + this.decryptData.iv, "text-slate-400 font-mono");
                await this.sleep(400);

                this.addLog("Menurunkan kunci Twofish dari password input...", "text-slate-300");
                
                try {
                    const response = await fetch('/sandbox/decrypt', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            ciphertext: this.decryptData.ciphertext,
                            iv: this.decryptData.iv,
                            key: this.decryptData.key
                        })
                    });

                    const data = await response.json();

                    if (!response.ok || data.error) {
                        this.addLog("[ERROR] " + (data.error || 'Terjadi kesalahan'), "text-red-400 font-bold");
                        return;
                    }

                    this.addLog("Memvalidasi parameter padding & panjang IV...", "text-slate-300");
                    await this.sleep(350);

                    this.addLog("Melakukan inverse permutasi S-Boxes & MDS multiplication...", "text-blue-400");
                    await this.sleep(450);

                    this.addLog("[SUKSES] Plaintext berhasil dipulihkan!", "text-emerald-400 font-bold");

                    this.results.type = 'decrypt';
                    this.results.plaintext = data.plaintext;
                    this.results.visible = true;

                } catch (err) {
                    this.addLog("[ERROR] Terjadi kegagalan jaringan.", "text-red-400 font-bold");
                }
            },

            addLog(text, color = "text-slate-300") {
                this.logs.push({ text: `[${new Date().toLocaleTimeString()}] ${text}`, color: color });
            },

            sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            },

            copyToClipboard(text) {
                navigator.clipboard.writeText(text);
                alert("Berhasil disalin ke clipboard!");
            }
        }
    }
</script>
@endsection
