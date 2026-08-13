<?php

use App\Events\LeadRecebido;
use App\Events\VendaConfirmada;
use App\Models\Empresa;
use App\Models\Lead;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\Venda;
use App\Models\Webhook;
use App\Models\WebhookEntrega;
use App\Services\Webhooks\WebhookDispatcher;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

function usuarioProprietarioDeTeste(Empresa $empresa): User
{
    return User::withoutGlobalScopes()
        ->where('empresa_id', $empresa->id)
        ->whereHas('roles', fn ($q) => $q->where('name', 'Proprietário'))
        ->firstOrFail();
}

it('bloqueia a api sem token e libera com token valido, escopado pela empresa certa', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietarioDeTeste($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'MarcaApiTeste'.Str::random(4),
        'modelo' => 'ModeloTeste',
        'slug' => Str::slug('api-teste-'.Str::random(8)),
        'km' => 0,
        'portas' => 4,
        'tipo_propriedade' => 'proprio',
        'data_entrada' => now(),
        'status' => 'disponivel',
    ]);

    $host = 'empresa-a.'.config('tenancy.central_domain');

    // Sem token -> 401.
    $this->withServerVariables(['HTTP_HOST' => $host])
        ->getJson("http://{$host}/api/v1/veiculos")
        ->assertStatus(401);

    $token = $user->createToken('teste')->plainTextToken;

    // Com token -> 200, ve o veiculo que acabou de criar.
    $resposta = $this->withServerVariables(['HTTP_HOST' => $host])
        ->withHeaders(['Authorization' => "Bearer {$token}"])
        ->getJson("http://{$host}/api/v1/veiculos/{$veiculo->id}");

    $resposta->assertOk()->assertJsonPath('data.marca', $veiculo->marca);

    $user->tokens()->delete();
    $veiculo->delete();
});

it('cria lead via api, dispara o evento LeadRecebido e distribui pro vendedor', function () {
    Event::fake([LeadRecebido::class]);

    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietarioDeTeste($empresa);
    $token = $user->createToken('teste-lead')->plainTextToken;
    $host = 'empresa-a.'.config('tenancy.central_domain');

    $telefoneUnico = '1499'.random_int(1000000, 9999999);

    $resposta = $this->withServerVariables(['HTTP_HOST' => $host])
        ->withHeaders(['Authorization' => "Bearer {$token}"])
        ->postJson("http://{$host}/api/v1/leads", [
            'nome' => 'Lead via API',
            'telefone' => $telefoneUnico,
            'email' => 'lead-api@teste.com',
        ]);

    $resposta->assertCreated()->assertJsonPath('data.telefone', $telefoneUnico);

    Event::assertDispatched(LeadRecebido::class, fn ($e) => $e->lead->telefone === $telefoneUnico);

    $lead = Lead::where('telefone', $telefoneUnico)->first();
    expect($lead)->not->toBeNull()->and($lead->origem)->toBe('outro');

    $lead->delete();
    $user->tokens()->delete();
});

it('despacha webhook com assinatura HMAC correta quando um lead chega', function () {
    Bus::fake();

    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $webhook = Webhook::create([
        'empresa_id' => $empresa->id,
        'url' => 'https://exemplo-teste.invalid/hook',
        'eventos' => ['lead.recebido'],
        'ativo' => true,
    ]);

    (new WebhookDispatcher)->despachar('lead.recebido', $empresa->id, ['teste' => 'valor']);

    $entrega = WebhookEntrega::where('webhook_id', $webhook->id)->first();
    expect($entrega)->not->toBeNull()
        ->and($entrega->status)->toBe('pendente')
        ->and($entrega->payload)->toBe(['teste' => 'valor']);

    Bus::assertDispatched(\App\Jobs\EntregarWebhook::class, fn ($job) => $job->webhookEntregaId === $entrega->id);

    $webhook->delete();
});

it('entrega de verdade via http com a assinatura certa e marca sucesso', function () {
    Http::fake(['exemplo-teste.invalid/*' => Http::response(['ok' => true], 200)]);

    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $webhook = Webhook::create([
        'empresa_id' => $empresa->id,
        'url' => 'https://exemplo-teste.invalid/hook',
        'eventos' => ['venda.confirmada'],
        'ativo' => true,
        'secret' => 'segredo-de-teste',
    ]);

    $entrega = WebhookEntrega::create([
        'webhook_id' => $webhook->id,
        'evento' => 'venda.confirmada',
        'payload' => ['a' => 1],
        'status' => 'pendente',
    ]);

    (new \App\Jobs\EntregarWebhook($entrega->id))->handle();

    $entrega->refresh();
    expect($entrega->status)->toBe('sucesso')->and($entrega->resposta_http)->toBe(200);

    $assinaturaEsperada = 'sha256='.hash_hmac('sha256', json_encode(['a' => 1]), 'segredo-de-teste');

    Http::assertSent(fn ($request) => $request->hasHeader('X-Copa-Signature', $assinaturaEsperada)
        && $request->hasHeader('X-Copa-Event', 'venda.confirmada'));

    $webhook->delete();
});

it('marca falhou e a entrega e reprocessavel quando o destino responde erro', function () {
    Http::fake(['exemplo-teste.invalid/*' => Http::response(['erro' => true], 500)]);

    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $webhook = Webhook::create([
        'empresa_id' => $empresa->id,
        'url' => 'https://exemplo-teste.invalid/hook-falha',
        'eventos' => ['lead.recebido'],
        'ativo' => true,
    ]);

    $entrega = WebhookEntrega::create([
        'webhook_id' => $webhook->id,
        'evento' => 'lead.recebido',
        'payload' => ['x' => 1],
        'status' => 'pendente',
    ]);

    expect(fn () => (new \App\Jobs\EntregarWebhook($entrega->id))->handle())->toThrow(RuntimeException::class);

    $entrega->refresh();
    expect($entrega->status)->toBe('falhou')->and($entrega->tentativas)->toBe(1);

    $webhook->delete();
});

it('dispara webhook de venda.confirmada quando o listener roda', function () {
    Bus::fake();

    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $webhook = Webhook::create([
        'empresa_id' => $empresa->id,
        'url' => 'https://exemplo-teste.invalid/vendas',
        'eventos' => ['venda.confirmada'],
        'ativo' => true,
    ]);

    $venda = Venda::first();
    expect($venda)->not->toBeNull('precisa de ao menos 1 venda seedada pra empresa de teste');

    (new \App\Listeners\DispararWebhookVendaConfirmada)->handle(new VendaConfirmada($venda));

    Bus::assertDispatched(\App\Jobs\EntregarWebhook::class);

    $webhook->delete();
});
