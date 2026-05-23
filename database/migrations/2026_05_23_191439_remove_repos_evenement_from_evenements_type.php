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
        // Supprimer les événements non-fériés
        \App\Models\Evenement::whereIn('type', ['repos', 'evenement'])->delete();

        // SQLite ne supporte pas ALTER COLUMN sur enum
        // On recrée la table avec seulement 'ferie'
        Schema::table('evenements', function (Blueprint $table) {
            $table->string(' gitype')->default('ferie')->change();
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->string('type')->change();
        });
    }
};
