<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdonDoc extends Model
{
    use HasFactory;

    protected $table = 'ordon_doc';

    protected $fillable = [
        'id_docteur',
        'logo_file_path',
        'file_path',
        'nom_cabinet',
        'addr_cabinet',
        'tel_cabinet',
        'desc_cabinet',
        'descOrdonn',
        'isSelected',
    ];

    protected $casts = [
        'isSelected' => 'boolean',
    ];

    /**
     * Get the doctor that owns the ordonnance document configuration
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'id_docteur');
    }
}