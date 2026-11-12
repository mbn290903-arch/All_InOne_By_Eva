
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regles_fidelite', function (Blueprint $table) {
            $table->id();

            $table->string('libelle', 120);

            // Montant dépensé nécessaire pour obtenir les points.
            $table->unsignedInteger('montant_par_tranche');

            // Nombre de points attribués pour chaque tranche.
            $table->unsignedInteger('points_par_tranche')->default(1);

            // Période d'application de la règle.
            $table->date('date_debut');
            $table->date('date_fin')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('enregistre_par')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['is_active', 'date_debut', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regles_fidelite');
    }
};
