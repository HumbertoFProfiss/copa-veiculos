import './bootstrap';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

// Alpine.js não é importado/iniciado aqui de propósito: o Livewire 3 já
// empacota e inicializa sua própria instância do Alpine via @livewireScripts.
// Chamar Alpine.start() de novo aqui causa conflito (dupla inicialização) e
// quebra as diretivas wire:*.
