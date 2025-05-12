<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'mouvements', 'jours_retards', 'etablissements_inconnus',
        'imports_en_attente', 'factures_en_attente', 'factures_rapprochement',
        'rejet_import', 'programmees', 'suspendues', 'sensibles', 'sans_as'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}