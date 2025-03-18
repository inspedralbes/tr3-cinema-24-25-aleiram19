<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     */
    public function register(Request $request)
    {
        try {
            \Log::info('Iniciando registro de usuario con datos:', $request->except(['password', 'password_confirmation']));
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Buscar el rol de usuario predeterminado (normalmente 'user')
            $userRole = Role::where('name', 'user')->first();
            if (!$userRole) {
                // Si no existe, crear el rol o usar un ID predeterminado
                $userRole = Role::create(['name' => 'user', 'description' => 'Usuario estándar']);
                \Log::info('Rol de usuario creado con ID: ' . $userRole->id);
            }

            \Log::info('Usando rol de usuario con ID: ' . $userRole->id);

            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id,
            ]);

            \Log::info('Usuario creado con ID: ' . $user->id);

            // Generar token con Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error en registro: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Devolver un mensaje más descriptivo
            return response()->json([
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Iniciar sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Verificar credenciales
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Obtener usuario autenticado
        $user = User::where('email', $request->email)->firstOrFail();
        
        // Revocar tokens anteriores (opcional)
        $user->tokens()->delete();
        
        // Crear nuevo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Obtener datos del usuario autenticado
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Cerrar sesión (revocar token)
     */
    public function logout(Request $request)
    {
        // Revocar el token que se usó para autenticar la solicitud
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
