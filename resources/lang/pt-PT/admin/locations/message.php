<?php

return array(

    'does_not_exist' => 'Localização não existe.',
    'assoc_users'    => 'This location is not currently deletable because it is the location of record for at least one utente or user, has utentes assigned to it, or is the parent location of another location. Please update your models to no longer reference this company and try again. ',
    'assoc_assets'	 => 'Esta localização está atualmente associada com pelo menos um utente e não pode ser removida. Atualize este utentes de modo a não referenciarem mais este local e tente novamente. ',
    'assoc_child_loc'	 => 'Esta localização contém pelo menos uma sub-localização e não pode ser removida. Por favor, atualize as localizações para não referenciarem mais esta localização e tente novamente. ',
    'assigned_assets' => 'Utentes atribuídos',
    'current_location' => 'Localização atual',


    'create' => array(
        'error'   => 'Não foi possível criar a localização. Por favor, tente novamente.',
        'success' => 'Localização criada com sucesso.'
    ),

    'update' => array(
        'error'   => 'A localização não foi atualizada. Por favor, tente novamente',
        'success' => 'Localização atualizada com sucesso.'
    ),

    'delete' => array(
        'confirm'   	=> 'Tem a certeza que pretende remover esta localização?',
        'error'   => 'Ocorreu um problema ao remover esta localização. Por favor, tente novamente.',
        'success' => 'A localização foi removida com sucesso.'
    )

);
