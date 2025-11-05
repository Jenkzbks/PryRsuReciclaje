<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupdetail extends Model
{
    protected $table = 'groupdetails';

    protected $fillable = [
        'scheduling_id',
        'emplooyee_id',   // OJO: coincide con tu migración (doble "o")
    ];

    public function scheduling()
    {
        return $this->belongsTo(Scheduling::class, 'scheduling_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emplooyee_id');
    }
}
