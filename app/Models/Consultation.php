<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'rendezvous_id',
        'date_consultation',
        'heure',
        'motif',
        'symptomes',
        'diagnostic',
        'traitement',
        'follow_up_instructions',
        'consultation_fee',
        'status'
    ];

    protected $casts = [
        'date_consultation' => 'date',
        'heure' => 'datetime:H:i',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function rendezvous()
    {
        return $this->belongsTo(Rendezvous::class);
    }
}
