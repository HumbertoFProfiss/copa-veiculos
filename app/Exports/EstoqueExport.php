<?php

namespace App\Exports;

use App\Models\Veiculo;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EstoqueExport implements FromCollection, Responsable, WithHeadings, WithMapping
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private string $fileName = 'estoque.xlsx';

    private string $writerType = \Maatwebsite\Excel\Excel::XLSX;

    public function collection()
    {
        return Veiculo::with('fornecedor')->orderBy('marca')->get();
    }

    public function headings(): array
    {
        return ['Marca', 'Modelo', 'Ano', 'KM', 'Status', 'Dias em pátio', 'Preço de compra', 'Preço de venda', 'Margem'];
    }

    public function map($veiculo): array
    {
        return [
            $veiculo->marca,
            $veiculo->modelo,
            "{$veiculo->ano_fabricacao}/{$veiculo->ano_modelo}",
            $veiculo->km,
            $veiculo->statusLabel(),
            $veiculo->diasEmPatio(),
            $veiculo->preco_compra,
            $veiculo->preco_venda,
            $veiculo->margem(),
        ];
    }
}
