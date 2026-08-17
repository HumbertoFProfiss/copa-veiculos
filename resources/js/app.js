import './bootstrap';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

// Alpine.js não é importado/iniciado aqui de propósito: o Livewire 3 já
// empacota e inicializa sua própria instância do Alpine via @livewireScripts.
// Chamar Alpine.start() de novo aqui causa conflito (dupla inicialização) e
// quebra as diretivas wire:*.

// Máscaras de formatação usadas via x-on:input nos formulários (ex: fornecedores).
// wire:model (mesmo sem .live) sincroniza o valor atual do input no proximo
// request, entao o texto ja formatado aqui e o que acaba salvo/exibido.
window.maskCpfCnpj = function (valor) {
    const digitos = valor.replace(/\D/g, '').slice(0, 14);

    if (digitos.length <= 11) {
        return digitos
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    return digitos
        .replace(/(\d{2})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2')
        .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
};

window.maskTelefone = function (valor) {
    const digitos = valor.replace(/\D/g, '').slice(0, 11);

    return digitos
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{4,5})(\d{4})$/, '$1-$2');
};
