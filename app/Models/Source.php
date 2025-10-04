<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'status',
        'cost_per_lead',
        'monthly_budget',
        'contact_person',
        'contact_email',
        'contact_phone',
        'configuration',
        'last_active_at'
    ];

    protected $casts = [
        'configuration' => 'array',
        'last_active_at' => 'datetime',
        'cost_per_lead' => 'decimal:2',
        'monthly_budget' => 'decimal:2'
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function adSpends()
    {
        return $this->hasMany(AdSpend::class);
    }
}
