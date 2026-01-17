<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocModel extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if the name matches convention)
    protected $table = 'doc_model';

    // Fillable attributes
    protected $fillable = [
        'id_docteur',
        'id_cabinet',
        'model_nom',
        'logo_file_path',
        'descr_head',
        'descr_body',
        'descr_footer',
        'document',
        'is_selected',
    ];

    // Relationships
    public function doctor()
    {
        return $this->belongsTo(User::class, 'id_docteur');
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class, 'id_cabinet');
    }
}