<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
    ];

    public function schedulingChanges()
    {
        return $this->hasMany(SchedulingChange::class);
    }
}
