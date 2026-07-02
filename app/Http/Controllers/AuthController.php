<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwofishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;

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

        $salt = base64_encode(random_bytes(24));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'master_key_salt' => $salt,
            'is_verified' => false,
            'role' => 'user',
        ]);

        // Generate 6-digit OTP
        $otp = (string) rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Log OTP for easy developer access
        Log::info("OTP untuk registrasi {$user->email}: {$otp}");

        // Try sending real email (will work if SMTP is set up in XAMPP)
        try {
            Mail::raw("Kode OTP registrasi TulipCrypt Anda adalah: {$otp}. Kode ini berlaku selama 10 menit.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Registrasi TulipCrypt');
            });
        } catch (\Exception $e) {
            // Silently capture SMTP errors, user can rely on log/toast debug message
        }

        // Derive the Twofish key and cache it temporarily until email is verified
        $derivedKey = $this->twofishService->deriveKey($request->password, $salt);
        Session::put('temp_register_twofish_key', bin2hex($derivedKey));
        Session::put('temp_register_user_id', $user->id);

        return redirect()->route('register.verify.form')->with('warning', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showVerifyForm()
    {
        if (!Session::has('temp_register_user_id')) {
            return redirect()->route('register');
        }
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        if (!Session::has('temp_register_user_id')) {
            return redirect()->route('register');
        }

        $user = User::findOrFail(Session::get('temp_register_user_id'));

        if ($user->otp_code === $request->otp_code && now()->lt($user->otp_expires_at)) {
            $user->update([
                'is_verified' => true,
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            Auth::login($user);

            // Promote temporary key to active session
            Session::put('twofish_key', Session::get('temp_register_twofish_key'));
            Session::forget('temp_register_twofish_key');
            Session::forget('temp_register_user_id');
            Session::put('twofish_last_activity', time());

            return redirect()->route('google2fa.setup')->with('success', 'Email Anda berhasil diverifikasi! Silakan lakukan pengaturan Google Authenticator (MFA).');
        }

        return back()->withErrors([
            'otp_code' => 'Kode OTP salah atau sudah kedaluwarsa.',
        ]);
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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Verify email verification status first
            if (!$user->is_verified) {
                // Generate and send new OTP
                $otp = (string) rand(100000, 999999);
                $user->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ]);

                Log::info("OTP untuk registrasi {$user->email}: {$otp}");

                try {
                    Mail::raw("Kode OTP registrasi TulipCrypt Anda adalah: {$otp}.", function ($message) use ($user) {
                        $message->to($user->email)->subject('Kode OTP Registrasi TulipCrypt');
                    });
                } catch (\Exception $e) {}

                $derivedKey = $this->twofishService->deriveKey($request->password, $user->master_key_salt);
                Session::put('temp_register_twofish_key', bin2hex($derivedKey));
                Session::put('temp_register_user_id', $user->id);

                return redirect()->route('register.verify.form')->with('warning', 'Email Anda belum diverifikasi. Kode OTP baru telah dikirim.');
            }

            // Derive key and hold it in a temporary variable
            $derivedKey = $this->twofishService->deriveKey($request->password, $user->master_key_salt);

            if ($user->google2fa_enabled) {
                Session::put('2fa_user_id', $user->id);
                Session::put('temp_twofish_key', bin2hex($derivedKey));
                return redirect()->route('login.2fa.form');
            }

            // Standard login if 2FA is not enabled
            Auth::login($user, $request->has('remember'));
            Session::put('twofish_key', bin2hex($derivedKey));
            Session::put('twofish_last_activity', time());

            return redirect()->route('dashboard')->with('success', 'Berhasil login! Sesi enkripsi Twofish aktif.');
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }

    public function show2FaChallenge()
    {
        if (!Session::has('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.login-2fa');
    }

    public function verify2FaChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (!Session::has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(Session::get('2fa_user_id'));
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            Auth::login($user);

            // Promote temporary key to active session
            Session::put('twofish_key', Session::get('temp_twofish_key'));
            Session::forget('temp_twofish_key');
            Session::forget('2fa_user_id');
            Session::put('twofish_last_activity', time());

            return redirect()->route('dashboard')->with('success', 'Kode 2FA Valid! Selamat datang kembali.');
        }

        return back()->withErrors([
            'code' => 'Kode Authenticator salah atau kedaluwarsa.',
        ]);
    }

    public function show2FaSetup()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        Session::put('temp_google2fa_secret', $secret);

        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("otpauth://totp/TulipCrypt:{$user->email}?secret={$secret}&issuer=TulipCrypt");

        return view('auth.setup-2fa', compact('secret', 'qrCodeUrl'));
    }

    public function enable2Fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = Session::get('temp_google2fa_secret');

        if (!$secret) {
            return redirect()->route('google2fa.setup')->with('error', 'Kunci rahasia kedaluwarsa. Silakan muat ulang halaman.');
        }

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($secret, $request->code)) {
            $user->update([
                'google2fa_secret' => $secret,
                'google2fa_enabled' => true,
            ]);

            Session::forget('temp_google2fa_secret');

            return redirect()->route('dashboard')->with('success', 'Google Authenticator 2FA berhasil diaktifkan pada akun Anda!');
        }

        return back()->withErrors([
            'code' => 'Kode Authenticator salah. Silakan coba lagi.',
        ]);
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
            Session::put('twofish_last_activity', time());

            return redirect()->intended(route('dashboard'))->with('success', 'Vault berhasil dibuka. Sesi Twofish aktif kembali.');
        }

        return back()->withErrors([
            'password' => 'Master Password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('twofish_key');
        Session::forget('twofish_last_activity');
        Session::invalidate();
        Session::regenerateToken();

        Auth::logout();

        return redirect()->route('login')->with('success', 'Sesi enkripsi Twofish Anda telah dihapus dengan aman.');
    }
}
