<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_retries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('webhook_log_id');
            $table->string('status', 20); // success, failed
            $table->integer('status_code')->default(0);
            $table->text('response')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();

            $table->foreign('webhook_log_id')
                ->references('id')
                ->on('webhook_logs')
                ->onDelete('cascade');
            $table->index(['webhook_log_id', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_retries');
    }
};