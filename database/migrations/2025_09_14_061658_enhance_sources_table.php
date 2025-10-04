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
        Schema::table('sources', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->enum('status', ['active', 'inactive', 'paused'])->default('active')->after('description');
            $table->decimal('cost_per_lead', 8, 2)->nullable()->after('status');
            $table->decimal('monthly_budget', 10, 2)->nullable()->after('cost_per_lead');
            $table->string('contact_person')->nullable()->after('monthly_budget');
            $table->string('contact_email')->nullable()->after('contact_person');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->json('configuration')->nullable()->after('contact_phone'); // For storing source-specific settings
            $table->timestamp('last_active_at')->nullable()->after('configuration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'status',
                'cost_per_lead',
                'monthly_budget',
                'contact_person',
                'contact_email',
                'contact_phone',
                'configuration',
                'last_active_at'
            ]);
        });
    }
};