<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id', 'type', 'etat', 'envisagee', 'effectuee',
        'suivi', 'apte', 'observations'
    ];

    protected $casts = [
        'envisagee' => 'date',
        'effectuee' => 'date',
        'apte' => 'boolean',
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}