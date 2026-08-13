<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->foreignId('categoria_pai_id')->nullable()->after('tipo')
                ->constrained('categorias_financeiras')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_pai_id');
        });
    }
};
