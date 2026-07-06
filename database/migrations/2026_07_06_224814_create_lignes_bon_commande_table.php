<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_bon_commande', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bons_commande')->cascadeOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->string('designation_libre')->nullable(); // si la désignation n'existe pas dans le catalogue
            $table->string('taille')->nullable();            // "12", "M", "XL"...
            $table->string('couleur')->nullable();
            $table->string('matiere')->nullable();
            $table->boolean('logo')->default(false);
            $table->unsignedInteger('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant_ligne', 12, 2); // quantite * prix_unitaire
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_bon_commande');
    }
};