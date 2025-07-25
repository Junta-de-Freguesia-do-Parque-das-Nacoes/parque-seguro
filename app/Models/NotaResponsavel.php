<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaResponsavel extends Model
{
    use HasFactory;

    protected $table = 'responsaveis_notas';

    protected $fillable = [
        'responsavel_id',
        'nota',
        'adicionado_por'
    ];

    public function responsavel()
    {
        return $this->belongsTo(Responsavel::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'adicionado_por');
    }
}

