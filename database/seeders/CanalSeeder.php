<?php

namespace Database\Seeders;

use App\Models\Canal;
use Illuminate\Database\Seeder;

class CanalSeeder extends Seeder
{
    public function run(): void
    {
        $canais = [
            ['slug' => 'webmotors', 'nome' => 'WebMotors', 'tipo' => 'csv'],
            ['slug' => 'icarros', 'nome' => 'iCarros', 'tipo' => 'csv'],
            ['slug' => 'chavesnamao', 'nome' => 'Chaves na Mão', 'tipo' => 'csv'],
            ['slug' => 'mobautos', 'nome' => 'MobAutos', 'tipo' => 'csv'],
            ['slug' => 'napista', 'nome' => 'Na Pista', 'tipo' => 'csv'],
            ['slug' => 'carrossp', 'nome' => 'Carros SP', 'tipo' => 'csv'],
            ['slug' => 'mercadolivre', 'nome' => 'Mercado Livre', 'tipo' => 'api'],
            ['slug' => 'facebook', 'nome' => 'Facebook Marketplace', 'tipo' => 'csv'],
            ['slug' => 'site_proprio', 'nome' => 'Site Próprio', 'tipo' => 'api'],
        ];

        foreach ($canais as $canal) {
            Canal::firstOrCreate(['slug' => $canal['slug']], $canal);
        }
    }
}
