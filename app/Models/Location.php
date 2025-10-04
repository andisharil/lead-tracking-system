<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
        'description',
        'contact_person',
        'contact_email',
        'contact_phone',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
