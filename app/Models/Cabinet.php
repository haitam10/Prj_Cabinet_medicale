<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocModel;  // corrigé ici

class Cabinet extends Model
{
    use HasFactory;

    protected $table = 'cabinets';

    protected $fillable = [
        'nom_cabinet',
        'id_docteur',
        'addr_cabinet',
        'tel_cabinet',
        'descr_cabinet',
    ];

    // Relation hasMany vers DocModel
    public function docs()
    {
        return $this->hasMany(DocModel::class, 'id_cabinet');
    }
}
