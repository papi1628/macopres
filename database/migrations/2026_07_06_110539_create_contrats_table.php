<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->unique()->constrained('programmes')->cascadeOnDelete();
            $table->text('description_engagement')->nullable();  // quantités / produits commandés
            $table->decimal('montant_total', 12, 2)->nullable();
            $table->date('date_limite_livraison')->nullable();
            $table->string('delai_livraison_texte')->nullable(); // ex: "avant la rentrée scolaire"
            $table->string('representant_macopres')->default('Masse BA');
            $table->string('representant_client')->nullable();
            $table->date('date_signature')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};