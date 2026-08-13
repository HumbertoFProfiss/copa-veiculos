<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Veiculo extends Model
{
    use BelongsToEmpresa, HasFactory, SoftDeletes;

    public const STATUS_LABELS = [
        'em_preparacao' => 'Em preparação',
        'disponivel' => 'Disponível',
        'reservado' => 'Reservado',
        'vendido' => 'Vendido',
        'entregue' => 'Entregue',
        'devolvido' => 'Devolvido',
    ];

    protected $fillable = [
        'empresa_id',
        'fornecedor_id',
        'filial_id',
        'marca',
        'modelo',
        'versao',
        'slug',
        'ano_fabricacao',
        'ano_modelo',
        'km',
        'combustivel',
        'cambio',
        'cor',
        'portas',
        'motor',
        'placa',
        'numero_chassi',
        'renavam',
        'numero_estoque',
        'tipo_propriedade',
        'consignado_nome',
        'consignado_telefone',
        'preco_compra',
        'preco_tabela_fipe',
        'preco_anuncio',
        'preco_venda',
        'preco_minimo',
        'situacao_documental',
        'chave_reserva',
        'manual',
        'estado_conservacao',
        'gravame',
        'debitos',
        'ipva_pago',
        'licenciado',
        'data_entrada',
        'local_patio',
        'status',
        'destaque',
    ];

    protected function casts(): array
    {
        return [
            'preco_compra' => 'decimal:2',
            'preco_tabela_fipe' => 'decimal:2',
            'preco_anuncio' => 'decimal:2',
            'preco_venda' => 'decimal:2',
            'preco_minimo' => 'decimal:2',
            'debitos' => 'decimal:2',
            'chave_reserva' => 'boolean',
            'manual' => 'boolean',
            'gravame' => 'boolean',
            'ipva_pago' => 'boolean',
            'licenciado' => 'boolean',
            'destaque' => 'boolean',
            'data_entrada' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Veiculo $veiculo) {
            if (! $veiculo->slug) {
                $veiculo->slug = $veiculo->gerarSlugUnico();
            }

            if (! $veiculo->data_entrada) {
                $veiculo->data_entrada = now();
            }
        });
    }

    protected function gerarSlugUnico(): string
    {
        $base = Str::slug("{$this->marca}-{$this->modelo}-{$this->ano_modelo}");
        $slug = $base;
        $contador = 1;

        while (static::withoutGlobalScopes()->where('empresa_id', $this->empresa_id)->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$contador}";
            $contador++;
        }

        return $slug;
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function filial(): BelongsTo
    {
        return $this->belongsTo(Filial::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(VeiculoFoto::class)->orderBy('ordem');
    }

    public function fotoPrincipal(): HasMany
    {
        return $this->hasMany(VeiculoFoto::class)->where('principal', true);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(VeiculoVideo::class);
    }

    public function opcionais(): HasMany
    {
        return $this->hasMany(VeiculoOpcional::class);
    }

    public function anotacoes(): HasMany
    {
        return $this->hasMany(VeiculoAnotacao::class)->latest();
    }

    public function custos(): HasMany
    {
        return $this->hasMany(CustoVeiculo::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusVariant(): string
    {
        return match ($this->status) {
            'disponivel' => 'success',
            'reservado' => 'warning',
            'vendido', 'entregue' => 'info',
            'devolvido' => 'error',
            default => 'neutral',
        };
    }

    public function diasEmPatio(): int
    {
        return $this->data_entrada ? (int) $this->data_entrada->diffInDays(now()) : 0;
    }

    public function margem(): ?float
    {
        if (! $this->preco_venda || ! $this->preco_compra) {
            return null;
        }

        $custos = $this->custos()->sum('valor');

        return (float) $this->preco_venda - (float) $this->preco_compra - (float) $custos;
    }
}
