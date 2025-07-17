<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doc_model;

class Cabinet extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if the name matches convention)
    protected $table = 'cabinets';

    // Fillable attributes
    protected $fillable = [
        'nom_cabinet',
        'id_docteur',
        'addr_cabinet',
        'tel_cabinet',
        'descr_cabinet',
    ];

    // Relationships
    public function docs()
    {
        return $this->hasMany(DocModel::class, 'id_cabinet');
    }
}