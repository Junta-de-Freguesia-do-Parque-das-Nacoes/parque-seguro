<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NotificacaoBackoffice extends Model
{
    protected $table = 'notificacoes_backoffice';

    protected $fillable = [
        'tipo',
        'asset_id',
        'mensagem',
        'lida',
    ];

    public function utilizadores()
{
    return $this->belongsToMany(User::class, 'notificacao_user', 'notificacao_backoffice_id', 'user_id')
                ->withPivot('lida')
                ->withTimestamps();
}


    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
