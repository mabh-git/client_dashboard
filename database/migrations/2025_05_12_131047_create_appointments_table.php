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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->dateTime('date');
            $table->date('envoi')->nullable();
            $table->boolean('ar')->default(false);
            $table->boolean('ordonnance')->default(false);
            $table->boolean('accepte')->default(false);
            $table->boolean('excusable')->default(false);
            $table->boolean('reporte')->default(false);
            $table->boolean('honore')->default(false);
            $table->string('motif')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};