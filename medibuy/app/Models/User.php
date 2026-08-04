<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
    'nomina',
    'password',
    'email',
    'name',
    'imagen',
    'phone',
    'curp',
    'cargo',
    'puesto',
    'domicilio',
    'nombre_contacto_emergencia',
    'numero_contacto_emergencia',
    'nombre_contacto_emergencia_secundario',
    'numero_contacto_emergencia_secundario',
    'vacaciones_disponibles',
    'vacaciones_utilizadas',
    'permisos',
    'retardos',
    'role'
];

    // Función para verificar roles
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // Relación uno a muchos con Asistencia
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
    public function solicitudes()
    {
        return $this->hasMany(SolicitudMaterial::class);
    }
    
    
}