<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->decimal('lucro_financeiro', 12, 2)->default(0)->after('comissao_vendedor');
            $table->string('instituicao_financeira', 100)->nullable()->after('lucro_financeiro');
            $table->decimal('impostos', 12, 2)->default(0)->after('instituicao_financeira');
        });

        // Renomeia 'financiado' -> 'financiamento' (alinhado ao nome usado nas
        // formas de pagamento por linha, ver venda_pagamentos) e abre espaco pros
        // metodos mais granulares (dinheiro, pix, cartao...) e 'misto' quando a
        // venda tiver mais de uma forma de pagamento.
        DB::statement("ALTER TABLE vendas MODIFY COLUMN forma_pagamento ENUM('avista','dinheiro','pix','transferencia','cartao_debito','cartao_credito','financiamento','consorcio','troca','misto') NOT NULL DEFAULT 'avista'");
        DB::statement("UPDATE vendas SET forma_pagamento = 'financiamento' WHERE forma_pagamento = 'financiado'");
    }

    public function down(): void
    {
        DB::statement("UPDATE vendas SET forma_pagamento = 'financiado' WHERE forma_pagamento = 'financiamento'");
        DB::statement("UPDATE vendas SET forma_pagamento = 'avista' WHERE forma_pagamento IN ('dinheiro','pix','transferencia','cartao_debito','cartao_credito','misto')");
        DB::statement("ALTER TABLE vendas MODIFY COLUMN forma_pagamento ENUM('avista','financiado','consorcio','troca') NOT NULL DEFAULT 'avista'");

        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn(['lucro_financeiro', 'instituicao_financeira', 'impostos']);
        });
    }
};
