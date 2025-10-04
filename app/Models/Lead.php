<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'position',
        'location_id',
        'source_id',
        'campaign_id',
        'status',
        'value',
        'notes',
        'closed_at'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
