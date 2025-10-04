<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'type',
        'source_id',
        'budget',
        'spent',
        'start_date',
        'end_date',
        'targeting',
        'settings',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'impressions',
        'clicks',
        'ctr',
        'cpc',
        'cpm',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'targeting' => 'array',
        'settings' => 'array',
        'ctr' => 'decimal:2',
        'cpc' => 'decimal:2',
        'cpm' => 'decimal:2',
    ];

    /**
     * Get the source that owns the campaign.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the leads for the campaign.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the ad spend records for the campaign.
     */
    public function adSpends(): HasMany
    {
        return $this->hasMany(AdSpend::class);
    }

    /**
     * Calculate the remaining budget.
     */
    public function getRemainingBudgetAttribute()
    {
        if (!$this->budget) {
            return null;
        }
        return $this->budget - $this->spent;
    }

    /**
     * Calculate the budget utilization percentage.
     */
    public function getBudgetUtilizationAttribute()
    {
        if (!$this->budget || $this->budget == 0) {
            return 0;
        }
        return ($this->spent / $this->budget) * 100;
    }

    /**
     * Calculate the conversion rate.
     */
    public function getConversionRateAttribute()
    {
        $totalLeads = $this->leads()->count();
        if ($this->clicks == 0) {
            return 0;
        }
        return ($totalLeads / $this->clicks) * 100;
    }

    /**
     * Calculate the cost per lead.
     */
    public function getCostPerLeadAttribute()
    {
        $totalLeads = $this->leads()->count();
        if ($totalLeads == 0) {
            return 0;
        }
        return $this->spent / $totalLeads;
    }

    /**
     * Calculate the ROI.
     */
    public function getRoiAttribute()
    {
        if ($this->spent == 0) {
            return 0;
        }
        
        $totalRevenue = $this->leads()->where('status', 'successful')->sum('value');
        return (($totalRevenue - $this->spent) / $this->spent) * 100;
    }

    /**
     * Check if the campaign is active.
     */
    public function getIsActiveAttribute()
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        $now = now()->toDateString();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }
        
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Scope to filter active campaigns.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now()->toDateString());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now()->toDateString());
                    });
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by source.
     */
    public function scopeBySource($query, $sourceId)
    {
        return $query->where('source_id', $sourceId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                       ->where('end_date', '>=', $endDate);
              });
        });
    }
}