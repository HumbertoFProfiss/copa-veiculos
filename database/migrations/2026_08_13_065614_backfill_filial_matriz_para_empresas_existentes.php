<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Cria uma filial "Matriz" pra cada empresa ja existente e associa todo
 * veiculo/usuario/venda sem filial a ela - assim, empresas que nunca vao
 * usar multi-loja continuam funcionando exatamente igual (tudo cai na
 * Matriz automaticamente), e as que quiserem uma segunda unidade so
 * precisam cadastrar a filial nova e mover o que for necessario.
 */
return new class extends Migration
{
    public function up(): void
    {
        $empresas = DB::table('empresas')->get(['id']);

        foreach ($empresas as $empresa) {
            $filialId = DB::table('filiais')->insertGetId([
                'empresa_id' => $empresa->id,
                'nome' => 'Matriz',
                'principal' => true,
                'ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('veiculos')->where('empresa_id', $empresa->id)->whereNull('filial_id')->update(['filial_id' => $filialId]);
            DB::table('users')->where('empresa_id', $empresa->id)->whereNull('filial_id')->update(['filial_id' => $filialId]);
            DB::table('vendas')->where('empresa_id', $empresa->id)->whereNull('filial_id')->update(['filial_id' => $filialId]);
        }
    }

    public function down(): void
    {
        // Reversao intencionalmente no-op: os dados voltam a filial_id=null
        // sozinhos quando a coluna e removida na migration anterior via down().
    }
};
