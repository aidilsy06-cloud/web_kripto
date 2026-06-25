<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Services\TwofishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected TwofishService $twofishService;

    public function __construct(TwofishService $twofishService)
    {
        $this->twofishService = $twofishService;
    }

    public function index()
    {
        $userId = Auth::id();
        $credentials = Credential::where('user_id', $userId)->get();
        
        $total = $credentials->count();
        $weak = $credentials->where('strength', 'weak')->count();
        $medium = $credentials->where('strength', 'medium')->count();
        $strong = $credentials->where('strength', 'strong')->count();

        // Calculate reused passwords by decrypting them in memory
        $reusedCount = 0;
        $duplicates = [];
        
        $hexKey = Session::get('twofish_key');
        
        if ($hexKey && $total > 0) {
            $key = hex2bin($hexKey);
            $plainPasswords = [];

            foreach ($credentials as $cred) {
                try {
                    $plain = $this->twofishService->decrypt(
                        $cred->password_encrypted,
                        $key,
                        $cred->password_iv
                    );
                    $plainPasswords[$cred->id] = $plain;
                } catch (\Exception $e) {
                    // Skip if decryption fails due to invalid key
                }
            }

            // Find duplicate values
            $valueCounts = array_count_values($plainPasswords);
            foreach ($plainPasswords as $id => $val) {
                if ($valueCounts[$val] > 1) {
                    $reusedCount++;
                    $duplicates[] = $id; // Store credential IDs that are duplicates
                }
            }
        }

        // Calculate security score (out of 100)
        // Deduct 15 points per weak password, 10 points per reused password
        $securityScore = 100;
        if ($total > 0) {
            $deductions = ($weak * 15) + (($reusedCount / 2) * 10); // divide reusedCount by 2 roughly to count pairs
            $securityScore = max(0, min(100, 100 - (int)$deductions));
        }

        return view('dashboard', compact(
            'total',
            'weak',
            'medium',
            'strong',
            'reusedCount',
            'securityScore',
            'credentials'
        ));
    }

    /**
     * Show the cryptography playground page.
     */
    public function playground()
    {
        return view('playground');
    }

    /**
     * AJAX endpoint to encrypt arbitrary text for the playground.
     */
    public function sandboxEncrypt(Request $request)
    {
        $request->validate([
            'plaintext' => 'required|string',
            'key' => 'required|string|min:4',
        ]);

        try {
            // Derive a 256-bit key from the custom key string using a static salt for simplicity in sandbox
            $salt = "static_sandbox_salt_for_twofish";
            $derivedKey = $this->twofishService->deriveKey($request->key, $salt);

            $result = $this->twofishService->encrypt($request->plaintext, $derivedKey);

            return response()->json([
                'success' => true,
                'ciphertext' => $result['ciphertext'],
                'iv' => $result['iv'],
                'derived_key_hex' => bin2hex($derivedKey)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX endpoint to decrypt arbitrary text for the playground.
     */
    public function sandboxDecrypt(Request $request)
    {
        $request->validate([
            'ciphertext' => 'required|string',
            'iv' => 'required|string',
            'key' => 'required|string',
        ]);

        try {
            $salt = "static_sandbox_salt_for_twofish";
            $derivedKey = $this->twofishService->deriveKey($request->key, $salt);

            $plaintext = $this->twofishService->decrypt(
                $request->ciphertext,
                $derivedKey,
                $request->iv
            );

            return response()->json([
                'success' => true,
                'plaintext' => $plaintext
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mendekripsi: Pastikan Ciphertext, IV, dan Kunci sudah benar.'], 500);
        }
    }
}
