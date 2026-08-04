<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Cambia por el nombre de tu modelo

class UpdatePasswords extends Command
{
    protected $signature = 'update:passwords';
    protected $description = 'Cifra todas las contraseñas en la base de datos';

    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            if (!password_get_info($user->password)['algo']) { // Verifica si no está cifrada
                $user->password = bcrypt($user->password);
                $user->save();
            }
        }
        $this->info('Contraseñas actualizadas correctamente.');
    }
}
