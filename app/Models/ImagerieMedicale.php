<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagerieMedicale extends Model
{
    use HasFactory;

    protected $table = 'imageries_medicales';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'type',
        'zone_examinee',
        'resultat',
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