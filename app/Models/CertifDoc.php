<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertifDoc extends Model
{
    use HasFactory;

    protected $table = 'certif_doc';

    protected $fillable = [
        'id_docteur',
        'logo_file_path',
        'file_path',
        'nom_cabinet',
        'addr_cabinet',
        'tel_cabinet',
        'desc_cabinet',
        'desc_certif',
        'is_selected',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'id_docteur');
    }
}
