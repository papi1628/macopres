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
        Schema::table('pointages', function (Blueprint $table) {
            $table->dropColumn('statut');
        });

        Schema::table('pointages', function (Blueprint $table) {
            $table->enum('statut', [
                'present',
                'retard',
                'absent',
                'ferie_paye'
            ])->default('present');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointages', function (Blueprint $table) {
            $table->dropColumn('statut');
        });

        Schema::table('pointages', function (Blueprint $table) {
            $table->enum('statut', [
                'present',
                'retard',
                'absent'
            ])->default('present');
        });
    }
};
