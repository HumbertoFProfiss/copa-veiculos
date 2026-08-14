<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo global (não é por empresa) - mesmas instituições pra
        // qualquer revenda, assim como "canais". Taxas cadastradas manualmente
        // aqui (sem parceria/API bancária real - ver PropostaFinanciamento).
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nome');
            $table->decimal('taxa_juros_am_padrao', 5, 2);
            $table->unsignedInteger('prazo_max_meses');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('bancos')->insert([
            ['slug' => 'bradesco_financiamentos', 'nome' => 'Bradesco Financiamentos', 'taxa_juros_am_padrao' => 1.89, 'prazo_max_meses' => 60, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'santander_financiamentos', 'nome' => 'Santander Financiamentos', 'taxa_juros_am_padrao' => 1.95, 'prazo_max_meses' => 60, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'itau_veiculos', 'nome' => 'Itaú Veículos', 'taxa_juros_am_padrao' => 1.79, 'prazo_max_meses' => 48, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'bv_financeira', 'nome' => 'BV Financeira', 'taxa_juros_am_padrao' => 2.10, 'prazo_max_meses' => 60, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'banco_pan', 'nome' => 'Banco Pan', 'taxa_juros_am_padrao' => 2.05, 'prazo_max_meses' => 48, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bancos');
    }
};
