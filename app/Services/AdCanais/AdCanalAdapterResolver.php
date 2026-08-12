<?php

namespace App\Services\AdCanais;

use App\Models\Canal;

class AdCanalAdapterResolver
{
    public function resolve(Canal $canal): AdCanalAdapter
    {
        return match ($canal->slug) {
            'site_proprio' => new SiteProprioAdapter,
            'facebook' => new FacebookMarketplaceCsvAdapter,
            'mercadolivre' => new MercadoLivreAdapter,
            default => new GenericPortalCsvAdapter($canal),
        };
    }
}
