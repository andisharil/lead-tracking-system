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
        Schema::table('locations', function (Blueprint $table) {
            // Add address and metadata fields expected by controllers/views
            $table->string('city')->nullable()->after('name');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('postal_code');
            $table->text('description')->nullable()->after('status');
            $table->string('contact_person')->nullable()->after('description');
            $table->string('contact_email')->nullable()->after('contact_person');
            $table->string('contact_phone')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'state',
                'country',
                'postal_code',
                'status',
                'description',
                'contact_person',
                'contact_email',
                'contact_phone',
            ]);
        });
    }
};
