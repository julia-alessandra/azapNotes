<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use App\Http\Controllers\CumprimentoController;


class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'cumprimento' => CumprimentoController::getCumprimento()
        ]);
    }

}
