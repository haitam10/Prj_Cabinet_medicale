<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cin',
        'nom',
        'email',
        'password',
        'role',
        'statut',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations

    public function rendezvousMedecin()
    {
        return $this->hasMany(Rendezvous::class, 'medecin_id');
    }

    public function rendezvousSecretaire()
    {
        return $this->hasMany(Rendezvous::class, 'secretaire_id');
    }

    public function certificats()
    {
        return $this->hasMany(Certificat::class, 'medecin_id');
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'medecin_id');
    }

    public function remarques()
    {
        return $this->hasMany(Remarque::class, 'medecin_id');
    }

    public function facturesGenerees()
    {
        return $this->hasMany(Facture::class, 'utilisateur_id');
    }

    public function facturesMedecin()
    {
        return $this->hasMany(Facture::class, 'medecin_id');
    }

    public function facturesSecretaire()
    {
        return $this->hasMany(Facture::class, 'secretaire_id');
    }
}
