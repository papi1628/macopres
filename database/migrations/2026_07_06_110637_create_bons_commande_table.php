<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_commande', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->string('numero');
            $table->date('date');
            $table->decimal('montant', 12, 2);
            $table->string('nature')->nullable();              // ex: "Uniformes Scolaires 2025/2026"
            $table->string('condition_paiement')->nullable();  // ex: "VOIR CONTRAT", "A LA LIVRAISON"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bons_commande');
    }
};