<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        return view('perfil', compact('user'));
    }

    /**
     * Actualizar datos personales (nombre, teléfono, correo, cargo, puesto).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
    'name'   => 'nullable|string|max:255',
    'phone'  => 'nullable|string|max:20',
    'email'  => 'nullable|email|unique:users,email,' . $user->id,
    'curp'   => 'nullable|string|max:30',
    'cargo'  => 'nullable|string|max:255',
    'puesto' => 'nullable|string|max:255',
    'domicilio' => 'nullable|string|max:500',
    'nombre_contacto_emergencia' => 'nullable|string|max:255',
    'numero_contacto_emergencia' => 'nullable|string|max:20',
    'nombre_contacto_emergencia_secundario' => 'nullable|string|max:255',
    'numero_contacto_emergencia_secundario' => 'nullable|string|max:20',
]);

    $user->update($request->only([
    'name',
    'email',
    'phone',
    'curp',
    'cargo',
    'puesto',
    'domicilio',
    'nombre_contacto_emergencia',
    'numero_contacto_emergencia',
    'nombre_contacto_emergencia_secundario',
    'numero_contacto_emergencia_secundario',
]));

        return redirect()->route('perfil')
    ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualizar la foto de perfil.
     * Soporta:
     *  - avatar_cropped (base64 desde el cropper)
     *  - imagen (archivo normal como fallback)
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar_cropped' => 'nullable|string',
            'imagen'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        // 1) Si viene imagen recortada desde el cropper (base64)
        if ($request->filled('avatar_cropped')) {
            $data = $request->input('avatar_cropped');

            // Formato esperado: data:image/png;base64,xxxx
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $matches)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($matches[1]); // png, jpg, jpeg, gif, webp...

                if (! in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $type = 'png';
                }
            } else {
                return back()->withErrors(['photo' => 'Formato de imagen inválido.']);
            }

            $data = base64_decode($data);

            if ($data === false) {
                return back()->withErrors(['photo' => 'No se pudo procesar la imagen recortada.']);
            }

            // Borrar imagen anterior si existe
            if ($user->imagen) {
                Storage::disk('public')->delete($user->imagen);
            }

            // Guardar nueva imagen
            $fileName = 'perfiles/' . uniqid('avatar_') . '.' . $type;
            Storage::disk('public')->put($fileName, $data);

            $user->imagen = $fileName;
            $user->save();

            return back()
                ->with('ok', 'Foto de perfil actualizada correctamente.')
                ->with('success', 'Foto de perfil actualizada correctamente.');
        }

        // 2) Fallback: archivo normal (por si llega de otra parte sin cropper)
        if ($request->hasFile('imagen')) {
            if ($user->imagen) {
                Storage::disk('public')->delete($user->imagen);
            }

            $path = $request->file('imagen')->store('perfiles', 'public');
            $user->imagen = $path;
            $user->save();

            return back()
                ->with('ok', 'Foto de perfil actualizada correctamente.')
                ->with('success', 'Foto de perfil actualizada correctamente.');
        }

        return back()
            ->with('ok', 'No se envió ninguna imagen.')
            ->with('success', 'No se envió ninguna imagen.');
    }
/** EDITAR PERFIL**/
    public function edit()
{
    $user = Auth::user();

    return view('perfil.edit', compact('user'));
}

    /**
     * Cambiar contraseña del usuario autenticado.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required'         => 'La nueva contraseña es obligatoria.',
            'password.min'              => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'La confirmación no coincide con la nueva contraseña.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()
            ->with('ok', 'Contraseña actualizada correctamente.')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Listado de todos los usuarios (solo admin).
     */
    public function allUsers()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $usuarios = User::all();

        return view('usuarios', compact('usuarios'));
    }
}