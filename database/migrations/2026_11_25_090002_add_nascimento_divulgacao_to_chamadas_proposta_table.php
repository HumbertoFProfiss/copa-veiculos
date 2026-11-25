<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chamadas_proposta', function (Blueprint $table) {
            if (! Schema::hasColumn('chamadas_proposta', 'data_nascimento')) {
                $table->date('data_nascimento')->nullable()->after('veiculo_procurado');
            }
            if (! Schema::hasColumn('chamadas_proposta', 'meio_divulgacao')) {
                $table->string('meio_divulgacao')->nullable()->after('data_nascimento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chamadas_proposta', function (Blueprint $table) {
            $table->dropColumn(['data_nascimento', 'meio_divulgacao']);
        });
    }
};
