<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'QR Code Scanner')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
			padding-top: 60px
        }

        /* Banner fixo */
        .banner {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .banner img {
            width: 100%;
            max-width: 600px;
            height: auto;
            display: block;
            margin: 0 auto;
        }



        /* Conteúdo principal */
        .container {
            flex: 1;
            padding: 20px;
            margin: 0 auto;
            max-width: 600px;
            text-align: center;
        }

        /* Rodapé fixo */
        footer {
                background-color: #004080;
    color: white;
    text-align: center;
    padding: 5px;
    font-size: 12px;
    width: 100%;
    position: fixed;
    bottom: 0;
    left: 0;
    border-top: 1px solid #3e8e41;;
            z-index: 1000;
        }

        /* Espaço reservado para o rodapé */
        .footer-space {
            height: 50px; /* Altura do rodapé */
        }
    </style>
    @stack('styles') <!-- Estilos adicionais -->
</head>
<body>
<!-- Banner fixo clicável -->
<div class="banner">
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/" target="_blank">
        <img src="{{ asset('img/banner.png') }}" alt="Banner">
    </a>
</div>


    <!-- Espaço reservado para o banner -->
    <div class="banner-space"></div>

    <!-- Conteúdo principal -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Espaço reservado para o rodapé -->
    <div class="footer-space"></div>

    <!-- Rodapé fixo -->
    <footer>
        Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações
    </footer>

    @stack('scripts') <!-- Scripts adicionais -->
</body>
</html>
