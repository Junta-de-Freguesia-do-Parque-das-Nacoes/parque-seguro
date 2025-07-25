<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Inscrição realizada para {{ $utente->name }}</title>
</head>
<body style="background-color: #ffffff; margin: 0; padding: 0; font-family: Arial, sans-serif;">

    <!-- Banner -->
    <div style="background-color: #ffffff; padding: 10px 0; text-align: center; border-bottom: 1px solid #ddd;">
        <img src="{{ asset('img/parquesegurobanneremail.png') }}" alt="Banner" style="max-width: 80%; max-height: 150px; height: auto; display: inline-block;">
    </div>

    <!-- Conteúdo Principal -->
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; max-width: 700px; margin: 20px auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); line-height: 1.6;">

        <p style="color: #333333;">Caro(a) <strong>{{ $encarregado->nome_completo }}</strong>,</p>

        <p style="color: #333333;">Informamos que o seu educando <strong>{{ $utente->name }}</strong> está inscrito no programa <strong>"{{ $programa }}"</strong>.</p>

        @if (!empty($data_inicio) && !empty($data_termino))
    <p>O programa decorrerá entre {{ Helper::getFormattedDateObject($data_inicio, 'date', false) }} e {{ Helper::getFormattedDateObject($data_termino, 'date', false) }}.</p>
@elseif (!empty($data_inicio))
    <p>O programa tem início a {{ Helper::getFormattedDateObject($data_inicio, 'date', false) }}.</p>
@elseif (!empty($data_termino))
    <p>O programa termina a {{ Helper::getFormattedDateObject($data_termino, 'date', false) }}.</p>
@endif



        <!-- Notas adicionais -->
        @if (!empty($note))
        <div style="background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; padding: 15px; border-radius: 5px; margin-top: 20px; font-size: 1em; line-height: 1.5;">
            <strong>Notas adicionais:</strong> {{ $note }}
        </div>
        @endif

        <!-- Administrador -->
        @if (!empty($admin))
        <p style="color: #333333;"><strong>Operação de inscrição realizada por:</strong> {{ $admin->present()->fullName() }}</p>
        @endif
        </div>
        @if (!empty($eula))
    <div style="text-align: center; margin-top: 30px; font-size: 1em; color: #444;">
        <h3 style="color: #1d3557;">Regulamento</h3>
        <p>{!! nl2br(e($eula)) !!}</p>
    </div>
@endif



       
        <!-- Rodapé -->
        <div style="width: 100%; text-align: center; font-size: 0.65em; color: #666; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd;">
    <p style="text-align: center; margin: 5px 0;">
        Este é um e-mail gerado automaticamente. Por favor, não responda.
    </p>
    <p style="text-align: center; margin: 5px 0;">
        Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações |
        <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" rel="noopener noreferrer" style="color: #1976d2; text-decoration: none;">
            Política de Privacidade
        </a>
    </p>
</div>


</body>
</html>
