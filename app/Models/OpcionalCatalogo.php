<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catalogo global de opcionais (ar-condicionado, direcao eletrica...) -
 * compartilhado entre todas as empresas, mesmo padrao dos catalogos de
 * canais/bancos. Admin marca por checkbox no cadastro do veiculo (ver
 * Livewire\Veiculos\Form); item fora da lista continua possivel via
 * campo de texto livre (VeiculoOpcional sem opcional_catalogo_id).
 */
class OpcionalCatalogo extends Model
{
    protected $table = 'opcionais_catalogo';

    protected $fillable = ['nome', 'ordem'];
}
