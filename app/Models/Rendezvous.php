<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendezvous extends Model
{
    use HasFactory;

    protected $table = 'rendezvous';

    // clé primaire auto-increment par défaut, pas besoin de surcharger

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'secretaire_id',
        'appointment_date',
        'appointment_time',
        'duration',
        'status',
        'appointment_type',
        'reason',
        'patient_notes',
        'doctor_notes',
        'cancelled_at',
        'cancellation_reason',
        'feedback',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function secretaire()
    {
        return $this->belongsTo(User::class, 'secretaire_id');
    }
}
