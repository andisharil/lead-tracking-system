<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable();
            $table->string('endpoint');
            $table->string('method', 10)->default('POST');
            $table->string('status', 20)->default('pending'); // pending, success, failed
            $table->integer('status_code')->nullable();
            $table->integer('processing_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->text('payload')->nullable();
            $table->text('headers')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};