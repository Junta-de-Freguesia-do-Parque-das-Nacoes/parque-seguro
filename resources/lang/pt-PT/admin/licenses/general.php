<?php

return array(
    'about_licenses_title'      => 'Sobre as Inscrições',
    'about_licenses'            => 'As inscrições são usadas para controlar o software.  Eles têm um número especificado de lugares disponíveis para atribuir',
    'checkin'  					=> 'Devolver a vaga da Inscrição',
    'checkout_history'  		=> 'Entregar histórico',
    'checkout'  				=> 'Entregar instalação da Inscrição',
    'edit'  					=> 'Editar Inscrição',
    'filetype_info'				=> 'Os tipos de ficheiro permitidos são png, gif, jpg, jpeg, doc, docx, pdf, txt, zip e rar.',
    'clone'  					=> 'Clonar inscrição',
    'history_for'  				=> 'Histórico para ',
    'in_out'  					=> 'Entrada/Saída',
    'info'  					=> 'Informação da Inscrição',
    'license_seats'  			=> 'Vagas da Inscrição',
    'seat'  					=> 'Vaga',
    'seat_count'  				=> 'Vaga :count',
    'seats'  					=> 'Vagas',
    'software_licenses'  		=> 'Inscrições ',
    'user'  					=> 'Utilizador',
    'view'  					=> 'Ver Inscrição',
    'delete_disabled'           => 'Esta inscrição ainda não pode ser excluída porque algumas vagas ainda estão reservadas.',
    'atribuir'                     => 'Atribua esta vaga',
    'devolver'                  => 'Liberte esta vaga',
   'bulk' => [
    'checkin_all' => [
        'button' => 'Receber todas as vagas',
        'modal' => 'Esta ação devolverá uma vaga. | Esta ação devolverá todas as :checkedout_seats_count vagas desta inscrição.',
        'enabled_tooltip' => 'Devolução de TODAS as vagas para esta inscrição de utilizadores e utentes.',
        'disabled_tooltip' => 'Esta ação está desativada porque não há vagas atribuídas no momento.',
        'disabled_tooltip_reassignable' => 'Esta ação está desativada porque a vaga de inscrição não pode ser reatribuída.',
        'success' => '{1} Inscrição alocada com sucesso!|[2,*] Todas as :count inscrições foram alocadas com sucesso!',
        'log_msg' => 'Vaga alocada através da funcionalidade de alocação de vagas em massa na interface gráfica.',
    ],

    'checkout_all' => [
        'button' => 'Entregar todas as vagas',
        'modal' => 'Esta ação atribuirá um lugar ao primeiro utilizador disponível. Se houver :available_seats_count vagas, elas serão atribuídas aos primeiros utilizadores elegíveis. Um utilizador é considerado disponível se ele ainda não tiver essa inscrição e se a opção "Atribuir Automaticamente" estiver ativada em sua conta.',
        'enabled_tooltip' => 'Entrega de TODAS as vagas disponíveis para TODOS os utilizadores elegíveis.',
        'disabled_tooltip' => 'Esta ação está desativada porque não há vagas atribuídas no momento.',
        'success' => '{1} Inscrição alocada com sucesso!|[2,*] :count inscrições foram alocadas com sucesso!',
        'error_no_seats' => 'Não há mais vagas disponíveis para esta inscrição.',
        'warn_not_enough_seats' => ':count utentes foram alocados para esta inscrição, mas não há mais vagas disponíveis.',
        'warn_no_avail_users' => 'Nada a fazer. Todos os lugares já estão atribuídos.',
        'log_msg' => 'Alocação realizada através da funcionalidade de alocação em massa.',
    ],
],


    'below_threshold' => 'Existem apenas :remaining_count vagas para esta inscrição com uma quantidade mínima de :min_amt. Considere aumentar o número de vagas disponíveis.',
    'below_threshold_short' => 'Este item está abaixo da quantidade mínima necessária.',
);
