<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPST extends Model
{
    use HasFactory;

    protected $table = 'spsts';

    protected $fillable = [
        'name', 'address', 'postal_code', 'city', 'phone', 'url', 'message'
    ];
}