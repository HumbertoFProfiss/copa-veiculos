<?php

namespace App\Livewire\Shared;

use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Lead;
use App\Models\User;
use App\Models\Veiculo;
use Livewire\Component;

/**
 * Busca global (Ctrl+K) - montado dentro do modal de layouts/admin.blade.php,
 * aberto via atalho de teclado ou pelo botão de busca no topbar. Cada
 * categoria só é buscada se o usuário tem permissão de "ver" aquele módulo,
 * e cada resultado linka pra listagem já filtrada (?q=termo, suportado por
 * todo módulo que usa WithDataTable) ou, quando existe, direto pro registro
 * (caso dos veículos).
 */
class GlobalSearch extends Component
{
    public string $termo = '';

    public function getResultadosProperty(): array
    {
        $termo = trim($this->termo);

        if (mb_strlen($termo) < 2) {
            return [];
        }

        $like = "%{$termo}%";
        $user = auth()->user();
        $resultados = [];

        if ($user->can('veiculos.ver')) {
            $veiculos = Veiculo::query()
                ->where(function ($q) use ($like) {
                    $q->where('marca', 'like', $like)
                        ->orWhere('modelo', 'like', $like)
                        ->orWhere('placa', 'like', $like)
                        ->orWhere('numero_estoque', 'like', $like);
                })
                ->limit(5)
                ->get(['id', 'marca', 'modelo', 'placa', 'numero_estoque']);

            if ($veiculos->isNotEmpty()) {
                $resultados['Veículos'] = $veiculos->map(fn (Veiculo $v) => [
                    'titulo' => "{$v->marca} {$v->modelo}",
                    'subtitulo' => trim(($v->placa ?? '').' · '.($v->numero_estoque ?? ''), ' ·'),
                    'url' => route('admin.veiculos.editar', $v),
                ]);
            }
        }

        if ($user->can('clientes.ver')) {
            $clientes = Cliente::query()
                ->where(function ($q) use ($like) {
                    $q->where('nome', 'like', $like)
                        ->orWhere('cpf', 'like', $like)
                        ->orWhere('telefone', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->limit(5)
                ->get(['id', 'nome', 'telefone', 'email']);

            if ($clientes->isNotEmpty()) {
                $resultados['Clientes'] = $clientes->map(fn (Cliente $c) => [
                    'titulo' => $c->nome,
                    'subtitulo' => $c->telefone ?? $c->email ?? '',
                    'url' => route('admin.clientes.index', ['q' => $c->nome]),
                ]);
            }
        }

        if ($user->can('leads.ver')) {
            $leads = Lead::query()
                ->where(function ($q) use ($like) {
                    $q->where('nome', 'like', $like)
                        ->orWhere('telefone', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->limit(5)
                ->get(['id', 'nome', 'telefone', 'estagio']);

            if ($leads->isNotEmpty()) {
                $resultados['Leads'] = $leads->map(fn (Lead $l) => [
                    'titulo' => $l->nome ?: 'Lead sem nome',
                    'subtitulo' => trim(($l->telefone ?? '').' · '.$l->estagioLabel(), ' ·'),
                    'url' => route('admin.leads.inbox', ['q' => $l->nome ?: $l->telefone]),
                ]);
            }
        }

        if ($user->can('fornecedores.ver')) {
            $fornecedores = Fornecedor::query()
                ->where('nome', 'like', $like)
                ->limit(5)
                ->get(['id', 'nome', 'tipo']);

            if ($fornecedores->isNotEmpty()) {
                $resultados['Fornecedores'] = $fornecedores->map(fn (Fornecedor $f) => [
                    'titulo' => $f->nome,
                    'subtitulo' => ucfirst($f->tipo ?? ''),
                    'url' => route('admin.fornecedores.index', ['q' => $f->nome]),
                ]);
            }
        }

        if ($user->can('usuarios.ver')) {
            $usuarios = User::query()
                ->where(function ($q) use ($like) {
                    $q->where('name', 'like', $like)->orWhere('email', 'like', $like);
                })
                ->limit(5)
                ->get(['id', 'name', 'email']);

            if ($usuarios->isNotEmpty()) {
                $resultados['Equipe'] = $usuarios->map(fn (User $u) => [
                    'titulo' => $u->name,
                    'subtitulo' => $u->email,
                    'url' => route('admin.usuarios.index', ['q' => $u->name]),
                ]);
            }
        }

        return $resultados;
    }

    public function render()
    {
        return view('livewire.shared.global-search', [
            'resultados' => $this->resultados,
        ]);
    }
}
