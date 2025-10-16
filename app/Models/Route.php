<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'zone_id',
        'start_point',
        'end_point',
        'distance',
        'path_coordinates',
    ];

    public function routecoords()
    {
        return $this->hasMany(RouteCoord::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

   
}
