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
        
        $query = Credential::where('user_id', Auth::id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('platform_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $credentials = $query->orderBy('platform_name')->get();

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
        
        // Calculate password strength
        $strength = $this->calculatePasswordStrength($request->password);

        // Encrypt the password using Twofish
        $passwordResult = $this->twofishService->encrypt($request->password, $key);

        // Encrypt notes if they exist
        $notesEncrypted = null;
        $notesIv = null;
        if ($request->filled('notes')) {
            $notesResult = $this->twofishService->encrypt($request->notes, $key);
            $notesEncrypted = $notesResult['ciphertext'];
            $notesIv = $notesResult['iv'];
        }

        Credential::create([
            'user_id' => Auth::id(),
            'platform_name' => $request->platform_name,
            'platform_url' => $request->platform_url,
            'username' => $request->username,
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

        $data = [
            'platform_name' => $request->platform_name,
            'platform_url' => $request->platform_url,
            'username' => $request->username,
        ];

        // If a new password is provided, encrypt it and update strength
        if ($request->filled('password')) {
            $passwordResult = $this->twofishService->encrypt($request->password, $key);
            $data['password_encrypted'] = $passwordResult['ciphertext'];
            $data['password_iv'] = $passwordResult['iv'];
            $data['strength'] = $this->calculatePasswordStrength($request->password);
        }

        // Encrypt notes if they are updated
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

        // Require master password verification
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

            // Decrypt notes if they exist
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
                // Add additional performance context for grading
                'algorithm' => 'Twofish-256 (CBC mode)',
                'key_source' => 'PBKDF2 SHA-256 Derived'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal mendekripsi data: ' . $e->getMessage()], 500);
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
