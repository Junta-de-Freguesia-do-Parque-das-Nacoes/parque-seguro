<?php

return [

    'does_not_exist' => 'Etiqueta de estado não existe.',
    'deleted_label' => 'Rótulo de estado excluído',
    'assoc_assets'	 => 'Esta etiqueta de estado está associada a pelo menos um Utente e não pode ser apagada. Atualize os seus Utentes para que não sejam usados novamente como referência a estes estado e tente novamente. ',

    'create' => [
        'error'   => 'Etiqueta de estado não foi criada, tente novamente.',
        'success' => 'Etiqueta de estado criada com sucesso.',
    ],

    'update' => [
        'error'   => 'Etiqueta de estado não foi atulizada, tente novamente',
        'success' => 'Etiqueta de estado atualizada com sucesso.',
    ],

    'delete' => [
        'confirm'   => 'Tem a certeza que pretende eliminar esta etiqueta de estado?',
        'error'   => 'Ocorreu um erra ao eliminar a etiqueta de estado. Tente novamente.',
        'success' => 'A etiqueta de estado foi eliminada com sucesso.',
    ],

    'help' => [
        'undeployable'   => 'Esses utentes não podem ser atribuídos a ninguém.',
        'deployable'   => 'Esses utentes podem ser entregues. Uma vez que entregues, eles ficarão com um estado de <i class="fas fa-circle text-blue"></i> <strong>Implementado</strong>.',
        'archived'   => 'Esses utentes não podem ser verificados, e só aparecerão na visão arquivada. Isso é útil para manter informações sobre recursos para fins orçamentários / históricos, mas mantendo-os fora da lista de utentes do dia-a-dia.',
        'pending'   => 'Esses utentes ainda não podem ser atribuídos a qualquer pessoa, muitas vezes usado para itens que estão fora de reparo, mas é esperado que retornem à circulação.',
    ],

];
