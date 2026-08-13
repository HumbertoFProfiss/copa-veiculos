<?php

namespace App\Events;

use App\Models\Venda;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendaConfirmada
{
    use Dispatchable, SerializesModels;

    public function __construct(public Venda $venda) {}
}
