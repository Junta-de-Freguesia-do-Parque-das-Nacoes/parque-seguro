<?php

return [

    'undeployable' 		=> '<strong>Warning: </strong> This utente has been marked as currently undeployable. If this status has changed, please update the utente status.',
    'does_not_exist' 	=> 'Utente não existente.',
    'does_not_exist_var'=> 'Utente with tag :asset_tag not found.',
    'no_tag' 	        => 'No utente tag provided.',
    'does_not_exist_or_not_requestable' => 'Esse utente não existe ou não é solicitável.',
    'assoc_users'	 	=> 'Este utente está correntemente Entregue a um utilizador e não pode ser removido. Por favor devolva o utente e de seguida tente remover novamente. ',
    'warning_audit_date_mismatch' 	=> 'This utente\'s next audit date (:next_audit_date) is before the last audit date (:last_audit_date). Please update the next audit date.',

    'create' => [
        'error'   		=> 'Não foi possível criar o Utente. Por favor, tente novamente. :(',
        'success' 		=> 'Utente criado com sucesso. :)',
        'success_linked' => 'O utente com a tag :tag foi criado com sucesso. <strong><a href=":link" style="color: white;">clique aqui para ver</a></strong>.',
    ],

    'update' => [
        'error'   			=> 'Utente não foi atualizado. Por favor, tente novamente',
        'success' 			=> 'Utente atualizado com sucesso.',
        'encrypted_warning' => 'Os utentes atualizados com sucesso, mas campos personalizados criptografados não se devem às permissões',
        'nothing_updated'	=>  'Nenhum atributo foi selecionado, portanto nada foi atualizado.',
        'no_assets_selected'  =>  'Nenhum utente foi selecionado, por isso nada foi atualizado.',
        'assets_do_not_exist_or_are_invalid' => 'Os arquivos selecionados não podem ser atualizados.',
    ],

    'restore' => [
        'error'   		=> 'O Utente não foi restaurado, por favor tente novamente',
        'success' 		=> 'Utente restaurado com sucesso.',
        'bulk_success' 		=> 'Utente restaurado com sucesso.',
        'nothing_updated'   => 'Nenhum utente foi selecionado, assim nada restaurado.', 
    ],

    'audit' => [
        'error'   		=> 'Utente audit unsuccessful: :error ',
        'success' 		=> 'Revisão de Inscrição de utentes logada com sucesso.',
    ],


    'deletefile' => [
        'error'   => 'Ficheiro não removido. Por favor, tente novamente.',
        'success' => 'Ficheiro removido com sucesso.',
    ],

    'upload' => [
        'error'   => 'Ficheiro(s) não submetidos. Por favor, tente novamente.',
        'success' => 'Ficheiro(s) submetidos com sucesso.',
        'nofiles' => 'Não selecionou nenhum ficheiro para submissão, ou o ficheiro que pretende submeter é demasiado grande',
        'invalidfiles' => 'Um ou mais ficheiros são demasiado grandes ou trata-se de um tipo de ficheiro não permitido. Os tipos de ficheiro permitidos são png, gif, jpg, jpeg, doc, docx, pdf e txt.',
    ],

    'import' => [
        'import_button'         => 'Process Import',
        'error'                 => 'Alguns itens não foram importados corretamente.',
        'errorDetail'           => 'Os seguintes itens não foram importados devido a erros.',
        'success'               => 'O seu ficheiro foi importado',
        'file_delete_success'   => 'Ficheiro eliminado com sucesso',
        'file_delete_error'      => 'Não foi possível eliminar o ficheiro',
        'file_missing' => 'Ficheiro selecionado está a faltar',
        'header_row_has_malformed_characters' => 'Um ou mais atributos na linha do cabeçalho contém caracteres UTF-8 mal formados',
        'content_row_has_malformed_characters' => 'Um ou mais atributos na primeira linha de conteúdo contém caracteres UTF-8 mal formados',
    ],


    'delete' => [
        'confirm'   	=> 'Tem a certeza de que pretende eliminar este utente?',
        'error'   		=> 'Ocorreu um problema ao remover o utente. Por favor, tente novamente.',
        'nothing_updated'   => 'Nenhum recurso foi selecionado, então nada foi excluído.',
        'success' 		=> 'O utente foi removido com sucesso.',
    ],

    'checkout' => [
        'error'   		=> 'Não foi possível Entregar o utente, por favor tente novamente',
        'success' 		=> 'Utente alocado com sucesso.',
        'user_does_not_exist' => 'O utilizador é inválido. Por favor, tente novamente.',
        'not_available' => 'Esse recurso não está disponível para checkout!',
        'no_assets_selected' => 'Deve escolher pelo menos um utente da lista',
    ],

    'checkin' => [
        'error'   		=> 'Não foi possível devolver o utente, por favor tente novamente',
        'success' 		=> 'Utente devolvido com sucesso.',
        'user_does_not_exist' => 'O utilizador é inválido. Por favor, tente novamente.',
        'already_checked_in'  => 'Este utente já foi devolvido.',

    ],

    'requests' => [
        'error'   		=> 'Utente não foi solicitado, por favor tente novamente',
        'success' 		=> 'Utente solicitado com sucesso.',
        'canceled'      => 'Requisição cancelado com sucesso',
    ],

];
