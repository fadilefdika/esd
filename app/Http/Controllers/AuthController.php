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
        
            $user = Admin::where('username', $decryptedUsername)->first();
            if (!$user || !Hash::check($decryptedPassword, $user->password_hash)) {
                DB::rollBack();
                return back()->withErrors(['Username atau password salah.']);
            }
        
            Auth::guard('admin')->login($user);
            
            DB::commit();
            return redirect()->route('admin.entities.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors(['Terjadi kesalahan pada server.']);
        }
    }
    
        


    public function logout(Request $request)
    {
        Log::info('Logout initiated', [
            'admin_id' => session('admin')
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // for CSRF protection
        $request->session()->forget('admin');

        return redirect()->route('login');
    }

}
