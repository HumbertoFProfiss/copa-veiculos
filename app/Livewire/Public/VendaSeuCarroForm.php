<?php

namespace App\Livewire\Public;

use App\Models\Lead;
use App\Services\Leads\LeadDistribuidor;
use Livewire\Component;

/**
 * Formulário "venda seu carro" da home pública - cria lead sem veículo
 * vinculado, com os dados do carro do interessado embutidos na mensagem
 * original (mesmo padrão do InteresseForm quanto a rate limit/anti-spam).
 */
class VendaSeuCarroForm extends Component
{
    public string $nome = '';

    public string $telefone = '';

    public ?string $email = '';

    public ?string $marca = '';

    public ?string $modelo = '';

    public ?string $ano = '';

    public ?string $km = '';

    public ?string $valorPretendido = '';

    public ?string $observacoes = '';

    public bool $enviado = false;

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:150',
            'telefone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'marca' => 'nullable|string|max:60',
            'modelo' => 'nullable|string|max:60',
            'ano' => 'nullable|string|max:4',
            'km' => 'nullable|string|max:30',
            'valorPretendido' => 'nullable|string|max:30',
            'observacoes' => 'nullable|string|max:1000',
        ];
    }

    public function enviar(): void
    {
        $chaveLimite = 'venda-seu-carro:'.request()->ip();

        if (cache()->get($chaveLimite, 0) >= 5) {
            $this->addError('nome', 'Muitas tentativas. Tente novamente em alguns minutos.');

            return;
        }

        $this->validate();

        $resumoCarro = trim("{$this->marca} {$this->modelo} {$this->ano}");
        $mensagem = "Carro: {$resumoCarro} | KM: {$this->km} | Valor pretendido: {$this->valorPretendido}";
        if ($this->observacoes) {
            $mensagem .= " | Obs: {$this->observacoes}";
        }

        $lead = Lead::create([
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'email' => $this->email ?: null,
            'origem' => 'venda_seu_carro',
            'mensagem_original' => $mensagem,
            'estagio' => 'novo',
            'ip_origem' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        (new LeadDistribuidor)->distribuir($lead);

        cache()->put($chaveLimite, cache()->get($chaveLimite, 0) + 1, now()->addMinutes(10));

        $this->reset(['nome', 'telefone', 'email', 'marca', 'modelo', 'ano', 'km', 'valorPretendido', 'observacoes']);
        $this->enviado = true;
    }

    public function render()
    {
        return view('livewire.public.venda-seu-carro-form', [
            'whatsappUrl' => app('tenant')?->whatsappUrl(),
        ]);
    }
}
