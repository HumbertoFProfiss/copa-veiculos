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
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('telefone', 20)->nullable()->after('cnpj');
            $table->string('whatsapp', 20)->nullable()->after('telefone');
            $table->string('email_contato')->nullable()->after('whatsapp');
            $table->string('endereco')->nullable()->after('email_contato');
            $table->string('cidade')->nullable()->after('endereco');
            $table->string('uf', 2)->nullable()->after('cidade');
            $table->string('horario_funcionamento')->nullable()->after('uf');
            $table->string('instagram_url')->nullable()->after('horario_funcionamento');
            $table->string('facebook_url')->nullable()->after('instagram_url');
            $table->text('sobre_texto')->nullable()->after('facebook_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'telefone', 'whatsapp', 'email_contato', 'endereco', 'cidade',
                'uf', 'horario_funcionamento', 'instagram_url', 'facebook_url', 'sobre_texto',
            ]);
        });
    }
};
