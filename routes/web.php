<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddTecnologiaController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;

use App\Models\ActivityLog;
use App\Models\Tecnologia;

use App\Support\ActivityLogger;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisteredUserController::class, 'store']);

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {

    if (Auth::check()) {
        ActivityLogger::log(
            ActivityLog::ACTION_LOGOUT,
            'Logout realizado',
            Auth::user()
        );
    }

    Auth::logout();

    return redirect('/login');

})->name('logout');

/*
|--------------------------------------------------------------------------
| ROTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $tecnologias = Tecnologia::with(['situacao', 'estagio'])
            ->orderByDesc('data_submissao')
            ->get();

        return view('dashboard', compact('tecnologias'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');

    Route::get('/user', function () {
        return view('home');
    })->name('user');

    Route::resource('add_tecnologia', AddTecnologiaController::class)
        ->parameters(['add_tecnologia' => 'tecnologia']);

}); // fim: auth

/*
|--------------------------------------------------------------------------
| ROTAS ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{user}', [UserController::class, 'show'])->name('usuarios.show');
        Route::patch('/usuarios/{user}/admin', [UserController::class, 'toggleAdmin'])->name('usuarios.toggleAdmin');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');

        Route::get('/atividades', [ActivityLogController::class, 'index'])->name('atividades.index');
        Route::get('/usuarios/{user}/atividades', [ActivityLogController::class, 'userActivities'])->name('usuarios.atividades');

        Route::resource('add_tecnologia', AddTecnologiaController::class)
            ->parameters(['add_tecnologia' => 'tecnologia']);

}); // fim: admin

/*
|--------------------------------------------------------------------------
| FALLBACK 404
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return view('errors.404');
});