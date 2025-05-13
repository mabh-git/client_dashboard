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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->integer('rating');
            $table->string('emotion')->nullable();
            $table->text('text');
            $table->json('categories')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->boolean('want_response')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};