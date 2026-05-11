<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Employé concerné
            |--------------------------------------------------------------------------
            */
            $table->foreignId('employe_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Utilisateur ayant effectué le pointage
            |--------------------------------------------------------------------------
            */
            $table->foreignId('cree_par')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Date du pointage
            |--------------------------------------------------------------------------
            */
            $table->date('date');

            /*
            |--------------------------------------------------------------------------
            | Heures
            |--------------------------------------------------------------------------
            */
            $table->time('arrivee')
                ->nullable();

            $table->time('sortie')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */
            $table->enum('statut', [
                'Présent',
                'Retard',
                'Absent',
                'Congé'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Méthode
            |--------------------------------------------------------------------------
            */
            $table->enum('methode', [
                'manuel',
                'qr'
            ])->default('manuel');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};