<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cliente implementa o contrato Authenticatable (login na área do cliente),
 * que exige a coluna remember_token pra função "lembrar de mim" - faltava
 * desde a criação da tabela, quebrando o login sempre que o guard tenta
 * gravar o token de sessão persistente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
