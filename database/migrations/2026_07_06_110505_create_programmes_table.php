<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->cascadeOnDelete();
            $table->string('annee_scolaire'); // ex: "2025/2026"
            $table->enum('statut', ['en_cours', 'termine', 'annule'])->default('en_cours');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['ecole_id', 'annee_scolaire']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};