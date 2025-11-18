<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenances extends Model
{
    use HasFactory;

    protected $table = 'maintenances';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];
}
