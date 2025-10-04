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
        Schema::table('ad_spend', function (Blueprint $table) {
            $table->date('spend_date')->nullable()->after('month');
            $table->string('platform')->nullable()->after('spend_date');
            $table->string('ad_type')->nullable()->after('platform');
            $table->integer('impressions')->default(0)->after('ad_type');
            $table->integer('clicks')->default(0)->after('impressions');
            $table->integer('conversions')->default(0)->after('clicks');
            $table->text('description')->nullable()->after('conversions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ad_spend', function (Blueprint $table) {
            $table->dropColumn([
                'spend_date',
                'platform',
                'ad_type',
                'impressions',
                'clicks',
                'conversions',
                'description'
            ]);
        });
    }
};
