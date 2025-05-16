<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CumprimentoController;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {

        $users = User::all();

        return view('dashAdm', [
            'cumprimento' => CumprimentoController::getCumprimento(),
            'users' => $users
        ]);
        
    }
}
