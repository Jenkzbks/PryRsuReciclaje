<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    
    protected $table = 'brandmodels';

    /**
     * Relación con la marca
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}