<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;

    protected $table = 'employeetype';

    protected $fillable = [
        'name',
        'code',
        'description',
        'level',
        'sort_order',
        'active',
        'protected',
        'color',
        'icon'
    ];

    protected $casts = [
        'active' => 'boolean',
        'protected' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relaciones
    public function employees()
    {
        return $this->hasMany(Employee::class, 'type_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'position_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeProtected($query)
    {
        return $query->where('protected', true);
    }

    public function scopeNotProtected($query)
    {
        return $query->where('protected', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Verificar si se puede eliminar
    public function canBeDeleted()
    {
        return !$this->protected && $this->employees()->count() === 0;
    }
}