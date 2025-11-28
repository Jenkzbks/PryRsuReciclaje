<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'shifts';

    protected $casts = [
        'hora_in' => 'string',
        'hora_out' => 'string',
    ];
}
