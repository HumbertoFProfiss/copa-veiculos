<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE veiculos MODIFY COLUMN status ENUM('em_preparacao', 'disponivel', 'reservado', 'vendido', 'entregue', 'devolvido', 'inativo') NOT NULL DEFAULT 'em_preparacao'");
    }

    public function down(): void
    {
        DB::table('veiculos')->where('status', 'inativo')->update(['status' => 'em_preparacao']);

        DB::statement("ALTER TABLE veiculos MODIFY COLUMN status ENUM('em_preparacao', 'disponivel', 'reservado', 'vendido', 'entregue', 'devolvido') NOT NULL DEFAULT 'em_preparacao'");
    }
};
