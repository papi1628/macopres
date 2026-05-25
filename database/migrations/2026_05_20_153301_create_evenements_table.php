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
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();

            $table->date('date');

            $table->enum('type', [
                'ferie',
                'repos',
                'evenement',
            ]);

            $table->string('titre');
            $table->text('description')->nullable();

            $table->boolean('est_paye')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
