<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('montant', 12, 2);
            $table->enum('mode_paiement', ['cheque', 'virement', 'wave', 'orange_money', 'espece', 'agent_mandate'])
                ->default('espece');
            $table->string('reference')->nullable(); // n° chèque, référence transaction...
            $table->foreignId('recu_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};