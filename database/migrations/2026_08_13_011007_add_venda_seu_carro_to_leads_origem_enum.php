<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE leads MODIFY COLUMN origem ENUM('estoque', 'formulario_interesse', 'simulador', 'site', 'venda_seu_carro', 'outro') NOT NULL DEFAULT 'site'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE leads MODIFY COLUMN origem ENUM('estoque', 'formulario_interesse', 'simulador', 'site', 'outro') NOT NULL DEFAULT 'site'");
    }
};
