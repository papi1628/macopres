<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles_production', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->string('designation');       // ex: "CHEMISE LM"
            $table->text('description')->nullable(); // specs : "Coton Suisse bleu immaculée, Col Filé..."
            $table->unsignedInteger('quantite')->nullable();
            $table->string('photo')->nullable(); // chemin disque "public"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles_production');
    }
};