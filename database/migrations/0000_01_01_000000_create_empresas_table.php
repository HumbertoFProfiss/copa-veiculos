<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj', 18)->nullable();
            $table->string('slug')->unique();
            $table->enum('plano', ['completo', 'completo_opcionais', 'somente_site'])->default('completo');
            $table->enum('status', ['trial', 'ativo', 'inadimplente', 'suspenso'])->default('trial');
            $table->timestamp('trial_termina_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
