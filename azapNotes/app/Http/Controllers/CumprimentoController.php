<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class CumprimentoController extends Controller
{
    public static function getCumprimento(): string
    {
        $horaAtual = (int) Carbon::now()->format('H');

        if ($horaAtual <= 12) {
            return 'Boníssimo dia, ';
        } elseif ($horaAtual <= 18) {
            return 'Boníssima tarde, ';
        } else {
            return 'Bonisíssima noite, ';
        }
    }
}
