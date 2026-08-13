<?php

namespace Database\Seeders;

use App\Models\OpcionalCatalogo;
use Illuminate\Database\Seeder;

class OpcionalCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $itens = [
            'Ar-condicionado',
            'Direção elétrica',
            'Direção hidráulica',
            'Câmera de ré',
            'Sensor de estacionamento',
            'Central multimídia',
            'Teto solar',
            'Bancos em couro',
            'Piloto automático',
            'Airbags',
            'Vidros elétricos',
            'Travas elétricas',
            'Rodas de liga leve',
            'Volante multifuncional',
            'Computador de bordo',
            'Farol de neblina',
            'Retrovisores elétricos',
            'Controle de estabilidade (ESP)',
            'Controle de tração (TCS)',
            'Freios ABS',
            'Alarme',
            'Piloto de partida sem chave (Keyless)',
            'Ar-condicionado digital (dual zone)',
            'Carregador por indução',
            'Sensor de chuva',
            'Faróis de LED',
        ];

        foreach ($itens as $ordem => $nome) {
            OpcionalCatalogo::firstOrCreate(['nome' => $nome], ['ordem' => $ordem]);
        }
    }
}
