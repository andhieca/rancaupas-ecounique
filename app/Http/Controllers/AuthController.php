<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'origin' => 'required|in:domestik,mancanegara',
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'pengunjung',
            'origin' => $data['origin'],
        ]);

        Auth::login($user);

        ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Registrasi akun'
        ]);

        return redirect('/visitor');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Melakukan login'
            ]);
            
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin');
            }
            return redirect()->intended('/visitor');
        }

        return back()->withErrors([
            'email' => 'Kombinasi Email dan Password tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Melakukan logout'
            ]);
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
