<?php

return array(

    'deleted' => 'Modelo de Utente de utente apagado',
    'does_not_exist' => 'O Modelo de Utente não existe.',
    'no_association' => 'AVISO! O modelo de utente de utente para este item é inválido ou está em falta!',
    'no_association_fix' => 'Isto estragará as coisas de maneiras estranhas e horríveis. Edite este utente agora para lhe atribuir um modelo de utente.',
    'assoc_users'	 => 'Este modelo de utente está atualmente associado com pelo menos um utente e não pode ser removido. Por favor, remova os utentes e depois tente novamente. ',
    'invalid_category_type' => 'The category must be an utente category.',

    'create' => array(
        'error'   => 'O Modelo de Utente não foi criado. Por favor tente novamente.',
        'success' => 'Modelo de Utente criado com sucesso.',
        'duplicate_set' => 'Já existe um Modelo de Utente de utente com esse nome, escola e número de modelo de utente.',
    ),

    'update' => array(
        'error'   => 'O Modelo de Utente não foi atualizado. Por favor tente novamente',
        'success' => 'Modelo de Utente atualizado com sucesso.',
    ),

    'delete' => array(
        'confirm'   => 'Tem a certeza que pretende remover este modelo de utente de utente?',
        'error'   => 'Ocorreu um problema ao remover o modelo de utente. Por favor, tente novamente.',
        'success' => 'O modelo de utente foi removido com sucesso.'
    ),

    'restore' => array(
        'error'   		=> 'O Modelo de Utente não foi restaurado, por favor tente novamente',
        'success' 		=> 'Modelo de Utente restaurado com sucesso.'
    ),

    'bulkedit' => array(
        'error'   		=> 'Nenhum campo foi alterado, portanto, nada foi atualizado.',
        'success' 		=> 'Modelo de Utente foi atualizado com sucesso. |:model_count modelos de utente atualizados com sucesso.',
        'warn'          => 'Você está prestes a atualizar as propriedades do seguinte modelo de utente: Você está prestes a editar as propriedades dos seguintes :model_count models:',

    ),

    'bulkdelete' => array(
        'error'   		    => 'Nenhum modelo de utente selecionado, por isso nenhum modelo de utente foi eliminado.',
        'success' 		    => 'Modelo de Utente apagado!|:success_count modelos de utente apagados!',
        'success_partial' 	=> ':sucess_count modelo de utente(s) eliminados, no entanto :fail_count não foram eliminados, porque ainda têm utentes associados.'
    ),

);
