<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenBiologique extends Model
{
    use HasFactory;

    protected $table = 'examens_biologiques';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'type',
        'resultat',
        'unite',
        'valeurs_reference',
        'date_examen',
        'commentaire'
    ];

    protected $casts = [
        'date_examen' => 'date'
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