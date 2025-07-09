<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'cin',
        'nom',
        'sexe',
        'date_naissance',
        'contact',
    ];

    public function rendezvous()
    {
        return $this->hasMany(Rendezvous::class);
    }

    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    public function remarques()
    {
        return $this->hasMany(Remarque::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
}
