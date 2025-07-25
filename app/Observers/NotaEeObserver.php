<?php

namespace App\Observers;

use App\Models\NotaEe;
use App\Models\NotificacaoBackoffice;
use App\Models\User;

class NotaEeObserver
{
    public function created(NotaEe $nota)
    {
        $notificacao = NotificacaoBackoffice::create([
            'tipo' => 'nota',
            'asset_id' => $nota->asset_id,
            'mensagem' => 'Nova nota do Encarregado de Educação: "' . mb_strimwidth($nota->conteudo, 0, 80, '...') . '"',
        ]);

        // Associar a todos os utilizadores com grupo 'monitores'
        $users = User::whereHas('groups', function ($q) {
            $q->where('name', 'monitores');
        })->get();

        foreach ($users as $user) {
            $notificacao->utilizadores()->attach($user->id, ['lida' => false]);
        }
    }
}