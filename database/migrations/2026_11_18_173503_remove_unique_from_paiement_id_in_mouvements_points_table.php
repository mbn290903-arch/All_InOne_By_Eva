<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements_points', function (Blueprint $table) {
            $table->dropForeign(['paiement_id']);
            $table->dropUnique(['paiement_id']);
            $table->index('paiement_id');

            $table->foreign('paiement_id')
                ->references('id')
                ->on('paiements')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mouvements_points', function (Blueprint $table) {
            $table->dropForeign(['paiement_id']);
            $table->dropIndex(['paiement_id']);
            $table->unique('paiement_id');

            $table->foreign('paiement_id')
                ->references('id')
                ->on('paiements')
                ->restrictOnDelete();
        });
    }
};
