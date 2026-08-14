<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotaFiscal;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaFiscalPdfController extends Controller
{
    public function __invoke(NotaFiscal $notaFiscal)
    {
        $this->authorize('vendas.ver');

        $notaFiscal->load(['venda.veiculo', 'venda.cliente', 'emissor']);

        $pdf = Pdf::loadView('pdf.nota-fiscal-simulada', ['nota' => $notaFiscal]);

        return $pdf->stream("nfe-simulada-{$notaFiscal->numero}.pdf");
    }
}
