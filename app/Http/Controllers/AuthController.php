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
    public function showLogin()
    {
        $publicKey = file_get_contents(storage_path('app/keys/public.pem'));
        return view('auth.login', compact('publicKey'));
    }

    public function login(Request $request)
    {
        try {
            DB::beginTransaction();

            $encryptedUsername = $request->input('encrypted_username');
            $encryptedPassword = $request->input('encrypted_password');
            $role = $request->input('role', 'admin'); // Default admin
        
            $privateKeyPath = storage_path('app/keys/private.pem');
            $privateKeyString = file_get_contents($privateKeyPath);
            $privateKey = openssl_pkey_get_private($privateKeyString);
        
            if (!$privateKey) {
                Log::error('Private key tidak valid.');
                DB::rollBack();
                return back()->withErrors(['Server error.']);
            }
        
            $ok1 = openssl_private_decrypt(base64_decode($encryptedUsername), $decryptedUsername, $privateKey);
            $ok2 = openssl_private_decrypt(base64_decode($encryptedPassword), $decryptedPassword, $privateKey);
        
            if (!$ok1 || !$ok2) {
                DB::rollBack();
                return back()->withErrors(['Gagal dekripsi.']);
            }
        
            if ($role === 'employee') {
                // Karyawan Login Logic
                $user = \App\Models\User::where('npk', $decryptedUsername)->first();
                
                if (!$user) {
                    DB::rollBack();
                    return back()->withErrors(['NPK tidak ditemukan.'])->withInput();
                }

                // Cek apakah password plain text ATAU hashed bcrypt
                $isPasswordValid = false;
                if ($user->password === $decryptedPassword) {
                    $isPasswordValid = true; // Plain text match
                } elseif (\Hash::check($decryptedPassword, $user->password)) {
                    $isPasswordValid = true; // Hashed match
                }

                if (!$isPasswordValid) {
                    DB::rollBack();
                    return back()->withErrors(['Password salah.'])->withInput();
                }
            
                Auth::guard('web')->login($user);
                DB::commit();

                // Redirect ke URL yang dituju (intended) sebelum dipaksa login, atau fallback ke dashboard
                return redirect()->intended(route('employee.dashboard'));
            } else {
                // Admin Login Logic
                $user = Admin::where('username', $decryptedUsername)->first();
                if (!$user || !Hash::check($decryptedPassword, $user->password_hash)) {
                    DB::rollBack();
                    return back()->withErrors(['Username atau password salah.']);
                }
            
                Auth::guard('admin')->login($user);
                DB::commit();
                
                return redirect()->route('admin.entities.index');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors(['Terjadi kesalahan pada server.']);
        }
    }
    
    public function logout(Request $request)
    {
        Log::info('Logout initiated', [
            'admin_id' => session('admin'),
            'user_id' => Auth::id()
        ]);

        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // for CSRF protection
        $request->session()->forget('admin');

        return redirect()->route('login');
    }

}
