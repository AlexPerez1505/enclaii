<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Rutas exentas de CSRF.
     */
    protected $except = [
        'webhooks/whatsapp',   // sin slash inicial
    ];
}