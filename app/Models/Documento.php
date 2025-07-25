<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'responsavel_id',
        'path',
    ];

    // Definindo o relacionamento com o Responsável (caso necessário)
    public function responsavel()
    {
        return $this->belongsTo(Responsavel::class);
    }


    public function documentos()
{
    return $this->hasMany(Documento::class);
}


}
