<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'cumprimento' => $this->cumprimento()
        ]);
    }

    public function cumprimento(): string
    {
        $horaAtual = (int) Carbon::now()->format('H');
        $cumprimento = '';

        if ($horaAtual <= 12) {
            $cumprimento = 'Boníssimo dia, ';
        } else if ($horaAtual > 12 && $horaAtual <= 18) {
            $cumprimento = 'Boníssima tarde, ';
        } else {
            $cumprimento = 'Bonisíssima noite, ';
        }

        return $cumprimento;
    }
}
