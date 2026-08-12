<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->foreign('venda_id')->references('id')->on('vendas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropForeign(['venda_id']);
        });
    }
};
