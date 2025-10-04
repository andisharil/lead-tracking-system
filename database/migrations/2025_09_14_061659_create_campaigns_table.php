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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'draft'])->default('draft');
            $table->enum('type', ['email', 'social', 'ppc', 'display', 'content', 'other'])->default('other');
            $table->foreignId('source_id')->constrained('sources')->onDelete('cascade');
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('spent', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('targeting')->nullable(); // Store targeting criteria
            $table->json('settings')->nullable(); // Store campaign-specific settings
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('ctr', 5, 2)->default(0); // Click-through rate
            $table->decimal('cpc', 8, 2)->default(0); // Cost per click
            $table->decimal('cpm', 8, 2)->default(0); // Cost per mille
            $table->timestamps();
            
            $table->index(['status', 'source_id']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};