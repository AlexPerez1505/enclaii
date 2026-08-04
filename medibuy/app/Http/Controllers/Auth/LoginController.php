<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validación de los datos del formulario
        $credentials = $request->validate([
            'nomina' => ['required', 'string'],
            'contrasena' => ['required', 'string'],
        ]);

        // Verificamos si el usuario existe
        $user = User::where('nomina', $credentials['nomina'])->first();

        // Si el usuario no existe
        if (!$user) {
            return back()->withErrors([
                'nomina' => 'Usuario incorrecto.',
            ])->withInput();
        }

        // Intentamos hacer login con las credenciales proporcionadas
        if (Auth::attempt([
            'nomina' => $credentials['nomina'],
            'password' => $credentials['contrasena']
        ])) {
            $request->session()->regenerate();

            /*
            |--------------------------------------------------------------------------
            | Validar horario después de confirmar contraseña correcta
            |--------------------------------------------------------------------------
            | Si el usuario está en la lista de restringidos y está fuera de horario,
            | se cierra la sesión y se manda a la vista sistema-no-disponible.
            */
            if ($this->usuarioFueraDeHorario(Auth::user())) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->view('auth.sistema-no-disponible', [
                    'mensaje' => 'Tu acceso está permitido únicamente de lunes a viernes de 9:00 AM a 6:00 PM y sábados de 9:00 AM a 1:00 PM.',
                    'horario' => 'Lunes a viernes de 9:00 AM a 6:00 PM y sábados de 9:00 AM a 1:00 PM.',
                ]);
            }

            return redirect('/home');
        }

        // Si el login falla debido a la contraseña incorrecta
        return back()->withErrors([
            'contrasena' => 'Contraseña incorrecta.',
        ])->withInput();
    }

    private function usuarioFueraDeHorario($user)
    {
        /*
        |--------------------------------------------------------------------------
        | Usuarios restringidos por horario
        |--------------------------------------------------------------------------
        | Aquí pones los IDs que tendrán horario limitado.
        |
        | Ejemplo:
        | $restrictedUserIds = [1];
        | $restrictedUserIds = [1, 18, 25, 30];
        */
        $restrictedUserIds = [19];

        // Si el usuario NO está restringido, puede entrar siempre
        if (!$user || !in_array($user->id, $restrictedUserIds)) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Horario permitido
        |--------------------------------------------------------------------------
        | Lunes a viernes: 09:00 a 18:00
        | Sábado: 09:00 a 13:00
        | Domingo: sin acceso
        */
        $now = Carbon::now('America/Mexico_City');

        // 1 = lunes, 2 = martes, 3 = miércoles, 4 = jueves,
        // 5 = viernes, 6 = sábado, 7 = domingo
        $dayOfWeek = $now->dayOfWeekIso;

        $currentTime = $now->format('H:i');

        // Lunes a viernes de 09:00 a 18:00
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            return !($currentTime >= '09:00' && $currentTime <= '18:00');
        }

        // Sábado de 09:00 a 13:00
        if ($dayOfWeek == 6) {
            return !($currentTime >= '09:00' && $currentTime <= '18:00');
        }

        // Domingo sin acceso
        return true;
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}