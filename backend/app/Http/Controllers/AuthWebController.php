<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthWebController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        // Si el usuario ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }
    
    /**
     * Procesar inicio de sesión
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Verificar si es administrador
            $user = Auth::user();
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Solo los administradores pueden acceder al panel.',
                ]);
            }
        }
        
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
    
    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
