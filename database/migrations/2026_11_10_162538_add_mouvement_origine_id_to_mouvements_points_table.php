<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements_points', function (Blueprint $table) {
            $table->foreignId('mouvement_origine_id')
                ->nullable()
                ->unique()
                ->constrained('mouvements_points')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mouvements_points', function (Blueprint $table) {
            $table->dropForeign(['mouvement_origine_id']);
            $table->dropUnique(['mouvement_origine_id']);
            $table->dropColumn('mouvement_origine_id');
        });
    }
};
