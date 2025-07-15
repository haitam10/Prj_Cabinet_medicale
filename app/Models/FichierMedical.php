<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichierMedical extends Model
{
    use HasFactory;

    protected $table = 'fichiers_medicaux';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'nom',
        'chemin',
        'type',
        'taille',
        'commentaire'
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