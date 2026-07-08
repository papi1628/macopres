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
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programme_id')
                ->unique()
                ->constrained('programmes')
                ->cascadeOnDelete();

            $table->foreignId('bon_commande_id')
                ->constrained('bons_commande')
                ->cascadeOnDelete();

            $table->date('date_limite_livraison')->nullable();
            $table->string('delai_livraison_texte')->nullable();

            $table->string('representant_macopres')->default('Masse BA');
            $table->string('representant_client')->nullable();

            $table->date('date_signature')->nullable();

            $table->enum('statut', ['brouillon', 'genere', 'signe'])
                ->default('brouillon');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
