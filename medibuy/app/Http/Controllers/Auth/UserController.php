<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Mostrar el formulario para cambiar la contraseña.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password'); // Asegúrate de que esta vista exista
    }

    /**
     * Actualizar la contraseña del usuario.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/', // Al menos una letra minúscula
                'regex:/[A-Z]/', // Al menos una letra mayúscula
                'regex:/[0-9]/', // Al menos un número
                'regex:/[@$!%*?&]/', // Al menos un carácter especial
                'confirmed' // Debe coincidir con new_password_confirmation
            ],
        ], [
            'new_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'new_password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'new_password.confirmed' => 'Las contraseñas no coinciden.',
        ]);
    
        $user = Auth::user();
    
        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }
    
        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        return redirect()->back()->with('success', '¡Contraseña actualizada correctamente!');
    }
    
}
