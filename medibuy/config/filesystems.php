<?php

// Aumentar límites de PHP para permitir archivos grandes
ini_set('upload_max_filesize', '3072M');
ini_set('post_max_size', '3072M');
ini_set('max_execution_time', '600');
ini_set('max_input_time', '600');

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'serve' => true,
            'throw' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Disco público
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | Tu hosting tiene deshabilitado symlink(), por eso php artisan storage:link
        | no funciona. Por eso configuramos el disco public para guardar
        | directamente dentro de public/storage.
        |
        | Las imágenes se verán desde:
        | https://medibuy.grupomedibuy.com/storage/archivo.jpg
        |
        */

        'public' => [
            'driver' => 'local',
            'root' => public_path('storage'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Se deja la configuración default de Laravel, pero en tu hosting no se usará
    | porque symlink() está deshabilitado. No ejecutes php artisan storage:link.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];