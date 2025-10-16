<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleImage extends Model
{
    use HasFactory;

    protected $table = 'vehiclesimage';

    protected $fillable = ['vehicle_id', 'image', 'profile'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
