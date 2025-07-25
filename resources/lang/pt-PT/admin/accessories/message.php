<?php

return array(

    'does_not_exist' => 'O recurso educativo e brinquedo [:id] não existe.',
    'not_found' => 'Esse recurso educativo e brinquedo não foi encontrado.',
    'assoc_users'	 => 'Esta recurso educativo e brinquedo tem atualmente :count items alocados a utilizadores. Por favor, devolva-os e tente novamente. ',

    'create' => array(
        'error'   => 'Recurso educativo e brinquedo não foi criado, por favor tente novamente.',
        'success' => 'Recurso educativo e brinquedo criado com sucesso.'
    ),

    'update' => array(
        'error'   => 'Recurso educativo e brinquedo não foi actualizado, por favor tente novamente',
        'success' => 'Recurso educativo e brinquedo actualizado com sucesso.'
    ),

    'delete' => array(
        'confirm'   => 'Tem a certeza que pretende remover este recurso educativo e brinquedo?',
        'error'   => 'Ocorreu um problema ao remover o recurso educativo e brinquedo. Por favor, tente novamente.',
        'success' => 'O recurso educativo e brinquedo foi removido com sucesso.'
    ),

     'checkout' => array(
        'error'   		=> 'O recurso educativo e brinquedo não foi alocado. Por favor, tente novamente',
        'success' 		=> 'Recurso educativo e brinquedo alocado com sucesso.',
        'unavailable'   => 'O recurso educativo e brinquedo não está disponível para check-out. Verifique a quantidade disponível',
        'user_does_not_exist' => 'O utilizador é inválido. Por favor, tente novamente.',
         'checkout_qty' => array(
            'lte'  => 'There is currently only one available accessory of this type, and you are trying to check out :checkout_qty. Please adjust the checkout quantity or the total stock of this accessory and try again.|There are :number_currently_remaining total available accessories, and you are trying to check out :checkout_qty. Please adjust the checkout quantity or the total stock of this accessory and try again.',
            ),
           
    ),

    'checkin' => array(
        'error'   		=> 'O recurso educativo e brinquedo não foi devolvido. Por favor, tente novamente',
        'success' 		=> 'Recurso educativo e brinquedo devolvido com sucesso.',
        'user_does_not_exist' => 'O utilizador é inválido. Por favor, tente novamente.'
    )


);
