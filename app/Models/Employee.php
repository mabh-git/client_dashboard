<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'name', 'matricule', 'gender', 'birthdate', 'contractType',
        'startDate', 'spst', 'role', 'poste', 'departement', 'surveillance', 'pcsCode',
        'is_active'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'startDate' => 'date',
        'is_active' => 'boolean',
    ];

    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}