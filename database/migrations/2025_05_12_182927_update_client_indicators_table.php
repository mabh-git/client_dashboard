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
        Schema::table('client_indicators', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->integer('mouvements')->default(0);
            $table->integer('jours_retards')->default(0);
            $table->integer('etablissements_inconnus')->default(0);
            $table->integer('imports_en_attente')->default(0);
            $table->integer('factures_en_attente')->default(0);
            $table->integer('factures_rapprochement')->default(0);
            $table->integer('rejet_import')->default(0);
            $table->integer('programmees')->default(0);
            $table->integer('suspendues')->default(0);
            $table->integer('sensibles')->default(0);
            $table->integer('sans_as')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_indicators', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'client_id', 'mouvements', 'jours_retards', 'etablissements_inconnus',
                'imports_en_attente', 'factures_en_attente', 'factures_rapprochement',
                'rejet_import', 'programmees', 'suspendues', 'sensibles', 'sans_as'
            ]);
        });
    }
};