<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chamadas_proposta', function (Blueprint $table) {
            $table->string('veiculo_procurado')->nullable()->after('veiculo_id');
        });
    }

    public function down(): void
    {
        Schema::table('chamadas_proposta', function (Blueprint $table) {
            $table->dropColumn('veiculo_procurado');
        });
    }
};
