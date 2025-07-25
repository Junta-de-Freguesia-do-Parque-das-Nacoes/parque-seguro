<?php

return array(

    'does_not_exist' => 'A inscrição não existe ou não tem permissão para visualizá-la.',
    'user_does_not_exist' => 'O utilizador não existe ou você não tem permissão para visualizá-lo.',
    'asset_does_not_exist' 	=> 'O utente que está a tentar associar com esta inscrição não existe.',
    'owner_doesnt_match_asset' => 'O proprietário do utente que está a tentar associar com esta inscrição não é pessoa selecionada na dropdown.',
    'assoc_users'	 => 'Esta inscrição está correntemente alocada a um utilizador e não pode ser removida. Por favor devolva a inscrição e de seguida tente remover novamente. ',
    'select_asset_or_person' => 'Deve selecionar um recurso ou um utilizador, mas não ambos.',
    'not_found' => 'Inscrição não encontrada',
    'seats_available' => ':seat_count lugares disponíveis',


    'create' => array(
        'error'   => 'Inscrição não foi criada, por favor tente novamente.',
        'success' => 'Inscrição criada com sucesso.'
    ),

    'deletefile' => array(
        'error'   => 'Ficheiro não removido. Por favor, tente novamente.',
        'success' => 'Ficheiro removido com sucesso.',
    ),

    'upload' => array(
        'error'   => 'Ficheiro(s) não submetidos. Por favor, tente novamente.',
        'success' => 'Ficheiro(s) submetidos com sucesso.',
        'nofiles' => 'Não selecionou nenhum ficheiro para submissão, ou o ficheiro que pretende submeter é demasiado grande',
        'invalidfiles' => 'Um ou mais ficheiros excedem o tamanho ou são do tipo de ficheiro não é permitido. Os tipos permitidos são png, gif, jpg, doc, docx, pdf, txt, zip, rar, and rtf.',
    ),

    'update' => array(
        'error'   => 'Inscrição não foi atualizada, por favor tente novamente',
        'success' => 'Inscrição atualizada com sucesso.'
    ),

    'delete' => array(
        'confirm'   => 'Tem a certeza que pretende remover esta inscrição?',
        'error'   => 'Ocorreu um problema ao remover esta inscrição. Por favor, tente novamente.',
        'success' => 'A inscrição foi removida com sucesso.'
    ),

    'checkout' => array(
        'error'   => 'Ocorreu um problema ao atribuir esta inscrição. Por favor, tente novamente.',
        'success' => 'A inscrição foi alocada com sucesso',
        'not_enough_seats' => 'Não há vagas de inscrição suficientes disponíveis para o pagamento',
        'mismatch' => 'A vaga de licença fornecida não corresponde à inscrição.',
        'unavailable' => 'Esta vaga não está disponível para alocar',
    ),

    'checkin' => array(
        'error'   => 'Ocorreu um problema ao devolver esta inscrição. Por favor, tente novamente.',
        'success' => 'A inscrição foi devolvida com sucesso'
    ),

);
