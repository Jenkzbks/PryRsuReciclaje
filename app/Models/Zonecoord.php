<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zonecoord extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'zone_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}