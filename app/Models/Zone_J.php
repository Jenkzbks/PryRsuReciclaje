<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone_J extends Model
{
    use HasFactory;

    protected $table = 'zones';

    protected $fillable = [
        'name',
        'average_waste',
        'description',
        'status',
        'district_id',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // Código automático
    public function getCodeAttribute()
    {
        return 'ZN' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // Ubicación completa
    public function getFullLocationAttribute()
    {
        if ($this->district) {
            return $this->district->department->name . '/' . $this->district->province->name . '/' . $this->district->name;
        }
        return 'No asignado';
    }

    public function coords()
    {
        return $this->hasMany(Coord::class, 'zone_id');
    }

    public function getHasPolygonAttribute()
    {
        return $this->coords()->count() > 0;
    }
}
