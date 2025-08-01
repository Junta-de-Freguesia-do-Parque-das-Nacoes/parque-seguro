<?php

return array(

    'accepted'                  => 'Aceitou este utente com sucesso.',
    'declined'                  => 'Recusou este utente com sucesso.',
    'bulk_manager_warn'	        => 'Seus utilizadores foram atualizados com sucesso, no entanto, sua entrada de gerente não foi salva porque o gerente que você selecionou também estava na lista de utilizadores para ser editada e os utilizadores podem não ser seu próprio gerente. Selecione seus utilizadores novamente, excluindo o gerente.',
    'user_exists'               => 'Utilizador já existe!',
    'user_not_found'            => 'O utilizador não existe.',
    'user_login_required'       => 'O atributo login é obrigatório',
    'user_has_no_assets_assigned' => 'Não há conteúdos atualmente atribuídos ao utilizador.',
    'user_password_required'    => 'A password é obrigatória.',
    'insufficient_permissions'  => 'Permissões insuficientes.',
    'user_deleted_warning'      => 'Este utilizador foi removido. Terá que restaurar este utilizador para os editar ou Entregar-lhe novos utentes.',
    'ldap_not_configured'        => 'A integração com LDAP não foi configurada para este instalação.',
    'password_resets_sent'      => 'Os utilizadores selecionados que são ativados e têm um endereço de e-mail válido receberam um link de redefinição de senha.',
    'password_reset_sent'       => 'Um link para redefinição de senha foi enviado para :email!',
    'user_has_no_email'         => 'Esse utilizador não tem um endereço de endereço eletrónico no seu perfil.',
    'log_record_not_found'        => 'Não foi possível encontrar um registro de registro correspondente para este utilizador.',


    'success' => array(
        'create'    => 'Utilizador criado com sucesso.',
        'update'    => 'Utilizador atualizado com sucesso.',
        'update_bulk'    => 'Os utilizadores foram atualizados com sucesso!',
        'delete'    => 'Utilizador removido com sucesso.',
        'ban'       => 'Utilizador desabilitado com sucesso.',
        'unban'     => 'Utilizador reabilitado com sucesso.',
        'suspend'   => 'Utilizador foi suspenso com sucesso.',
        'unsuspend' => 'A suspensão do Utilizador foi removida com sucesso.',
        'restored'  => 'Utilizador restaurado com sucesso.',
        'import'    => 'Utilizadores importados com sucesso.',
    ),

    'error' => array(
        'create' => 'Ocorreu um problema ao criar o utilizador. Por favor, tente novamente.',
        'update' => 'Ocorreu um problema ao atualizar o utilizador. Por favor, tente novamente.',
        'delete' => 'Ocorreu um problema ao remover o utilizador. Por favor, tente novamente.',
        'delete_has_assets' => 'Este utilizador tem itens atribuídos e não pôde ser excluído.',
        'delete_has_assets_var' => 'This user still has an utente assigned. Please check it in first.|This user still has :count utentes assigned. Please check their utentes in first.',
        'delete_has_licenses_var' => 'This user still has a license seats assigned. Please check it in first.|This user still has :count license seats assigned. Please check them in first.',
        'delete_has_accessories_var' => 'This user still has an accessory assigned. Please check it in first.|This user still has :count accessories assigned. Please check their utentes in first.',
        'delete_has_locations_var' => 'This user still manages a location. Please select another manager first.|This user still manages :count locations. Please select another manager first.',
        'delete_has_users_var' => 'This user still manages another user. Please select another manager for that user first.|This user still manages :count users. Please select another manager for them first.',
        'unsuspend' => 'Ocorreu um problema ao retirar a suspensão ao utilizador. Por favor, tente novamente.',
        'import'    => 'Ocorreu um problema ao importar os utilizadores. Por favor, tente novamente.',
        'asset_already_accepted' => 'Este utente já foi aceite.',
        'accept_or_decline' => 'Tem que aceitar ou recusar este utente.',
        'cannot_delete_yourself' => 'We would feel really bad if you deleted yourself, please reconsider.',
        'incorrect_user_accepted' => 'O utente que tentou aceitar não foi solicitado por si.',
        'ldap_could_not_connect' => 'Não foi possível estabelecer uma ligação ao servidor LDAP. Por favor, verifique a configuração de servidor no ficheiro de configuração. <br>Error do Servidor LDAP:',
        'ldap_could_not_bind' => 'Não foi possível estabelecer uma vinculação com o servidor LDAP. Por favor, verifique a configuração de servidor no ficheiro de configuração. <br>Error do Servidor LDAP: ',
        'ldap_could_not_search' => 'Não foi possível pesquisar no servidor LDAP. Por favor, verifique a configuração de servidor no ficheiro de configuração. <br>Error do Servidor LDAP:',
        'ldap_could_not_get_entries' => 'Não foi possível obter registos do servidor LDAP. Por favor, verifique a configuração de servidor no ficheiro de configuração. <br>Error do Servidor LDAP:',
        'password_ldap' => 'A senha desta conta é gerenciada pelo LDAP / Active Directory. Entre em contato com seu departamento de TI para alterar sua senha.',
    ),

    'deletefile' => array(
        'error'   => 'Ficheiro não removido. Por favor, tente novamente.',
        'success' => 'Ficheiro removido com sucesso.',
    ),

    'upload' => array(
        'error'   => 'Ficheiro(s) não submetidos. Por favor, tente novamente.',
        'success' => 'Ficheiro(s) submetidos com sucesso.',
        'nofiles' => 'Não selecionou nenhum ficheiro para submissão',
        'invalidfiles' => 'Um ou mais ficheiros são demasiado grandes ou trata-se de um tipo de ficheiro não permitido. Os tipos de ficheiro permitidos são png, gif, jpg, jpeg, doc, docx, pdf e txt.',
    ),

    'inventorynotification' => array(
        'error'   => 'Este utilizador não tem endereço eletrónico definido.',
        'success' => 'O utilizador foi notificado sobre o seu inventário atual.'
    )
);