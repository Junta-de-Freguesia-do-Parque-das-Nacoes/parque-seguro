<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificação</title> {{-- CORRIGIDO --}}
    <style>
        /* O seu CSS continua aqui, está perfeito */
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
        .info-box {
            text-align: center;
            margin: 20px 0;
        }
        .info-box h2 {
            font-size: 1.5em;
            color: #333;
        }
        .highlight {
            display: inline-block;
            font-weight: bold;
            color: #333;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 1.2em;
            margin-top: 10px;
            letter-spacing: 1px;
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
    </style>
</head>
<body>

    <div class="banner">
        {{-- A função asset() está correta, não é a variável $asset --}}
        <img src="{{ asset('img/parquesegurobanneremail.png') }}" alt="Banner">
    </div>

    <div class="container">
        <h1>Código de Verificação</h1>

        <div class="info-box">
            <h3>O seu código de acesso</h3> {{-- CORRIGIDO --}}
            <p class="highlight">{{ $codigo }}</p> {{-- CORRIGIDO de $code para $codigo --}}
        </div>

        <p>Este código é válido por 10 minutos. Caso não tenha solicitado esta ação, ignore este e-mail.</p>
    </div>

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