<?php

use App\Http\Controllers\AssistantDashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\DirecteurDashboardController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\ImpressionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\BonCommandeController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ReleveController;
use App\Http\Controllers\FicheProductionController;
use App\Http\Controllers\LivraisonController;
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

    Route::post('/pointages/store', [PointageController::class, 'store'])
        ->name('pointages.store');

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
    | IMPRESSION
    |--------------------------------------------------------------------------
    */

    // Page de sélection (employés + période) pour le bulletin de salaire
    Route::get('/impressions', [ImpressionController::class, 'index'])
        ->name('impressions.index');

    // Bulletin de salaire imprimable — plusieurs employés sélectionnés
    Route::post('/impressions/apercu', [ImpressionController::class, 'apercu'])
        ->name('impressions.apercu');

    // Bulletin de salaire imprimable — un seul employé (depuis sa fiche)
    Route::get('/impressions/employe/{employe}', [ImpressionController::class, 'ficheEmploye'])
        ->name('impressions.fiche-employe');

    // Badges QR
    Route::post('/impressions/badges', [ImpressionController::class, 'badges'])
        ->name('impressions.badges');

    // Rapport statistique par département
    Route::get('/impressions/statistiques', [ImpressionController::class, 'statistiques'])
        ->name('impressions.statistiques');

    // Feuille de présence imprimable (jour ou plage)
    Route::get('/impressions/feuille-presence', [ImpressionController::class, 'feuillePresence'])
        ->name('impressions.feuille-presence');

    /*
    |--------------------------------------------------------------------------
    | PROFIL
    |--------------------------------------------------------------------------
    */

    Route::get('/profil', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profil/login', [ProfileController::class, 'updateLogin'])
        ->name('profile.login');

    Route::put('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    /*
    |--------------------------------------------------------------------------
    | PROGRAMMES / COMMANDES CLIENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/programmes', [ProgrammeController::class, 'index'])->name('programmes.index');
    Route::get('/programmes/creer', [ProgrammeController::class, 'create'])->name('programmes.create');
    Route::post('/programmes', [ProgrammeController::class, 'store'])->name('programmes.store');
    Route::get('/programmes/{programme}', [ProgrammeController::class, 'show'])->name('programmes.show');
    Route::delete('/programmes/{programme}', [ProgrammeController::class, 'destroy'])->name('programmes.destroy');
    Route::patch('/programmes/{programme}/statut', [ProgrammeController::class, 'updateStatut'])->name('programmes.statut');

    /*
    |--------------------------------------------------------------------------
    | BONS DE COMMANDE
    |--------------------------------------------------------------------------
    */

    Route::get('/programmes/{programme}/bons', [BonCommandeController::class, 'index'])->name('programmes.bons.index');
    Route::get('/programmes/{programme}/factures', [FactureController::class, 'index'])->name('programmes.factures.index');
    Route::post('/programmes/{programme}/bons', [BonCommandeController::class, 'store'])->name('programmes.bons.store');
    Route::get('/bons-commande/{bonCommande}', [BonCommandeController::class, 'show'])->name('programmes.bons.show');
    Route::get('/bons-commande/{bonCommande}/imprimer', [BonCommandeController::class, 'imprimer'])->name('programmes.bons.imprimer');
    Route::get('/bons-commande/{bonCommande}/facture', [FactureController::class, 'imprimer'])->name('programmes.bons.facture');

    // Fiche de production (groupée automatiquement depuis les articles du BC)
    Route::get('/programmes/{programme}/fiches', [FicheProductionController::class, 'index'])->name('programmes.fiches.index');
    Route::get('/bons-commande/{bonCommande}/fiche', [FicheProductionController::class, 'show'])->name('programmes.bons.fiche.show');
    Route::post('/bons-commande/{bonCommande}/fiche/note', [FicheProductionController::class, 'storeNote'])->name('programmes.bons.fiche.note');
    Route::get('/bons-commande/{bonCommande}/fiche/imprimer', [FicheProductionController::class, 'imprimer'])->name('programmes.bons.fiche.imprimer');
    Route::patch('/bons-commande/{bonCommande}/condition', [BonCommandeController::class, 'update'])->name('programmes.bons.condition');
    Route::delete('/bons-commande/{bonCommande}', [BonCommandeController::class, 'destroy'])->name('programmes.bons.destroy');

    // Articles (lignes) d'un bon de commande
    Route::post('/bons-commande/{bonCommande}/lignes', [BonCommandeController::class, 'storeLigneBonCommande'])->name('programmes.bons.lignes.store');
    Route::patch('/lignes-bon-commande/{ligneBonCommande}', [BonCommandeController::class, 'updateLigneBonCommande'])->name('programmes.bons.lignes.update');
    Route::delete('/lignes-bon-commande/{ligneBonCommande}', [BonCommandeController::class, 'destroyLigneBonCommande'])->name('programmes.bons.lignes.destroy');

    /*
    |--------------------------------------------------------------------------
    | CONTRAT (généré et actualisé automatiquement depuis le 1er BC)
    |--------------------------------------------------------------------------
    */

    Route::get('/programmes/{programme}/contrat', [ContratController::class, 'show'])->name('programmes.contrat.show');
    Route::put('/programmes/{programme}/contrat', [ContratController::class, 'update'])->name('programmes.contrat.update');
    Route::patch('/programmes/{programme}/contrat/signer', [ContratController::class, 'marquerSigne'])->name('programmes.contrat.signer');
    Route::get('/programmes/{programme}/contrat/imprimer', [ContratController::class, 'imprimer'])->name('programmes.contrat.imprimer');

    // Échéancier de paiement (Article 4 du contrat)
    Route::post('/programmes/{programme}/echeances', [ContratController::class, 'storeEcheance'])->name('programmes.contrat.echeances.store');
    Route::delete('/echeances/{echeancePaiement}', [ContratController::class, 'destroyEcheance'])->name('programmes.contrat.echeances.destroy');

    /*
    |--------------------------------------------------------------------------
    | RELEVÉ DE COMPTE (auto-généré à partir des factures + versements)
    |--------------------------------------------------------------------------
    */

    Route::get('/programmes/{programme}/releve', [ReleveController::class, 'show'])->name('programmes.releve.show');
    Route::get('/programmes/{programme}/releve/imprimer', [ReleveController::class, 'imprimer'])->name('programmes.releve.imprimer');
    Route::post('/programmes/{programme}/releve/paiements', [ReleveController::class, 'storePaiement'])->name('programmes.releve.paiements.store');
    Route::patch('/paiements/{paiement}', [ReleveController::class, 'updatePaiement'])->name('programmes.releve.paiements.update');
    Route::delete('/paiements/{paiement}', [ReleveController::class, 'destroyPaiement'])->name('programmes.releve.paiements.destroy');

    /*
    |--------------------------------------------------------------------------
    | LIVRAISONS (bordereaux + tableau de suivi)
    |--------------------------------------------------------------------------
    */

    Route::get('/programmes/{programme}/livraisons', [LivraisonController::class, 'index'])->name('programmes.livraisons.index');
    Route::get('/programmes/{programme}/livraisons/creer', [LivraisonController::class, 'create'])->name('programmes.livraisons.create');
    Route::post('/programmes/{programme}/livraisons', [LivraisonController::class, 'store'])->name('programmes.livraisons.store');
    Route::get('/programmes/{programme}/suivi-livraisons', [LivraisonController::class, 'suivi'])->name('programmes.livraisons.suivi');
    Route::get('/livraisons/{livraison}/imprimer', [LivraisonController::class, 'imprimer'])->name('programmes.livraisons.imprimer');
    Route::delete('/livraisons/{livraison}', [LivraisonController::class, 'destroy'])->name('programmes.livraisons.destroy');
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