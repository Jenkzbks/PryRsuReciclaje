<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scheduling extends Model
{
    use HasFactory;

    protected $table = 'schedulings';
    protected $guarded = [];

    public function group()   { return $this->belongsTo(\App\Models\Employeegroup::class, 'group_id'); }
    public function shift()   { return $this->belongsTo(\App\Models\Shift::class,        'shift_id'); }
    public function vehicle() { return $this->belongsTo(\App\Models\Vehicle::class,      'vehicle_id'); }
    public function zone()    { return $this->belongsTo(\App\Models\Zone::class,         'zone_id'); }
    public function details()
{
    return $this->hasMany(\App\Models\Groupdetail::class, 'scheduling_id');
}

}
