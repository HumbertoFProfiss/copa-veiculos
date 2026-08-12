<?php

return [

    /*
     * Domínio raiz usado pra resolver o subdomínio de cada empresa (tenant).
     * Requests pro domínio raiz exato (sem subdomínio) passam sem tenant
     * resolvido - reservado pra landing/marketing, fora do escopo da Fase 1.
     */
    'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'copaveiculos.test'),

];
