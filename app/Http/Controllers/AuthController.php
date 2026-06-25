<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwofishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected TwofishService $twofishService;

    public function __construct(TwofishService $twofishService)
    {
        $this->twofishService = $twofishService;
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate a cryptographically secure random 24-byte salt, base64 encoded (makes it 32 chars)
        $salt = base64_encode(random_bytes(24));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'master_key_salt' => $salt,
        ]);

        Auth::login($user);

        // Derive the Twofish master key and cache it in the session
        $derivedKey = $this->twofishService->deriveKey($request->password, $salt);
        Session::put('twofish_key', bin2hex($derivedKey));

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat dan kunci enkripsi Twofish Anda telah didefinisikan!');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));

            // Derive the Twofish key using the login password and user's stored salt
            $derivedKey = $this->twofishService->deriveKey($request->password, $user->master_key_salt);
            Session::put('twofish_key', bin2hex($derivedKey));

            // Log session start time to implement key expiration checks
            Session::put('twofish_key_timestamp', time());

            return redirect()->route('dashboard')->with('success', 'Berhasil login! Sesi enkripsi Twofish aktif.');
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }

    public function showUnlock()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if (Session::has('twofish_key')) {
            return redirect()->route('dashboard');
        }
        return view('auth.unlock');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            $derivedKey = $this->twofishService->deriveKey($request->password, $user->master_key_salt);
            Session::put('twofish_key', bin2hex($derivedKey));
            Session::put('twofish_key_timestamp', time());

            return redirect()->intended(route('dashboard'))->with('success', 'Vault berhasil dibuka. Sesi Twofish aktif kembali.');
        }

        return back()->withErrors([
            'password' => 'Master Password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        // Wipe the cryptographic key from the session
        Session::forget('twofish_key');
        Session::forget('twofish_key_timestamp');
        Session::invalidate();
        Session::regenerateToken();

        Auth::logout();

        return redirect()->route('login')->with('success', 'Sesi enkripsi Twofish Anda telah dihapus dengan aman.');
    }
}
