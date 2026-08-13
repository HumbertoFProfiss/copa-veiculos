<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('veiculos_opcionais', function (Blueprint $table) {
            $table->foreignId('opcional_catalogo_id')->nullable()->after('veiculo_id')->constrained('opcionais_catalogo')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veiculos_opcionais', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opcional_catalogo_id');
        });
    }
};
