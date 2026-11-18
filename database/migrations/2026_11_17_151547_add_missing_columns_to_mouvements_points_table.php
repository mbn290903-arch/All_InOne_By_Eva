<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements_points', function (Blueprint $table) {
            $table->foreignId('regle_fidelite_id')
                ->nullable()
                ->constrained('regles_fidelite')
                ->nullOnDelete();

            $table->unsignedInteger('montant_reference')->nullable();

            $table->unsignedInteger('montant_par_tranche_snapshot')->nullable();

            $table->unsignedInteger('points_par_tranche_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mouvements_points', function (Blueprint $table) {
            $table->dropConstrainedForeignId('regle_fidelite_id');

            $table->dropColumn([
                'montant_reference',
                'montant_par_tranche_snapshot',
                'points_par_tranche_snapshot',
            ]);
        });
    }
};