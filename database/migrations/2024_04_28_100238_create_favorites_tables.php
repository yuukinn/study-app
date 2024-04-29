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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();										
            $table->foreignUuId('user_id')->constrained()->onDelete('cascade');										
            $table->foreignUuId('recipe_id')->constrained()->onDelete('cascade');										
            $table->boolean('favorite')->default(false);										
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));										
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));										
            // unique 制約を追加										
            $table->unique(['user_id', 'recipe_id']);										
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
