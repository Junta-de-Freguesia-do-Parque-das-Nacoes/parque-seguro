<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informação para Recolha - Parque Seguro</title>
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

        h1, h2 {
            color: #1d3557;
            margin-bottom: 20px;
            font-size: 1.6em;
            text-align: center;
        }

        p {
            color: #555;
            margin: 10px 0;
            line-height: 1.6;
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

        .listagem-utentes {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .utente-box {
            margin-bottom: 25px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fefefe;
        }

        /* Removido estilo .utente-box img pois a imagem não será mais embutida */

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
    </style>
</head>
<body>

    <div class="banner">
        <img src="{{ asset('img/parquesegurobanneremail.png') }}" alt="Parque Seguro">
    </div>

    <div class="container">
        <h1>Olá!</h1>

        <p>Este e-mail foi enviado pelo Encarregado de Educação <b>{{ $responsavel->nome_completo }}</b> e contém os QR Codes necessários para a recolha dos educandos associados no sistema <b>Parque Seguro</b>.</p>
        <p>Estes QR Codes estão em anexo, identificados pelo nome de cada educando(a), e são a sua credencial para efetuar a recolha de forma segura e eficiente.</p>

        <div class="alert-box">
            <p><b>Importante:</b> Por favor, utilize o QR Code correspondente ao educando(a) no momento da recolha. Mantenha estes códigos em segurança e não os partilhe com terceiros não autorizados.</p>
        </div>

        <div class="listagem-utentes">
            <h2>QR Codes em Anexo</h2>

            @forelse ($utentes as $item)
                @php
                    $utente = $item['model'] ?? null;
                    $nome_educando = $utente->name ?? 'Educando(a) sem nome';
                    $ficheiro = $item['ficheiro'] ?? 'QR_Code.png';
                @endphp

                <div class="utente-box">
                    <p><b>Educando(a): {{ $nome_educando }}</b></p>
                    <p>O QR Code para {{ $nome_educando }} está no anexo: <code>{{ $ficheiro }}</code></p>
                </div>
            @empty
                <p style="color: red; text-align: center;">Não foram encontrados QR Codes de educandos associados neste e-mail.</p>
            @endforelse
        </div>

        <p style="margin-top: 30px; font-size: 0.95em;">
            Em caso de dúvidas ou se necessitar de mais informações, por favor, contacte diretamente o Encarregado de Educação <b>{{ $responsavel->nome_completo }}</b>.
        </p>
    </div>

    <footer>
        <p>Este é um e-mail gerado automaticamente. Por favor, não responda.</p>
        <p>
            Núcleo de Sistemas de Informação © 2016–{{ now()->year }} JF Parque das Nações |
            <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" rel="noopener noreferrer">
                Política de Privacidade
            </a>
        </p>
    </footer>

</body>
</html>