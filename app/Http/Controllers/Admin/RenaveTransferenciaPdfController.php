<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RenaveTransferencia;
use Barryvdh\DomPDF\Facade\Pdf;

class RenaveTransferenciaPdfController extends Controller
{
    public function __invoke(RenaveTransferencia $renaveTransferencia)
    {
        $this->authorize('vendas.ver');

        $renaveTransferencia->load(['venda.veiculo', 'venda.cliente', 'geradaPor']);

        $pdf = Pdf::loadView('pdf.renave-simulada', ['transferencia' => $renaveTransferencia]);

        return $pdf->stream("renave-simulado-{$renaveTransferencia->protocolo}.pdf");
    }
}
