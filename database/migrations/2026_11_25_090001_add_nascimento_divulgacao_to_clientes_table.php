<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'data_nascimento')) {
                $table->date('data_nascimento')->nullable()->after('cpf');
            }
            if (! Schema::hasColumn('clientes', 'meio_divulgacao')) {
                $table->string('meio_divulgacao')->nullable()->after('data_nascimento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['data_nascimento', 'meio_divulgacao']);
        });
    }
};
