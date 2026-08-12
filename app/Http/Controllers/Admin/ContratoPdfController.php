<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContratoPdfController extends Controller
{
    public function __invoke(Contrato $contrato): StreamedResponse
    {
        $this->authorize('contratos.ver');

        abort_unless($contrato->pdf_path && Storage::disk('local')->exists($contrato->pdf_path), 404);

        return Storage::disk('local')->response($contrato->pdf_path, "{$contrato->numero}.pdf");
    }
}
