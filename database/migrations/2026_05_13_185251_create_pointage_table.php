<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pointages');
        
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Employé pointé
            |--------------------------------------------------------------------------
            */
            $table->foreignId('employe_id')
                ->constrained('employes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Date et horaires
            |--------------------------------------------------------------------------
            */
            $table->date('date');                          // 2025-05-13
            $table->time('heure_arrivee')->nullable();     // 08:32:00
            $table->time('heure_depart')->nullable();      // 17:15:00

            /*
            |--------------------------------------------------------------------------
            | Statut et type
            |--------------------------------------------------------------------------
            | statut : present, absent, retard
            | type   : manuel, qr_code
            */
            $table->enum('statut', ['present', 'absent', 'retard'])->default('present');
            $table->enum('type', ['manuel', 'qr_code'])->default('manuel');

            /*
            |--------------------------------------------------------------------------
            | Calculs
            |--------------------------------------------------------------------------
            */
            $table->decimal('heures_travaillees', 5, 2)->nullable(); // ex: 8.75
            $table->decimal('salaire_jour', 10, 2)->nullable();      // salaire journalier calculé
            $table->boolean('retard')->default(false);               // arrivée après 8h45
            $table->integer('minutes_retard')->default(0);           // nb minutes de retard

            /*
            |--------------------------------------------------------------------------
            | Qui a fait le pointage
            |--------------------------------------------------------------------------
            */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Unicité : un seul pointage par employé par jour
            |--------------------------------------------------------------------------
            */
            $table->unique(['employe_id', 'date']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};