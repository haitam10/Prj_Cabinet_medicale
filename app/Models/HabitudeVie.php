<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitudeVie extends Model
{
    use HasFactory;

    protected $table = 'habitudes_vie';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'type',
        'description',
        'frequence',
        'quantite',
        'date_debut',
        'date_fin',
        'commentaire'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}