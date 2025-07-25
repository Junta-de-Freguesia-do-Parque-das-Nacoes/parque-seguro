<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificação de Entrada para {{ $utente->name }}</title>
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .responsavel-info .info {
            flex: 1;
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

        .alert-info {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
            border-radius: 5px;
            margin: 20px 0;
}

.alert-info:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.alert-info strong {
    font-weight: bold;
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
        <h1>Notificação de Entrada para {{ $utente->nome_apelido }}</h1>

    

<!-- Local (Escola ou Evento) -->
@if (!empty($utente->company))
<p><strong>Escola/Programas:</strong> {{ is_array($utente->company) ? ($utente->company['name'] ?? 'Local não disponível') : ($utente->company->name ?? 'Local não disponível') }}</p>
@endif

        <!-- Alerta de incidente para Check-in -->
        @if ($manutencao)
            <div class="alert-box">
                <strong>⚠️ Incidente Reportado Hoje:</strong>
                <p><strong>{{ $manutencao->title }}</strong></p>
                <p><em>{{ $manutencao->notes }}</em></p>
            </div>
        @endif

        <!-- Informação sobre o Check-in -->
        <div class="alert-info">
           <p> <strong>✅ Entrada realizada:</strong>
            {{ $utente->name }} foi devidamente registado no sistema como presente.</p>
        </div>

        <!-- Operação realizada por -->
       <p>Operação de Entrada realizada por:  {{ $utilizadorBackoffice->first_name ?? 'Não informado' }} {{ $utilizadorBackoffice->last_name ?? '' }}</p>




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
