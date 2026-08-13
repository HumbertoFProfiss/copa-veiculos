<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Amplia a biblioteca de modelos de contrato (prompt cita "20+ modelos" -
 * so 3 existiam ate aqui). ENUM precisa de ALTER MODIFY direto (mesmo
 * padrao ja usado em leads.origem) - Blueprint::enum()->change() não
 * cobre bem alteração de enum no MySQL via Doctrine.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE contrato_modelos MODIFY COLUMN tipo ENUM(
            'compra_venda', 'consignacao', 'procuracao',
            'financiamento', 'troca', 'garantia_estendida', 'recibo_sinal',
            'termo_entrega', 'declaracao_quitacao', 'termo_reserva',
            'termo_distrato', 'prestacao_servico', 'termo_cautela',
            'contrato_comissao', 'termo_consentimento_dados'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE contrato_modelos MODIFY COLUMN tipo ENUM('compra_venda', 'consignacao', 'procuracao') NOT NULL");
    }
};
