<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pra mostrar "quem" fez cada cadastro no feed de atividades
        // recentes do dashboard - registros ja existentes ficam com
        // criado_por nulo (nao ha como saber retroativamente).
        Schema::table('veiculos', function (Blueprint $table) {
            $table->foreignId('criado_por')->nullable()->after('fornecedor_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('criado_por');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('criado_por');
        });
    }
};
