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

                session()->forget('vendor_preview_code');

                // Redirect ke URL yang dituju (intended) sebelum dipaksa login, atau fallback ke dashboard
                return redirect()->intended(route('employee.dashboard'));
            } elseif ($role === 'vendor') {
                // Vendor Login Logic
                $vendor = \App\Models\Vendor::where('username', $decryptedUsername)->first();
                
                if (!$vendor) {
                    DB::rollBack();
                    return back()->withErrors(['Username/Kode Vendor tidak ditemukan.'])->withInput();
                }

                if (!\Hash::check($decryptedPassword, $vendor->password_hash)) {
                    DB::rollBack();
                    return back()->withErrors(['Password salah.'])->withInput();
                }
            
                Auth::guard('vendor')->login($vendor);
                DB::commit();

                if (session()->has('vendor_preview_code')) {
                    $code = session()->pull('vendor_preview_code');
                    return redirect()->route('vendor.action', $code);
                }

                return redirect()->intended(route('vendor.dashboard'));
            } else {
                // Admin Login Logic
                $user = Admin::where('username', $decryptedUsername)->first();
                if (!$user || !Hash::check($decryptedPassword, $user->password_hash)) {
                    DB::rollBack();
                    return back()->withErrors(['Username atau password salah.']);
                }
            
                Auth::guard('admin')->login($user);
                DB::commit();
                
                session()->forget('vendor_preview_code');

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

}
