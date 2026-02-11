<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Usuários: listar e criar para Gerente (2) e Admin (3); editar/excluir só Admin (3)
    Route::middleware(['check.permission:2,3'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware(['check.permission:3'])->group(function () {
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Pedidos: todos os níveis (1, 2, 3)
    Route::middleware(['check.permission:1,2,3'])->group(function () {
        Route::resource('orders', OrderController::class);
    });

    // Relatórios: Gerente (2) e Admin (3)
    Route::middleware(['check.permission:2,3'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/mesa/{mesa}/fechar-venda', [ReportController::class, 'fecharVenda'])->name('reports.fechar-venda');
        Route::post('/reports/encerrar-mesa/{mesa}', [ReportController::class, 'encerrarMesa'])->name('reports.encerrar-mesa');
    });
});
