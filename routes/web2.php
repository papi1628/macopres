<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\DirecteurDashboardController;

/*
|--------------------------------------------------------------------------
| ROUTE PUBLIQUE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard/directeur', [DirecteurDashboardController::class, 'index'])
        ->name('dashboard.directeur');

    /*
    |--------------------------------------------------------------------------
    | EMPLOYÉS
    |--------------------------------------------------------------------------
    */

    Route::resource('employes', EmployeController::class);

    /*
    |--------------------------------------------------------------------------
    | QR CODE EMPLOYÉ
    |--------------------------------------------------------------------------
    */

    Route::get('/employes/{employe}/qr', [EmployeController::class, 'qr_code'])
        ->name('employes.qr');

    /*
    |--------------------------------------------------------------------------
    | POINTAGES
    |--------------------------------------------------------------------------
    */

    Route::get('/pointages', [PointageController::class, 'index'])
        ->name('pointages.index');

    Route::get('/pointages/scan', [PointageController::class, 'scan'])
        ->name('pointages.scan');

    Route::get('/pointages/historique', [PointageController::class, 'historique'])
        ->name('pointages.historique');

    Route::post('/pointages/store', [PointageController::class, 'store'])
        ->name('pointages.store');

    /*
    |--------------------------------------------------------------------------
    | DIRECTEUR UNIQUEMENT
    |--------------------------------------------------------------------------
    */

    Route::middleware('directeur')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ASSISTANTS
        |--------------------------------------------------------------------------
        */

        Route::get('/assistants', [AssistantController::class, 'index'])
            ->name('assistants.index');

        Route::get('/assistants/create', [AssistantController::class, 'create'])
            ->name('assistants.create');

        Route::post('/assistants', [AssistantController::class, 'store'])
            ->name('assistants.store');

        Route::get('/assistants/{id}/edit', [AssistantController::class, 'edit'])
            ->name('assistants.edit');

        Route::put('/assistants/{id}', [AssistantController::class, 'update'])
            ->name('assistants.update');

        Route::delete('/assistants/{id}', [AssistantController::class, 'destroy'])
            ->name('assistants.destroy');

        /*
        |--------------------------------------------------------------------------
        | RÔLES
        |--------------------------------------------------------------------------
        */

        Route::get('/roles', function () {
            return view('roles.index');
        })->name('roles.index');

    });

});