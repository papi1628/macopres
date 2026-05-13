<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Désactive les clés étrangères pour éviter le blocage SQL
        Schema::disableForeignKeyConstraints();
        
        // Supprime proprement la table
        Schema::dropIfExists('pointages');
        
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Optionnel : recréer la structure de base si vous faites un rollback
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
