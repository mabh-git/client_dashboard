<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id', 'date', 'envoi', 'ar', 'ordonnance', 'accepte',
        'excusable', 'reporte', 'honore', 'motif', 'commentaire'
    ];

    protected $casts = [
        'date' => 'datetime',
        'envoi' => 'date',
        'ar' => 'boolean',
        'ordonnance' => 'boolean',
        'accepte' => 'boolean',
        'excusable' => 'boolean',
        'reporte' => 'boolean',
        'honore' => 'boolean',
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}