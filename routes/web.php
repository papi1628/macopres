<?php

use App\Http\Controllers\AssistantDashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\DirecteurDashboardController;
use App\Http\Controllers\CalendrierController;

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

    Route::get('/dashboard/assistant', [AssistantDashboardController::class, 'index'])
        ->name('dashboard.assistant');
    /*
    |--------------------------------------------------------------------------
    | EMPLOYÉS
    |--------------------------------------------------------------------------
    */

    Route::get('/employes', [EmployeController::class, 'index'])
        ->name('employes.index');

    Route::post('/employes', [EmployeController::class, 'store'])
        ->name('employes.store');

    Route::put('/employes/{employe}', [EmployeController::class, 'update'])
        ->name('employes.update');

    Route::delete('/employes/{employe}', [EmployeController::class, 'destroy'])
        ->name('employes.destroy');

    Route::get('/employes/{employe}/edit', [EmployeController::class, 'edit']);

    Route::get('/employes/{employe}/qr', [EmployeController::class, 'qr'])
        ->name('employes.qr');

    Route::get('/employes/{employe}/derniers-pointages', [PointageController::class, 'derniersPointages'])
        ->name('employes.derniers-pointages');


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
    | ROUTES POINTAGES
    |--------------------------------------------------------------------------
    */

    // Feuille de présence du jour
    Route::get('/pointages', [PointageController::class, 'index'])
        ->name('pointages.index');

    // Pointer manuellement (arrivée)
    Route::post('/pointages/pointer', [PointageController::class, 'pointer'])
        ->name('pointages.pointer');

    // Enregistrer le départ
    Route::patch('/pointages/{pointage}/depart', [PointageController::class, 'enregistrerDepart'])
        ->name('pointages.depart');

    // Scanner QR code (page)
    Route::get('/pointages/scan', [PointageController::class, 'scan'])
        ->name('pointages.scan');

    // Scanner QR code (API — appelée en AJAX)
    Route::post('/pointages/scan', [PointageController::class, 'scannerQr'])
        ->name('pointages.scanner');

    // Historique global
    Route::get('/pointages/historique', [PointageController::class, 'historique'])
        ->name('pointages.historique');

    // Statistiques globales
    Route::get('/pointages/statistiques', [PointageController::class, 'statistiques'])
        ->name('pointages.statistiques');

    // Fiche individuelle employé
    Route::get('/pointages/employe/{employe}', [PointageController::class, 'ficheEmploye'])
        ->name('pointages.fiche-employe');

    // Supprimer un pointage (correction)
    Route::delete('/pointages/{pointage}', [PointageController::class, 'destroy'])
        ->name('pointages.destroy');

    Route::get('/pointages/statistiques', [PointageController::class, 'statistiques'])
    ->name('pointages.statistiques');

    // Route Férié Payé
    Route::post('/pointages/pointer-fp', [PointageController::class, 'pointerFP'])
    ->name('pointages.pointer-fp');

    /*
    |--------------------------------------------------------------------------
    | CALENDRIER
    |--------------------------------------------------------------------------
    */

    /*Route::get('/calendrier', [CalendrierController::class, 'index'])
        ->name('calendrier.index');

    Route::get('/calendrier', [CalendrierController::class, 'index'])
        ->name('calendrier.index');

    Route::get('/calendrier/{date}', [CalendrierController::class, 'show'])
        ->name('calendrier.show');

    Route::post('/calendrier/evenements', [CalendrierController::class, 'store'])
        ->name('calendrier.store');*/

    Route::get('/calendrier', [CalendrierController::class, 'index'])
        ->name('calendrier.index');

    Route::post('/calendrier', [CalendrierController::class, 'store'])
        ->name('calendrier.store');

    Route::put('/calendrier/{calendrier}', [CalendrierController::class, 'update'])
        ->name('calendrier.update');

    Route::delete('/calendrier/{calendrier}', [CalendrierController::class, 'destroy'])
        ->name('calendrier.destroy');

    Route::get('/calendrier/ferie/{date}', [CalendrierController::class, 'jourFerie'])
        ->name('calendrier.ferie');
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