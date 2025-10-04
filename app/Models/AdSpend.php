<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSpend extends Model
{
    protected $table = 'ad_spend';

    protected $fillable = [
        'month',
        'spend_date',
        'platform',
        'ad_type',
        'source_id',
        'campaign_id',
        'amount_spent',
        'impressions',
        'clicks',
        'conversions',
        'description'
    ];

    protected $casts = [
        'amount_spent' => 'decimal:2',
        'spend_date' => 'date',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
