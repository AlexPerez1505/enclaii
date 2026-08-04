<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nomina' => 'required|string|unique:users,nomina',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'cargo' => 'required|string',
            'puesto' => 'required|string',
            'vacaciones_disponibles' => 'nullable|integer',
            'vacaciones_utilizadas' => 'nullable|integer',
            'permisos' => 'nullable|integer',
            'retardos' => 'nullable|integer',
            'role' => 'required|string|in:admin,editor,user',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes', 'public');
        }

        User::create([
            'nomina' => $request->nomina,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'cargo' => $request->cargo,
            'puesto' => $request->puesto,
            'vacaciones_disponibles' => $request->vacaciones_disponibles,
            'vacaciones_utilizadas' => $request->vacaciones_utilizadas ?? 0,
            'permisos' => $request->permisos ?? 0,
            'retardos' => $request->retardos ?? 0,
            'role' => $request->role,
            'imagen' => $imagenPath,
        ]);

        return redirect()->route('users.create')->with('success', 'Usuario registrado exitosamente');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'confirmed'
            ],
        ], [
            'new_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'new_password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'new_password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', '¡Contraseña actualizada correctamente!');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            // Evita que te borres a ti mismo si no lo quieres permitir
            // Si sí quieres permitirlo, elimina este bloque.
            if (Auth::id() == $user->id) {
                return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
            }

            // 1) Romper relaciones que impiden borrar al usuario
            DB::table('cash_transactions')
                ->where('counterparty_id', $user->id)
                ->update(['counterparty_id' => null]);

            // Si tienes otras tablas relacionadas, agrega más bloques así:
            // DB::table('otra_tabla')->where('user_id', $user->id)->update(['user_id' => null]);

            // 2) Eliminar imagen si existe
            if ($user->imagen && Storage::disk('public')->exists($user->imagen)) {
                Storage::disk('public')->delete($user->imagen);
            }

            // 3) Eliminar usuario
            $user->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'No se pudo eliminar el usuario: ' . $e->getMessage());
        }
    }
}