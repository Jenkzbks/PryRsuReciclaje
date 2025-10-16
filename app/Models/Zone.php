<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area',
        'description',
        'district_id',
        'province_id',
        'polygon_coordinates',
    ];

    protected $casts = [
        'polygon_coordinates' => 'array',
        'area' => 'decimal:2',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Relaciones dinámicas que manejan cuando hay o no distrito
    public function getDepartmentAttribute()
    {
        if ($this->district) {
            return $this->district->department;
        } elseif ($this->province) {
            return $this->province->department;
        }
        return null;
    }

    public function getProvinceRelationAttribute()
    {
        return $this->district ? $this->district->province : $this->province;
    }

    // Generar código automático
    public function getCodeAttribute()
    {
        return 'ZN' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // Verificar si tiene polígono asignado
    public function hasPolygon()
    {
        return !empty($this->polygon_coordinates);
    }

    // Obtener la ubicación completa
    public function getFullLocationAttribute()
    {
        if ($this->district) {
            // Zona con distrito específico
            return $this->district->department->name . '/' . $this->district->province->name . '/' . $this->district->name;
        } elseif ($this->province) {
            // Zona a nivel de provincia (sin distrito)
            return $this->province->department->name . '/' . $this->province->name . '/(Toda la provincia)';
        }
        return 'No asignado';
    }
}