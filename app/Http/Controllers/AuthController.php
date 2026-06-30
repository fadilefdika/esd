<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->has('from_preview')) {
            session(['vendor_preview_code' => $request->query('from_preview')]);
        }

        return view('auth.login');
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

        $decryptedUsername = $request->input('username');
        $decryptedPassword = $request->input('password');

        try {
            DB::beginTransaction();

            // 1. Attempt Admin Login
            $admin = Admin::where('username', $decryptedUsername)->first();
            if ($admin && $this->verifyAndUpgradePassword($admin, 'password_hash', $decryptedPassword)) {
                Auth::guard('admin')->login($admin);
                DB::commit();
                session()->forget('vendor_preview_code');
                return redirect()->route('admin.entities.index');
            }

            // 2. Attempt Vendor Login
            $vendor = \App\Models\Vendor::where('username', $decryptedUsername)->first();
            if ($vendor && $this->verifyAndUpgradePassword($vendor, 'password_hash', $decryptedPassword)) {
                Auth::guard('vendor')->login($vendor);
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
