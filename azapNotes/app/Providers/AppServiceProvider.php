<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        if (User::where('email', 'admin@admin.com.br')->doesntExist()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com.br',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'ativo'
            ]);
        }
    }
}
