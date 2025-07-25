<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaEe extends Model
{
    protected $table = 'notas_ee';

    protected $fillable = [
        'asset_id',
        'responsavel_id',
        'conteudo',
    ];

    public function utente()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(Responsavel::class, 'responsavel_id');
    }
}
