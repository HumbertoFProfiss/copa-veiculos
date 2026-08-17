<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Separa, pra venda financiada, quanto e pago direto (entrada) de
        // quanto vai pro banco (financiado, o que gera comissao do banco) -
        // preenchido direto na tela de Nova venda, igual o carro de troca.
        Schema::table('vendas', function (Blueprint $table) {
            $table->decimal('valor_entrada', 12, 2)->nullable()->after('desconto');
            $table->decimal('valor_financiado', 12, 2)->nullable()->after('valor_entrada');
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn(['valor_entrada', 'valor_financiado']);
        });
    }
};
