<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Assistente de IA (ver App\Services\Ia) - provider configurável,
    // último módulo em prioridade (ver plano). Sem AI_PROVIDER/AI_API_KEY
    // no .env, cai automaticamente no NullAiProvider (nunca quebra a tela).
    'ia' => [
        'provider' => env('AI_PROVIDER'),
        'api_key' => env('AI_API_KEY'),
        'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1'),
        'modelo' => env('AI_MODELO', 'gpt-4o-mini'),
    ],

    // Consulta de placa (ver App\Services\ConsultaPlaca) - apiplacas.com.br
    // (endpoint documentado em wdapi2.com.br/consulta/{placa}/{token}).
    // Token único de plataforma, mesmo padrão do AI_API_KEY acima - sem ele
    // configurado, a busca por placa mostra erro claro em vez de quebrar a tela.
    'apiplacas' => [
        'token' => env('APIPLACAS_TOKEN'),
    ],

];
