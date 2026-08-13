<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    /**
     * Modulos incluidos em cada plano base (ver prompt §5). "somente_site" e
     * o produto reduzido (§4.16): so estoque, recebimento de leads e
     * configuracoes - sem o ERP completo. Modulos fora dessa lista para um
     * plano so ficam acessiveis via modulos_ativos (add-on avulso).
     */
    public const MODULOS_POR_PLANO = [
        'somente_site' => ['dashboard', 'veiculos', 'crm', 'leads', 'configuracoes'],
        'completo' => [
            'dashboard', 'veiculos', 'clientes', 'fornecedores', 'importacoes',
            'crm', 'leads', 'vendas', 'contratos', 'financeiro', 'anuncios',
            'relatorios', 'usuarios', 'configuracoes', 'integracoes', 'filiais',
        ],
        'completo_opcionais' => [
            'dashboard', 'veiculos', 'clientes', 'fornecedores', 'importacoes',
            'crm', 'leads', 'vendas', 'contratos', 'financeiro', 'anuncios',
            'relatorios', 'usuarios', 'configuracoes', 'integracoes', 'filiais',
        ],
    ];

    /**
     * Preco mensal de cada plano (ver prompt §5 - "Completo R$300/mes,
     * Completo+Opcionais R$550/mes", competindo com o Boom Sistemas).
     * "somente_site" nao teve valor definido no prompt - R$97 e um
     * placeholder razoavel pro produto reduzido, ajustavel aqui se o
     * valor real de mercado definido pelo usuario for outro.
     */
    public const PRECOS_PLANOS = [
        'somente_site' => 97.00,
        'completo' => 300.00,
        'completo_opcionais' => 550.00,
    ];

    protected $fillable = [
        'nome',
        'cnpj',
        'slug',
        'dominio_customizado',
        'plano',
        'status',
        'trial_termina_em',
        'telefone',
        'whatsapp',
        'email_contato',
        'endereco',
        'cidade',
        'uf',
        'horario_funcionamento',
        'instagram_url',
        'facebook_url',
        'sobre_texto',
        'analytics_ga4_id',
        'analytics_gtm_id',
        'analytics_meta_pixel_id',
        'limite_destaques',
        'abc_limite_a',
        'abc_limite_b',
    ];

    protected function casts(): array
    {
        return [
            'trial_termina_em' => 'datetime',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function modulosAtivos(): HasMany
    {
        return $this->hasMany(ModuloAtivo::class);
    }

    public function possuiModulo(string $slug): bool
    {
        return $this->modulosAtivos()->where('modulo_slug', $slug)->exists();
    }

    /**
     * Acesso a um modulo do ERP: incluido no plano base OU liberado como
     * add-on avulso via modulos_ativos. Usado pelo middleware EnsureModuloAtivo.
     */
    public function possuiAcessoModulo(string $modulo): bool
    {
        $incluidosNoPlano = self::MODULOS_POR_PLANO[$this->plano] ?? [];

        return in_array($modulo, $incluidosNoPlano, true) || $this->possuiModulo($modulo);
    }

    public function whatsappUrl(?string $mensagem = null): ?string
    {
        if (blank($this->whatsapp)) {
            return null;
        }

        $numero = preg_replace('/\D/', '', $this->whatsapp);
        $numero = str_starts_with($numero, '55') ? $numero : '55'.$numero;

        $url = "https://wa.me/{$numero}";

        if (filled($mensagem)) {
            $url .= '?text='.rawurlencode($mensagem);
        }

        return $url;
    }
}
