<?php

namespace App\Livewire\Usuarios;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Filial;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithDataTable;

    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public string $name = '';

    public string $email = '';

    public ?string $telefone = '';

    public string $papel = 'Vendedor';

    public ?int $filial_id = null;

    public ?string $password = '';

    public bool $ativo = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->where('empresa_id', app('tenant')->id)->ignore($this->editandoId),
            ],
            'telefone' => 'nullable|string|max:20',
            'papel' => 'required|string|exists:roles,name',
            'filial_id' => 'nullable|exists:filiais,id',
            'password' => $this->editandoId ? 'nullable|string|min:6' : 'required|string|min:6',
        ];
    }

    public function novo(): void
    {
        $this->authorize('usuarios.criar');
        $this->reset(['editandoId', 'name', 'email', 'telefone', 'password', 'filial_id']);
        $this->papel = 'Vendedor';
        $this->ativo = true;
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $this->authorize('usuarios.editar');
        $user = User::findOrFail($id);
        $this->editandoId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->telefone = $user->telefone;
        $this->ativo = $user->ativo;
        $this->papel = $user->getRoleNames()->first() ?? 'Vendedor';
        $this->filial_id = $user->filial_id;
        $this->password = '';
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize($this->editandoId ? 'usuarios.editar' : 'usuarios.criar');

        $dados = $this->validate();

        if ($this->editandoId) {
            $user = User::findOrFail($this->editandoId);
            $user->update([
                'name' => $dados['name'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone'],
                'ativo' => $this->ativo,
                'filial_id' => $dados['filial_id'],
                ...(filled($dados['password']) ? ['password' => Hash::make($dados['password'])] : []),
            ]);
        } else {
            $user = User::create([
                'name' => $dados['name'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone'],
                'password' => Hash::make($dados['password']),
                'ativo' => true,
                'filial_id' => $dados['filial_id'],
                'email_verified_at' => now(),
            ]);
        }

        $user->syncRoles([$dados['papel']]);

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Usuário salvo com sucesso.');
    }

    public function excluir(int $id): void
    {
        $this->authorize('usuarios.excluir');

        if ($id === auth()->id()) {
            $this->addError('geral', 'Você não pode remover a si mesmo.');

            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('sucesso', 'Usuário removido.');
    }

    protected function query(): Builder
    {
        return User::query()
            ->with('roles')
            ->when($this->busca, fn (Builder $q) => $q->where('name', 'like', "%{$this->busca}%")
                ->orWhere('email', 'like', "%{$this->busca}%"));
    }

    public function render()
    {
        return view('livewire.usuarios.index', [
            'usuarios' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
            'papeis' => Role::pluck('name'),
            'filiais' => Filial::where('ativa', true)->orderBy('nome')->get(),
        ])->layout('layouts.admin', ['title' => 'Usuários']);
    }
}
