<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


    // Departamentos
    Route::prefix('departamentos')->name('departamentos.')->group(function () {
        Route::get('/', [DepartamentoController::class, 'index'])->name('index');
        Route::get('/criar', [DepartamentoController::class, 'create'])->name('create');
        Route::post('/', [DepartamentoController::class, 'store'])->name('store');
        Route::get('/{noticia}', [DepartamentoController::class, 'show'])->name('show');
        Route::get('/{noticia}/editar', [DepartamentoController::class, 'edit'])->name('edit');
        Route::put('/{noticia}', [DepartamentoController::class, 'update'])->name('update');
        Route::delete('/{noticia}', [DepartamentoController::class, 'destroy'])->name('destroy');
    });

    // Funcionarios
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Links
    Route::prefix('links')->name('links.')->group(function () {
        Route::get('/', [LinkController::class, 'index'])->name('index');
        Route::get('/criar', [LinkController::class, 'create'])->name('create');
        Route::post('/', [LinkController::class, 'store'])->name('store');
        Route::get('/{noticia}', [LinkController::class, 'show'])->name('show');
        Route::get('/{noticia}/editar', [LinkController::class, 'edit'])->name('edit');
        Route::put('/{noticia}', [LinkController::class, 'update'])->name('update');
        Route::delete('/{noticia}', [LinkController::class, 'destroy'])->name('destroy');
    });

    // Tutoriais
    Route::prefix('tutoriais')->name('tutoriais.')->group(function () {
        Route::get('/', [TutorialController::class, 'index'])->name('index');
        Route::get('/criar', [TutorialController::class, 'create'])->name('create');
        Route::post('/', [TutorialController::class, 'store'])->name('store');
        Route::get('/{tutorial}', [TutorialController::class, 'show'])->name('show');
        Route::get('/{tutorial}/editar', [TutorialController::class, 'edit'])->name('edit');
        Route::put('/{tutorial}', [TutorialController::class, 'update'])->name('update');
        Route::delete('/{tutorial}', [TutorialController::class, 'destroy'])->name('destroy');
    });

    // Notícias
    Route::prefix('noticias')->name('noticias.')->group(function () {
        Route::get('/', [NoticiaController::class, 'index'])->name('index');
        Route::get('/criar', [NoticiaController::class, 'create'])->name('create');
        Route::post('/', [NoticiaController::class, 'store'])->name('store');
        Route::get('/{noticia}', [NoticiaController::class, 'show'])->name('show');
        Route::get('/{noticia}/editar', [NoticiaController::class, 'edit'])->name('edit');
        Route::put('/{noticia}', [NoticiaController::class, 'update'])->name('update');
        Route::delete('/{noticia}', [NoticiaController::class, 'destroy'])->name('destroy');
    });

    // Documentos
    Route::prefix('documentos')->name('documentos.')->group(function () {
        Route::get('/', [DocumentoController::class, 'index'])->name('index');
        Route::get('/criar', [DocumentoController::class, 'create'])->name('create');
        Route::post('/', [DocumentoController::class, 'store'])->name('store');
        Route::get('/{documento}', [DocumentoController::class, 'show'])->name('show');
        Route::get('/{documento}/editar', [DocumentoController::class, 'edit'])->name('edit');
        Route::put('/{documento}', [DocumentoController::class, 'update'])->name('update');
        Route::delete('/{documento}', [DocumentoController::class, 'destroy'])->name('destroy');
        Route::get('/busca', [DocumentoController::class, 'busca'])->name('busca');
    });
});

require __DIR__.'/auth.php';