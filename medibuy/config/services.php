<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Aquí se guardan las credenciales de servicios externos
    | (Mailgun, Postmark, AWS, WhatsApp, OpenAI, etc.).
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    // ===================== WHATSAPP CLOUD API =====================
    'whatsapp' => [
        'token'        => env('WHATSAPP_ACCESS_TOKEN', ''),
        'version'      => env('WHATSAPP_API_VERSION', 'v21.0'),
        'phone_id'     => env('WHATSAPP_PHONE_NUMBER_ID', ''),
        'phone_e164'   => env('WHATSAPP_PHONE_NUMBER', ''),
        'waba_id'      => env('WHATSAPP_BUSINESS_ACCOUNT_ID', ''),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN', ''),

        // 🔔 Plantilla de servicio para AGENDA
        // Nombre EXACTO de la plantilla:
        //   servicio_recordatorio_evento · Spanish (MEX)
        'service_template_name' => env('WHATSAPP_TEMPLATE_NAME', 'servicio_recordatorio_evento'),
        // Idioma EXACTO para Spanish (MEX) en la API: es_MX
        'service_template_lang' => env('WHATSAPP_TEMPLATE_LANG', 'es_MX'),

        // Hora para recordatorios masivos (si llegas a usarlo)
        'reminder_send_at' => env('REMINDER_SEND_AT', '09:00'), // HH:MM 24h

        // Otras plantillas que ya usas en tickets, etc.
        'ticket_template_created'   => env('WA_TICKET_TEMPLATE_CREATED', null),   // ej: 'ticket_creado_v1'
        'ticket_template_commented' => env('WA_TICKET_TEMPLATE_COMMENTED', null), // ej: 'ticket_comentario_v1',
    ],

    // ===================== OPENAI / IA =====================
    'openai' => [
        'key'             => env('OPENAI_API_KEY'),
        'primary'         => env('AI_MODEL_PRIMARY', 'gpt-4o'),
        'fallback_models' => array_filter(array_map('trim', explode(',', env('OPENAI_FALLBACK_MODELS', 'gpt-4o,gpt-4o-mini,gpt-4.1-mini')))),
        'timeout'         => (int) env('AI_TIMEOUT', 45),
    ],

    // ===================== SLACK =====================
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ===================== AGENDA (CRON TOKEN) =====================
    'agenda_cron' => [
        'token' => env('AGENDA_CRON_TOKEN', 'EUPA021212'),
    ],

];
