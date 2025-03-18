<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
// use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('role')->get();
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($users);
        }
        
        // Si es una solicitud web, devolver vista
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_guest' => false,
        ]);

        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Usuario creado correctamente',
                'user' => $user
            ], 201);
        } else {
            return redirect()->route('users.index')
                ->with('success', 'Usuario creado correctamente');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $user = User::with('role', 'tickets')->findOrFail($id);
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($user);
        }
        
        // Si es una solicitud web, devolver vista
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ];

        // Solo validar la contraseña si se proporciona
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $userData = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        // Actualizar la contraseña solo si se proporciona
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ]);
        } else {
            return redirect()->route('users.index')
                ->with('success', 'Usuario actualizado correctamente');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Verificar si tiene tickets
        if ($user->tickets()->count() > 0) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el usuario porque tiene tickets asociados'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el usuario porque tiene tickets asociados');
            }
        }

        $user->delete();

        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Usuario eliminado correctamente'
            ]);
        } else {
            return redirect()->route('users.index')
                ->with('success', 'Usuario eliminado correctamente');
        }
    }
}
