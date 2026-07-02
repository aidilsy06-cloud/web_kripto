<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Services\TwofishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;

class CredentialController extends Controller
{
    protected TwofishService $twofishService;

    public function __construct(TwofishService $twofishService)
    {
        $this->twofishService = $twofishService;
    }

    /**
     * Display a listing of the user's credentials.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $hexKey = Session::get('twofish_key');
        if (!$hexKey) {
            return redirect()->route('unlock')->with('error', 'Sesi enkripsi Twofish Anda kedaluwarsa. Silakan buka vault Anda.');
        }
        $key = hex2bin($hexKey);
        
        $allCredentials = Credential::where('user_id', Auth::id())->get();
        $credentials = [];

        foreach ($allCredentials as $cred) {
            try {
                // Decrypt metadata in memory
                $cred->platform_name = $this->twofishService->decrypt($cred->platform_name_encrypted, $key, $cred->platform_name_iv);
                $cred->username = $this->twofishService->decrypt($cred->username_encrypted, $key, $cred->username_iv);
                
                $cred->platform_url = ($cred->platform_url_encrypted && $cred->platform_url_iv)
                    ? $this->twofishService->decrypt($cred->platform_url_encrypted, $key, $cred->platform_url_iv)
                    : null;

                // Search filtering in memory (Zero-Knowledge)
                if ($search) {
                    $term = strtolower($search);
                    if (str_contains(strtolower($cred->platform_name), $term) || str_contains(strtolower($cred->username), $term)) {
                        $credentials[] = $cred;
                    }
                } else {
                    $credentials[] = $cred;
                }
            } catch (Exception $e) {
                // Skip record if decryption fails (safeguard)
            }
        }

        // Sort credentials by decrypted platform_name
        usort($credentials, function ($a, $b) {
            return strcasecmp($a->platform_name ?? '', $b->platform_name ?? '');
        });

        return view('credentials.index', compact('credentials', 'search'));
    }

    /**
     * Store a newly created credential.
     */
    public function store(Request $request)
    {
        $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_url' => 'nullable|url|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:1',
            'notes' => 'nullable|string',
        ]);

        $hexKey = Session::get('twofish_key');
        if (!$hexKey) {
            return redirect()->route('unlock')->with('error', 'Sesi enkripsi Twofish Anda kedaluwarsa. Silakan buka vault Anda.');
        }

        $key = hex2bin($hexKey);
        $strength = $this->calculatePasswordStrength($request->password);

        // Encrypt everything with Twofish
        $platformResult = $this->twofishService->encrypt($request->platform_name, $key);
        $usernameResult = $this->twofishService->encrypt($request->username, $key);
        $passwordResult = $this->twofishService->encrypt($request->password, $key);

        $platformUrlEncrypted = null;
        $platformUrlIv = null;
        if ($request->filled('platform_url')) {
            $platformUrlResult = $this->twofishService->encrypt($request->platform_url, $key);
            $platformUrlEncrypted = $platformUrlResult['ciphertext'];
            $platformUrlIv = $platformUrlResult['iv'];
        }

        $notesEncrypted = null;
        $notesIv = null;
        if ($request->filled('notes')) {
            $notesResult = $this->twofishService->encrypt($request->notes, $key);
            $notesEncrypted = $notesResult['ciphertext'];
            $notesIv = $notesResult['iv'];
        }

        Credential::create([
            'user_id' => Auth::id(),
            'platform_name_encrypted' => $platformResult['ciphertext'],
            'platform_name_iv' => $platformResult['iv'],
            'platform_url_encrypted' => $platformUrlEncrypted,
            'platform_url_iv' => $platformUrlIv,
            'username_encrypted' => $usernameResult['ciphertext'],
            'username_iv' => $usernameResult['iv'],
            'password_encrypted' => $passwordResult['ciphertext'],
            'password_iv' => $passwordResult['iv'],
            'notes_encrypted' => $notesEncrypted,
            'notes_iv' => $notesIv,
            'strength' => $strength,
        ]);

        return redirect()->route('credentials.index')->with('success', 'Akun baru berhasil dienkripsi dan disimpan menggunakan algoritma Twofish!');
    }

    /**
     * Update the specified credential.
     */
    public function update(Request $request, Credential $credential)
    {
        if ($credential->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_url' => 'nullable|url|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:1',
            'notes' => 'nullable|string',
        ]);

        $hexKey = Session::get('twofish_key');
        if (!$hexKey) {
            return redirect()->route('unlock')->with('error', 'Sesi enkripsi Twofish Anda kedaluwarsa. Silakan buka vault Anda.');
        }

        $key = hex2bin($hexKey);

        // Encrypt new metadata
        $platformResult = $this->twofishService->encrypt($request->platform_name, $key);
        $usernameResult = $this->twofishService->encrypt($request->username, $key);

        $data = [
            'platform_name_encrypted' => $platformResult['ciphertext'],
            'platform_name_iv' => $platformResult['iv'],
            'username_encrypted' => $usernameResult['ciphertext'],
            'username_iv' => $usernameResult['iv'],
        ];

        if ($request->filled('platform_url')) {
            $platformUrlResult = $this->twofishService->encrypt($request->platform_url, $key);
            $data['platform_url_encrypted'] = $platformUrlResult['ciphertext'];
            $data['platform_url_iv'] = $platformUrlResult['iv'];
        } else {
            $data['platform_url_encrypted'] = null;
            $data['platform_url_iv'] = null;
        }

        if ($request->filled('password')) {
            $passwordResult = $this->twofishService->encrypt($request->password, $key);
            $data['password_encrypted'] = $passwordResult['ciphertext'];
            $data['password_iv'] = $passwordResult['iv'];
            $data['strength'] = $this->calculatePasswordStrength($request->password);
        }

        if ($request->has('notes')) {
            if ($request->filled('notes')) {
                $notesResult = $this->twofishService->encrypt($request->notes, $key);
                $data['notes_encrypted'] = $notesResult['ciphertext'];
                $data['notes_iv'] = $notesResult['iv'];
            } else {
                $data['notes_encrypted'] = null;
                $data['notes_iv'] = null;
            }
        }

        $credential->update($data);

        return redirect()->route('credentials.index')->with('success', 'Akun berhasil diperbarui dengan enkripsi Twofish baru.');
    }

    /**
     * Remove the specified credential.
     */
    public function destroy(Credential $credential)
    {
        if ($credential->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $credential->delete();

        return redirect()->route('credentials.index')->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Decrypt and return the credentials (via AJAX).
     */
    public function decrypt(Request $request, Credential $credential)
    {
        if ($credential->user_id !== Auth::id()) {
            return response()->json(['error' => 'Akses tidak sah.'], 403);
        }

        $request->validate([
            'master_password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->master_password, Auth::user()->password)) {
            return response()->json(['error' => 'Master Password salah. Akses ditolak.'], 403);
        }

        $hexKey = Session::get('twofish_key');
        if (!$hexKey) {
            return response()->json(['error' => 'Sesi Twofish kedaluwarsa. Silakan buka kembali vault Anda.'], 401);
        }

        try {
            $key = hex2bin($hexKey);

            // Decrypt password
            $decryptedPassword = $this->twofishService->decrypt(
                $credential->password_encrypted,
                $key,
                $credential->password_iv
            );

            // Decrypt notes
            $decryptedNotes = null;
            if ($credential->notes_encrypted && $credential->notes_iv) {
                $decryptedNotes = $this->twofishService->decrypt(
                    $credential->notes_encrypted,
                    $key,
                    $credential->notes_iv
                );
            }

            return response()->json([
                'success' => true,
                'password' => $decryptedPassword,
                'notes' => $decryptedNotes,
                'algorithm' => 'Twofish-256 (CBC mode)',
                'key_source' => 'PBKDF2 SHA-256 Derived'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal mendekripsi data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export credentials backup (encrypted with session Twofish key)
     */
    public function exportBackup()
    {
        $hexKey = Session::get('twofish_key');
        if (!$hexKey) {
            return redirect()->route('credentials.index')->with('error', 'Sesi Twofish kedaluwarsa. Gagal mengekspor backup.');
        }

        $key = hex2bin($hexKey);
        $allCredentials = Credential::where('user_id', Auth::id())->get();
        $plaintextList = [];

        foreach ($allCredentials as $cred) {
            try {
                $platform = $this->twofishService->decrypt($cred->platform_name_encrypted, $key, $cred->platform_name_iv);
                $username = $this->twofishService->decrypt($cred->username_encrypted, $key, $cred->username_iv);
                $password = $this->twofishService->decrypt($cred->password_encrypted, $key, $cred->password_iv);
                
                $url = ($cred->platform_url_encrypted && $cred->platform_url_iv)
                    ? $this->twofishService->decrypt($cred->platform_url_encrypted, $key, $cred->platform_url_iv)
                    : null;
                
                $notes = ($cred->notes_encrypted && $cred->notes_iv)
                    ? $this->twofishService->decrypt($cred->notes_encrypted, $key, $cred->notes_iv)
                    : null;

                $plaintextList[] = [
                    'platform_name' => $platform,
                    'platform_url' => $url,
                    'username' => $username,
                    'password' => $password,
                    'notes' => $notes,
                ];
            } catch (Exception $e) {
                // Skip record on decryption error
            }
        }

        // Encode payload in JSON
        $jsonPayload = json_encode([
            'credentials' => $plaintextList,
            'exported_at' => now()->toIso8601String(),
            'owner' => Auth::user()->email
        ]);

        // Encrypt the JSON payload with Twofish
        $encryptedResult = $this->twofishService->encrypt($jsonPayload, $key);

        // Build backup file structure
        $backupContent = json_encode([
            'app' => 'TulipCrypt',
            'version' => '1.0',
            'ciphertext' => $encryptedResult['ciphertext'],
            'iv' => $encryptedResult['iv']
        ], JSON_PRETTY_PRINT);

        $filename = 'tulip_backup_' . date('Y_m_d_His') . '.tulipbackup';

        return response($backupContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import credentials from encrypted backup file
     */
    public function importBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $hexKey = Session::get('twofish_key');
        if (!$hexKey) {
            return redirect()->route('credentials.index')->with('error', 'Sesi Twofish kedaluwarsa. Gagal mengimpor backup.');
        }

        $key = hex2bin($hexKey);
        $file = $request->file('backup_file');
        
        try {
            $content = json_decode(file_get_contents($file->getRealPath()), true);

            if (!$content || ($content['app'] ?? '') !== 'TulipCrypt') {
                return back()->with('error', 'Format berkas backup tidak valid! Pastikan menggunakan file berekstensi .tulipbackup yang diunduh dari TulipCrypt.');
            }

            // Decrypt backup content
            $decryptedJson = $this->twofishService->decrypt($content['ciphertext'], $key, $content['iv']);
            $payload = json_decode($decryptedJson, true);

            if (!isset($payload['credentials']) || !is_array($payload['credentials'])) {
                return back()->with('error', 'Payload data backup rusak atau tidak terbaca.');
            }

            $count = 0;
            foreach ($payload['credentials'] as $item) {
                // Re-encrypt fields for new DB storage
                $platformResult = $this->twofishService->encrypt($item['platform_name'], $key);
                $usernameResult = $this->twofishService->encrypt($item['username'], $key);
                $passwordResult = $this->twofishService->encrypt($item['password'], $key);

                $urlEncrypted = null;
                $urlIv = null;
                if (!empty($item['platform_url'])) {
                    $urlResult = $this->twofishService->encrypt($item['platform_url'], $key);
                    $urlEncrypted = $urlResult['ciphertext'];
                    $urlIv = $urlResult['iv'];
                }

                $notesEncrypted = null;
                $notesIv = null;
                if (!empty($item['notes'])) {
                    $notesResult = $this->twofishService->encrypt($item['notes'], $key);
                    $notesEncrypted = $notesResult['ciphertext'];
                    $notesIv = $notesResult['iv'];
                }

                Credential::create([
                    'user_id' => Auth::id(),
                    'platform_name_encrypted' => $platformResult['ciphertext'],
                    'platform_name_iv' => $platformResult['iv'],
                    'platform_url_encrypted' => $urlEncrypted,
                    'platform_url_iv' => $urlIv,
                    'username_encrypted' => $usernameResult['ciphertext'],
                    'username_iv' => $usernameResult['iv'],
                    'password_encrypted' => $passwordResult['ciphertext'],
                    'password_iv' => $passwordResult['iv'],
                    'notes_encrypted' => $notesEncrypted,
                    'notes_iv' => $notesIv,
                    'strength' => $this->calculatePasswordStrength($item['password']),
                ]);

                $count++;
            }

            return redirect()->route('credentials.index')->with('success', "Berhasil mengimpor {$count} kredensial secara aman!");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mendekripsi atau memuat berkas cadangan: Kunci enkripsi berbeda atau berkas rusak.');
        }
    }

    /**
     * Calculate password strength score (weak, medium, strong).
     */
    private function calculatePasswordStrength(string $password): string
    {
        $score = 0;
        $length = strlen($password);

        if ($length >= 8) $score += 1;
        if ($length >= 12) $score += 1;
        if (preg_match('/[0-9]/', $password)) $score += 1;
        if (preg_match('/[A-Z]/', $password)) $score += 1;
        if (preg_match('/[a-z]/', $password)) $score += 1;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $score += 1;

        if ($score <= 2) {
            return 'weak';
        } elseif ($score <= 4) {
            return 'medium';
        } else {
            return 'strong';
        }
    }
}
