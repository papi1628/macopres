<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Lien avec users
            |--------------------------------------------------------------------------
            | NULL = journalier ou employé sans accès système
            | rempli = assistant connecté au système
            */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informations employé
            |--------------------------------------------------------------------------
            */
            $table->string('matricule')->unique();

            $table->string('nom');

            $table->string('prenom');

            $table->string('tel')
                ->nullable();

            $table->string('poste')
                ->nullable();

            $table->string('departement')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | QR Code
            |--------------------------------------------------------------------------
            */
            $table->string('qr_code')
                ->unique()
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */
            $table->boolean('actif')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | RH
            |--------------------------------------------------------------------------
            */
            $table->date('date_embauche')
                ->nullable();

            $table->decimal('salaire', 10, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Créateur
            |--------------------------------------------------------------------------
            */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};