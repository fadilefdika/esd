<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->has('from_preview')) {
            session(['vendor_preview_code' => $request->query('from_preview')]);
        }

        return view('auth.login');
    }

    public function generateCaptcha()
    {
        // 1. Generate 5 karakter acak
        $code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
        session(['captcha_code' => $code]);

        // 2. Buat gambar ukuran 130x40
        $image = imagecreatetruecolor(130, 40);
        $bg = imagecolorallocate($image, 240, 240, 240); // Abu-abu terang
        
        imagefilledrectangle($image, 0, 0, 130, 40, $bg);

        // Cetak teks ke gambar SATU PER SATU agar posisinya naik-turun (Wobble Effect)
        $x = 20;
        for ($i = 0; $i < 5; $i++) {
            // Warna font dibuat agak gelap dan bervariasi
            $textColor = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100)); 
            $y = rand(8, 18); // Posisi Y naik turun secara acak
            imagestring($image, 5, $x, $y, $code[$i], $textColor);
            $x += 20; // Jarak antar huruf
        }

        // Tambahkan garis acak DI ATAS huruf agar bot OCR sangat kesulitan membaca
        for($i=0; $i<12; $i++) {
            $lineColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
            imageline($image, rand(0,130), rand(0,40), rand(0,130), rand(0,40), $lineColor);
        }
        
        // Tambahkan titik bising ekstra banyak DI ATAS huruf
        for($i=0; $i<250; $i++) {
            $pixelColor = imagecolorallocate($image, rand(50, 150), rand(50, 150), rand(50, 150));
            imagesetpixel($image, rand(0,130), rand(0,40), $pixelColor);
        }

        ob_start();
        imagejpeg($image);
        $imgData = ob_get_clean();
        imagedestroy($image);

        return response($imgData)->header('Content-type', 'image/jpeg');
    }

    public function login(Request $request)
    {
        // Log tampering alert if role parameter is explicitly provided
        if ($request->has('role')) {
            Log::warning('Login Tampering Attempt: role parameter sent in request', [
                'ip' => $request->ip(),
                'username' => $request->input('username'),
                'role_value' => $request->input('role')
            ]);
        }

        $key = 'login_attempts_' . $request->ip() . '_' . $request->input('username');

        // Cek apakah sudah terkunci (Diletakkan di atas agar VAPT scanner mendapat 429 walau tanpa Captcha)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $errors = new \Illuminate\Support\MessageBag(["Akun dikunci sementara. Silakan tunggu beberapa saat."]);
            return response()->view('auth.login', [
                'errors' => $errors, 
                'lockout_seconds' => $seconds
            ], 429);
        }

        // Hitungan bertambah pada setiap percobaan (termasuk jika lupa Captcha), agar VAPT scanner mendapat 429
        RateLimiter::hit($key, 60);

        $request->validate([
            'captcha' => 'required',
        ], [
            'captcha.required' => 'Silakan masukkan kode CAPTCHA terlebih dahulu.',
        ]);

        if (strtolower($request->input('captcha')) !== strtolower(session('captcha_code'))) {
            return back()->withErrors(['CAPTCHA tidak valid. Silakan coba lagi.'])->withInput();
        }

        $decryptedUsername = $request->input('username');
        $rawPassword = $request->input('password');
        $decryptedPassword = '';

        // RSA Decryption
        $privateKeyPath = storage_path('keys/private_key.pem');
        if (!file_exists($privateKeyPath)) {
            $privateKeyPath = storage_path('app/keys/private_key.pem');
        }

        if (file_exists($privateKeyPath) && !empty($rawPassword)) {
            $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
            if ($privateKey) {
                // Decode Base64 safely
                $decoded = base64_decode($rawPassword, true);
                if ($decoded !== false) {
                    if (openssl_private_decrypt($decoded, $decryptedPassword, $privateKey)) {
                        // Decryption successful
                    } else {
                        \Illuminate\Support\Facades\Log::error('RSA Decryption failed.', [
                            'error' => openssl_error_string()
                        ]);
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::error('RSA Private Key is invalid.');
            }
        }

        // Fallback jika RSA dinonaktifkan atau gagal (untuk kompatibilitas testing)
        if (empty($decryptedPassword)) {
            $decryptedPassword = $rawPassword;
        }

        try {
            DB::beginTransaction();

            // 1. Attempt Admin Login
            $admin = Admin::where('username', $decryptedUsername)->first();
            if ($admin && $this->verifyAndUpgradePassword($admin, 'password_hash', $decryptedPassword)) {
                Auth::guard('admin')->login($admin);
                RateLimiter::clear($key);
                DB::commit();
                session()->forget('vendor_preview_code');
                return redirect()->route('admin.entities.index');
            }

            // 2. Attempt Vendor Login
            $vendor = \App\Models\Vendor::where('username', $decryptedUsername)->first();
            if ($vendor && $this->verifyAndUpgradePassword($vendor, 'password_hash', $decryptedPassword)) {
                Auth::guard('vendor')->login($vendor);
                RateLimiter::clear($key);
                DB::commit();

                if (session()->has('vendor_preview_code')) {
                    $code = session()->pull('vendor_preview_code');
                    return redirect()->route('vendor.action', $code);
                }
                return redirect()->intended(route('vendor.dashboard'));
            }

            // 3. Attempt Employee Login
            $user = \App\Models\User::where('npk', $decryptedUsername)->first();
            if ($user && $this->verifyAndUpgradePassword($user, 'password', $decryptedPassword)) {
                Auth::guard('web')->login($user);
                RateLimiter::clear($key);
                DB::commit();
                session()->forget('vendor_preview_code');
                return redirect()->intended(route('employee.dashboard'));
            }

            // Authentication failed for all roles
            DB::rollBack();
            return back()->withErrors(['Username/NPK atau password salah.'])->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors(['Terjadi kesalahan pada server.']);
        }
    }
    
    public function logout(Request $request)
    {
        Log::info('Logout initiated', [
            'admin_id' => Auth::guard('admin')->id(),
            'employee_id' => Auth::guard('web')->id(),
            'vendor_id' => Auth::guard('vendor')->id()
        ]);

        // Logout dari semua role (guard)
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();
        Auth::guard('vendor')->logout();

        // invalidate() akan membersihkan/flush secara menyeluruh 
        // SEMUA session browser (role, id, data yg tersisa), dan me-regenerate Session ID baru.
        $request->session()->invalidate();
        
        // Memastikan regenerasi token CSRF agar sesi lama benar-benar mati dan aman 
        $request->session()->regenerateToken(); 

        return redirect()->route('login');
    }

    /**
     * Memverifikasi password dan otomatis melakukan update dari Plaintext/MD5 ke Bcrypt.
     */
    private function verifyAndUpgradePassword($model, $passwordField, $plainPassword)
    {
        $currentPassword = $model->{$passwordField};

        try {
            // Coba verifikasi dengan format hash modern (Bcrypt/Argon2)
            if (Hash::check($plainPassword, $currentPassword)) {
                return true;
            }
        } catch (\Exception $e) {
            // Mengabaikan error "This password does not use the Bcrypt algorithm"
            // yang muncul saat mendeteksi password usang, dan lanjut ke pengecekan legacy
        }

        // Cek jika password masih berupa Plaintext (belum di-hash)
        if ($currentPassword === $plainPassword) {
            $model->{$passwordField} = Hash::make($plainPassword);
            $model->save();
            return true;
        }

        // Cek jika password masih berupa MD5
        if ($currentPassword === md5($plainPassword)) {
            $model->{$passwordField} = Hash::make($plainPassword);
            $model->save();
            return true;
        }

        // --- DEBUGGING LOG ---
        Log::warning('Password Mismatch Debug', [
            'username_or_id' => $model->id ?? 'unknown',
            'password_from_form_decrypted' => $plainPassword,
            'password_in_database' => $currentPassword
        ]);

        return false;
    }
}
