<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($action) }} para {{ $utente->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .banner {
            background-color: #ffffff;
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .banner img {
            max-width: 80%;
            max-height: 150px;
            height: auto;
            display: inline-block;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            max-width: 700px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            line-height: 1.6;
        }

        h1 {
            color: #1d3557;
            margin-bottom: 20px;
            font-size: 1.8em;
            text-align: center;
        }

        p {
            color: #555;
            margin: 10px 0;
            line-height: 1.6;
        }

        .responsavel-info {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
            border-radius: 5px;
            margin: 20px 0;
        }

        .responsavel-info img {
    width: 100px;
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer;
        }



.responsavel-info .info {
    flex: 1;
    font-size: 1em;
    line-height: 1.5;
    margin-left: 15px;  /* Adicionando espaço entre a imagem e o texto */
}

        .alert-box {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 0.95em;
            line-height: 1.5;
        }

        footer {
            text-align: center;
            font-size: 0.85em;
            color: #666;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        footer p {
            margin: 5px 0;
        }

        .preferences-link {
            margin: 20px 0;
            text-align: center;
        }

        .preferences-link a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #1976d2;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .preferences-link a:hover {
            background-color: #115293;
        }
    </style>
</head>
<body>

    <!-- Banner -->
    <div class="banner">
        <img src="{{ asset('img/parquesegurobanneremail.png') }}" alt="Banner">
    </div>

    <!-- Conteúdo Principal -->
    <div class="container">
        <h1>{{ ucfirst($action) }} realizada para {{ $utente->nome_apelido }}</h1>

        <!-- Data e Hora -->
        <p><strong>Data e Hora:</strong> {{ $dataHoraAcao->format('d/m/Y H:i') }}</p>

         <!-- Local (Escola ou Evento) -->
    @if (!empty($utente->company))
        <p><strong>Escola/Programas:</strong> {{ is_array($utente->company) ? ($utente->company['name'] ?? 'Local não disponível') : ($utente->company->name ?? 'Local não disponível') }}</p>
    @endif

        <!-- Alerta de incidente para Check-out -->
        @if ($action === 'Saída' && $manutencao)
            <div class="alert-box">
                <strong>⚠️ Alerta de Incidente:</strong>
                <p>Foi reportado um incidente hoje: <strong>{{ $manutencao->title }}</strong></p>
                <p><em>{{ $manutencao->notes }}</em></p>
            </div>
        @endif

       <!-- Informação sobre a saída -->
@if ($action === 'Saída')
    @if ($responsavel)
    <div class="responsavel-info">
    <img src="{{ $responsavel->image ? asset('' . $responsavel->image) : asset('img/anonimoadulto.png') }}" alt="Foto do Responsável" class="responsavel-img">
    <div class="info">
        <p><strong><i class="fas fa-user-check"></i>Saiu com a pessoa autorizada:</strong> {{ $responsavel->name }}</p>
        @if ($grauParentesco)
            <p><strong>Parentesco:</strong> {{ $grauParentesco }}</p>
        @endif
    </div>
</div>

    @elseif ($nomeResponsavel)
        <div class="alert-box">
            <p><strong>Saiu com uma pessoa com autorização excepcional:</strong></p>
            {{ $nomeResponsavel }}</br>
            @if ($nrCC)
                <p><strong>Documento de Identificação:</strong> {{ $nrCC }}</p>
            @endif
        </div>
    @else
        <div class="alert-box">
            <strong>⚠️ Informação:</strong>
            <p>O aluno saiu de forma autónoma. 🚶‍️</p>
        </div>
    @endif
@elseif ($action === 'Entrada')
    <div class="alert-box">
        <strong>✅ Entrada:</strong>
        <p>O utente foi devidamente registado no sistema como presente.</p>
    </div>
@endif

<!-- Mensagem sobre a importância da foto -->
@if ($responsavel && !$responsavel->image)
    <div class="alert-box">
        <p><strong>⚠️Importante:</strong> A foto da pessoa autorizada é fundamental para uma identificação rápida e para reforçar a segurança. Para atualizar a foto, envie um e-mail para educacao@jf-parquedasnacoes.pt.</p>
    </div>
@endif



        <!-- Operação realizada por -->
        <p><strong>Operação de {{ ucfirst($action) }} realizada por:</strong> 
            {{ $utilizadorBackoffice->first_name ?? 'Não informado' }} {{ $utilizadorBackoffice->last_name ?? '' }}
        </p>

                <!-- Link para o Portal do Encarregado de Educação -->
<div class="preferences-link" style="display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 20px;">
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/ee/login" style="display: inline-flex; align-items: center; padding: 10px 15px; background-color: #1976d2; color: #fff; text-decoration: none; border-radius: 5px; font-size: 0.9em; width: auto;">
        <img src="https://parque-seguro.jf-parquedasnacoes.pt:8126/img/logoportal_ee.png" alt="Logo do Portal EE" style="width: 24px; height: auto; margin-right: 8px;">
        Aceder ao Portal do Encarregado de Educação
    </a>
</div>
      <p>
            Aceda ao <strong>Portal do Encarregado de Educação</strong> para gerir as suas preferências de notificação, atualizar os dados dos seus educandos, adicionar ou editar responsáveis autorizados, e acompanhar todas as informações importantes relacionadas ao seu educando.
            <br>
            Faça login e tenha acesso a uma gestão simples e eficaz das suas autorizações e notificações.
        </p>
    </div>
    </div>

    <!-- Rodapé -->
    <footer>
    <p>Este é um e-mail gerado automaticamente. Por favor, não responda.</p>
    <p>
        Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações |
        <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" rel="noopener noreferrer">
            Política de Privacidade
        </a>
    </p>
</footer>


</body>
</html>
