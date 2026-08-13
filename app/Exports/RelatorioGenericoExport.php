<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Exportacao generica pro construtor de relatorios (App\Livewire\Relatorios\Construtor) -
 * exporta exatamente os campos selecionados pelo usuario, na ordem escolhida,
 * reaproveitando os mesmos rotulos de enum (status, estagio...) mostrados na tela.
 */
class RelatorioGenericoExport implements FromCollection, Responsable, WithHeadings, WithMapping
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private string $fileName = 'relatorio.xlsx';

    private string $writerType = \Maatwebsite\Excel\Excel::XLSX;

    /**
     * @param  Collection  $linhas
     * @param  array<int, string>  $camposSelecionados
     * @param  array<string, string>  $rotulosCampos
     * @param  array<string, array<string|bool, string>>  $labelsValor
     */
    public function __construct(
        private Collection $linhas,
        private array $camposSelecionados,
        private array $rotulosCampos,
        private array $labelsValor,
    ) {}

    public function collection()
    {
        return $this->linhas;
    }

    public function headings(): array
    {
        return array_map(fn ($campo) => $this->rotulosCampos[$campo] ?? $campo, $this->camposSelecionados);
    }

    public function map($linha): array
    {
        return array_map(function ($campo) use ($linha) {
            $valor = $linha->{$campo};

            if (isset($this->labelsValor[$campo])) {
                return $this->labelsValor[$campo][$valor] ?? $valor;
            }

            if (is_bool($valor)) {
                return $valor ? 'Sim' : 'Não';
            }

            return $valor instanceof \DateTimeInterface ? $valor->format('d/m/Y') : $valor;
        }, $this->camposSelecionados);
    }
}
