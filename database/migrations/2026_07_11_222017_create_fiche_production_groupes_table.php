<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiche_production_groupes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bons_commande')->cascadeOnDelete();
            $table->string('groupe_cle'); // identifie le groupe d'articles (désignation+couleur+matière+logo) au sein du BC
            $table->text('description')->nullable();   // specs complémentaires précisées par l'assistant
            $table->string('photo')->nullable();        // chemin disque "public"
            $table->timestamps();

            $table->unique(['bon_commande_id', 'groupe_cle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiche_production_groupes');
    }
};