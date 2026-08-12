<?php

namespace App\Services\Ia;

class AiProviderFactory
{
    public static function make(): AiProvider
    {
        $provider = config('services.ia.provider');
        $apiKey = config('services.ia.api_key');

        if (blank($provider) || blank($apiKey)) {
            return new NullAiProvider;
        }

        return match ($provider) {
            'openai', 'openai_compatible' => new OpenAiCompatibleProvider(
                apiKey: $apiKey,
                baseUrl: config('services.ia.base_url', 'https://api.openai.com/v1'),
                modelo: config('services.ia.modelo', 'gpt-4o-mini'),
            ),
            default => new NullAiProvider,
        };
    }
}
