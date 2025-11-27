<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coord extends Model
{
    use HasFactory;

    protected $fillable = [
        'coord_index',
        'latitude',
        'longitude',
        'zone_id',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone_J::class, 'zone_id');
    }
}
