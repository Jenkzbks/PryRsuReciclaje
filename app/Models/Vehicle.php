<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'plate', 'year', 'load_capacity', 'description', 'status', 'brand_id', 'model_id', 'type_id', 'color_id', 'passengers', 'fuel_capacity'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function model()
    {
        return $this->belongsTo(BrandModel::class);
    }
    public function type()
    {
        return $this->belongsTo(VehicleType::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    public function images()
    {
        return $this->hasMany(VehicleImage::class);
    }
}
