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
        Schema::create('ad_spend', function (Blueprint $table) {
            $table->id();
            $table->string('month'); // YYYY-MM format
            $table->foreignId('source_id')->constrained('sources');
            $table->decimal('amount_spent', 10, 2);
            $table->timestamps();
            
            $table->unique(['month', 'source_id']); // Prevent duplicate entries for same month/source
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_spend');
    }
};
