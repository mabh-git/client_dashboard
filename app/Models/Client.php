<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'logo_path', 'address', 'city', 'postal_code',
        'phone', 'email', 'website', 'contact_person', 'contact_email',
        'contact_phone', 'is_active', 'is_favorite', 'contact', 'indicators'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_favorite' => 'boolean',
        'contact' => 'array',
        'indicators' => 'array',
    ];

    // Relations
    public function indicators()
    {
        return $this->hasOne(ClientIndicator::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}