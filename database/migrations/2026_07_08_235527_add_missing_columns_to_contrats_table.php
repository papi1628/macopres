<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            if (!Schema::hasColumn('contrats', 'description_engagement')) {
                $table->text('description_engagement')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'montant_total')) {
                $table->decimal('montant_total', 12, 2)->nullable()->after('description_engagement');
            }
            if (!Schema::hasColumn('contrats', 'date_limite_livraison')) {
                $table->date('date_limite_livraison')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'delai_livraison_texte')) {
                $table->string('delai_livraison_texte')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'representant_macopres')) {
                $table->string('representant_macopres')->default('Masse BA');
            }
            if (!Schema::hasColumn('contrats', 'representant_client')) {
                $table->string('representant_client')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'representant_client_role')) {
                $table->string('representant_client_role')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'date_signature')) {
                $table->date('date_signature')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'bon_commande_id')) {
                $table->foreignId('bon_commande_id')->nullable()->constrained('bons_commande')->nullOnDelete();
            }
            if (!Schema::hasColumn('contrats', 'statut')) {
                $table->enum('statut', ['brouillon', 'signe'])->default('brouillon');
            }
            
        });
    }

    public function down(): void
    {
        // Volontairement vide : migration défensive, on ne retire rien au rollback
        // pour ne pas supprimer des colonnes qui existaient déjà avant elle.
    }
};