<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulingChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduling_id',
        'reason_id',
        'change_type',
        'old_value',
        'new_value',
        'user_id',
        'notes',
    ];

    public function scheduling()
    {
        return $this->belongsTo(Scheduling::class);
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
