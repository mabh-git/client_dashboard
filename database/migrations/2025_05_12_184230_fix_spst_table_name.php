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
        Schema::rename('spsts', 's_p_s_t_s');
    }
    
    public function down(): void
    {
        Schema::rename('s_p_s_t_s', 'spsts');
    }
};
