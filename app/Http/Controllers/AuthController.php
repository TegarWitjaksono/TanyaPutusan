<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman Login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Jika dipanggil via AJAX (misal dari pop-up modal)
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang kembali.',
                    'user' => [
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ],
                    'redirect' => $request->input('redirect', route('home'))
                ]);
            }

            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan salah.'
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan halaman Register
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Proses Register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profesi' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Simpan data user ke Firebase Firestore
        try {
            $firebaseService = new \App\Services\FirebaseService();
            $firebaseService->syncUserToFirestore([
                'name' => $user->name,
                'email' => $user->email,
                'profesi' => $validated['profesi'] ?? 'Umum',
                'provider' => 'manual',
                'created_at' => now()->toIso8601String(),
                'role' => 'user',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal simpan ke Firebase: ' . $e->getMessage());
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Akun Anda aktif.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'redirect' => route('home')
            ]);
        }

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat! Selamat datang di TanyaPutusan.');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah keluar dari sistem.');
    }

    /**
     * Redirect pengguna ke halaman Google OAuth
     */
    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id');
        $redirectUri = url('/auth/google/callback');
        
        $state = bin2hex(random_bytes(16));
        $request->session()->put('google_oauth_state', $state);

        // Catat mode: 'login' (hanya jika akun sudah terdaftar) atau 'register' (buat akun jika belum ada)
        $mode = $request->query('mode', 'login');
        $request->session()->put('google_oauth_mode', $mode);

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    /**
     * Tangani callback dari Google OAuth
     */
    public function handleGoogleCallback(Request $request)
    {
        // Validasi parameter error dari Google jika user membatalkan
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google dibatalkan: ' . $request->input('error')
            ]);
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('login')->withErrors([
                'email' => 'Kode otorisasi Google tidak ditemukan.'
            ]);
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = url('/auth/google/callback');
        $mode = $request->session()->pull('google_oauth_mode', 'login');

        try {
            // Tukar authorization code dengan access token (tanpa verifikasi SSL ketat pada local dev lingkungan Windows/Laragon)
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->asForm()
                ->post('https://oauth2.googleapis.com/token', [
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ]);

            if (!$response->successful()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Gagal verifikasi dengan server Google: ' . $response->body()
                ]);
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Access token Google tidak valid.'
                ]);
            }

            // Ambil data profil pengguna dari Google UserInfo endpoint (tanpa verifikasi SSL ketat)
            $userResponse = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (!$userResponse->successful()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Gagal mengambil data profil Google.'
                ]);
            }

            $googleUser = $userResponse->json();
            $email = $googleUser['email'] ?? null;
            $name = $googleUser['name'] ?? ($googleUser['given_name'] ?? 'Pengguna Google');

            if (!$email) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Email dari akun Google Anda tidak dapat diakses.'
                ]);
            }

            // Cari pengguna di database lokal
            $user = User::where('email', $email)->first();

            // Jika dalam mode login dan akun BELUM terdaftar, tolak dan arahkan untuk registrasi
            if ($mode === 'login' && !$user) {
                return redirect()->route('register')->withErrors([
                    'email' => 'Akun Google dengan email (' . $email . ') belum terdaftar. Silakan registrasi terlebih dahulu.'
                ]);
            }

            // Jika pengguna belum ada dan dalam mode registrasi
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(\Illuminate\Support\Str::random(24)),
                    'email_verified_at' => now(),
                ]);

                // Simpan user baru ke Firebase Firestore
                try {
                    $firebaseService = new \App\Services\FirebaseService();
                    $firebaseService->syncUserToFirestore([
                        'name' => $user->name,
                        'email' => $user->email,
                        'profesi' => 'Umum',
                        'provider' => 'google',
                        'photo_url' => $googleUser['picture'] ?? null,
                        'created_at' => now()->toIso8601String(),
                        'last_login_at' => now()->toIso8601String(),
                        'role' => 'user',
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Firebase sync error: ' . $e->getMessage());
                }
            } else {
                // Perbarui nama jika kosong
                if (empty($user->name)) {
                    $user->name = $name;
                    $user->save();
                }

                // Update last_login_at di Firebase
                try {
                    $firebaseService = new \App\Services\FirebaseService();
                    $firebaseService->syncUserToFirestore([
                        'name' => $user->name,
                        'email' => $user->email,
                        'last_login_at' => now()->toIso8601String(),
                    ]);
                } catch (\Throwable $e) {
                    // silent fail
                }
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('success', 'Berhasil masuk dengan akun Google! Selamat datang, ' . $user->name . '.');

        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Terjadi kendala saat login dengan Google: ' . $e->getMessage()
            ]);
        }
    }
}
