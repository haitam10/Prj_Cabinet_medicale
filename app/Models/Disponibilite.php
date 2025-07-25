<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disponibilite extends Model
{
    use HasFactory;

    protected $fillable = [
        'medecin_id',
        'secretaire_id',
        'date',
        'heure_entree',
        'heure_sortie',
    ];

    // Relations
    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function secretaire()
    {
        return $this->belongsTo(User::class, 'secretaire_id');
    }
}
