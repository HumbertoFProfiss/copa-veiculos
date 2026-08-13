<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->enum('consignado_comissao_tipo', ['fixa', 'percentual'])->nullable()->after('consignado_telefone');
            $table->decimal('consignado_comissao_valor', 12, 2)->nullable()->after('consignado_comissao_tipo');
        });
    }

    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropColumn(['consignado_comissao_tipo', 'consignado_comissao_valor']);
        });
    }
};
