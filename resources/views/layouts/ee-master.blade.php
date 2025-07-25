<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>@yield('title') Parque Seguro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/dist/all.css') }}">
    @stack('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* O teu CSS atual */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background-color: #004080;
            margin-left: 0 !important;
        }

        .navbar {
            padding: 8px 0;
            color: white;
            margin-left: 0 !important;
        }

        .navbar-container {
            max-width: 970px;
            margin: 0 auto;
            padding: 0 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            flex-wrap: wrap;
        }

        .navbar-logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            flex: 0 0 auto;
        }

        .navbar-logo-link img {
            height: 40px;
            margin: 0;
            display: block;
        }

        .navbar-session-info {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
            gap: 6px;
        }

        .navbar-session-info span,
        .navbar-session-info div span {
            font-size: 1rem;
            background-color: #e3f2fd;
            color: #004080;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .navbar-session-info div {
            font-size: 1.1rem;
        }

        .navbar-session-info form button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .navbar-session-info form button:hover {
            background-color: #c82333;
        }

        .content-wrapper {
            padding-top: 100px;
            padding-bottom: 60px;
            margin-left: 0 !important;
        }

        section.content-header h1 {
            font-size: 1.8rem;
            margin-top: 0;
            padding: 0 16px;
            max-width: 970px;
            margin: 0 auto;
        }

        section.content {
            padding: 0 16px;
            max-width: 970px;
            margin: 0 auto;
        }

.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #004080;
    color: white;
    padding: 2px;
    font-size: 1.1rem;
    z-index: 1020;
    min-height: 30px;
    display: flex; /* Ativa o Flexbox */
    flex-direction: column; /* Organiza os itens em coluna */
    align-items: center; /* Centraliza os itens horizontalmente */
    justify-content: center; /* Centraliza os itens verticalmente, se houver espaço */
}

.footer-links {
    margin-top: 5px; /* Adiciona um pequeno espaçamento entre a linha de cima e os links */
}

/* Opcional: para ajustar o espaçamento entre os links se o '|' não for suficiente */
.footer-links a {
    margin: 0 5px; /* Adiciona 5px de margem à esquerda e à direita de cada link */
    color: white; /* Garante que os links continuem brancos */
    text-decoration: none; /* Remove sublinhado padrão */
}

.footer-links a:hover {
    text-decoration: underline; /* Adiciona sublinhado ao passar o rato */
}



        @media (min-width: 769px) {
            .navbar-logo-link {
                margin-right: 500px;
            }
        }

        .navbar-container {
            flex-direction: row;
            align-items: center;
            padding: 10px 12px;
        }

        .navbar-logo-link {
            width: auto;
            justify-content: flex-start;
            margin-bottom: 0;
        }

        .navbar-session-info {
            width: auto;
            align-items: flex-end;
            text-align: right;
        }

        .navbar-session-info form button {
            max-width: 200px;
            margin-top: 0;
        }

        .content-wrapper {
            padding-top: 140px;
        }

        .icone-rotativo {
            display: inline-block;
            animation: girar 2s ease-in-out infinite;
            animation-delay: 0s;
            animation-fill-mode: both;
        }

        @keyframes girar {
            0%   { transform: rotate(0deg); }
            20%  { transform: rotate(360deg); }
            100% { transform: rotate(360deg); }
        }

        .navbar-session-info .icone-rotativo {
            background: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        .modal.show {
        display: block !important;
        opacity: 1 !important;
        z-index: 11000 !important; /* Acima do #aviso-rgpd (z-index: 10000) */
    }
    .modal-backdrop {
        z-index: 10990 !important; /* Abaixo do modal, mas acima do #aviso-rgpd */
        opacity: 0.5 !important;
    }
    
  
    .btn-secondary {
    background-color: #6c757d !important; /* Cinza escuro do Bootstrap */
    border-color: #6c757d !important;
    color: #fff !important;
}
.btn-secondary:hover {
    background-color: #5c636a; /* Cor mais escura para hover */
    border-color: #565e64;
}
    </style>
    
    <link rel="icon" href="{{ asset('img/favicon_ee.ico') }}" type="image/x-icon">
</head>

<body class="skin-blue">
    <div class="wrapper">
        {{-- HEADER --}}
        <header class="main-header">
            <nav class="navbar navbar-static-top">
                <div class="navbar-container">
                    {{-- Logo --}}
                    <a href="{{ url('/ee/dashboard') }}" class="navbar-logo-link">
                        <img src="https://parque-seguro.jf-parquedasnacoes.pt:8126/uploads/setting-logo-1-HIRQksBmOI.png"
                             alt="Logo Parque Seguro">
                    </a>

                    {{-- Boas-vindas + Contador + Logout --}}
                    <div class="navbar-session-info">
                        @if (Auth::guard('ee')->check())
                            @php
                                $responsavelAutenticado = Auth::guard('ee')->user();
                            @endphp

                            <div style="font-size: 1.1rem;">
                                👋 Bem-vindo EE,
                                <strong>
                                    @php
                                        $partesNome = explode(' ', trim($responsavelAutenticado->nome_completo ?? ''));
                                        $primeiro = $partesNome[0] ?? '';
                                        $ultimo = count($partesNome) > 1 ? $partesNome[count($partesNome) - 1] : '';
                                    @endphp
                                    {{ $primeiro }} {{ $ultimo }}
                                </strong>
                            </div>

                            @if(session('ee_session_expires_at'))
                                <div>
                                    <span class="icone-rotativo">⏳</span> Tempo restante da sessão:
                                    <strong><span id="contador">--:--</span></strong>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('ee.logout') }}">
                                @csrf
                                <button type="submit">Terminar Sessão</button>
                            </form>
                        @endif
                    </div>
                </div>
            </nav>
        </header>

        {{-- CONTEÚDO --}}
        <main class="content-wrapper" id="main">
            <section class="content-header">
                <h1>@yield('title')</h1>
            </section>
            <section class="content">
                @include('notifications')
                @yield('content')
            </section>
        </main>

{{-- RODAPÉ --}}
<footer class="footer text-center">
    <div>
        Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações
    </div>
    <div class="footer-links">
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalSobre"> Sobre esta aplicação</a> |
        <a href="https://www.jf-parquedasnacoes.pt/protecao-de-dados" target="_blank">Política de Proteção de Dados</a>
    </div>
</footer>

<div class="modal fade" id="modalSobre" tabindex="-1" aria-labelledby="modalSobreLabel" aria-hidden="true" style="z-index: 11000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSobreLabel">Sobre esta aplicação</h5>
            </div>
            <div class="modal-body">
                <p>Esta aplicação, "Parque Seguro", foi desenvolvida e personalizada pelo <strong>Núcleo de Sistemas de Informação</strong> da Junta de Freguesia do Parque das Nações, entre 2024 e {{ date('Y') }}.</p>

                <p>Baseia-se no projeto <strong>Snipe-IT</strong>, © Grokability, Inc., licenciado sob a <a href="https://www.gnu.org/licenses/agpl-3.0.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0 (AGPL v3)</a>. As modificações realizadas pela Junta de Freguesia do Parque das Nações também são distribuídas sob a mesma licença.</p>

                <p><strong>Não existe qualquer garantia para esta aplicação</strong>, na medida permitida pela lei aplicável. O programa é fornecido "tal como está".</p>

                <p>Os utilizadores podem redistribuir e/ou modificar esta aplicação sob os termos da AGPL v3.</p>

                <p>O **código-fonte modificado** está publicamente disponível no nosso repositório GitHub: <a href="https://github.com/Junta-de-Freguesia-do-Parque-das-Nacoes/parque-seguro" target="_blank" rel="noopener noreferrer">github.com/Junta-de-Freguesia-do-Parque-das-Nacoes/parque-seguro</a>. Para mais informações, pode também contactar: <a href="mailto:nsi@jf-parquedasnacoes.pt">nsi@jf-parquedasnacoes.pt</a>.</p>

                <p class="text-muted"><small>Junta de Freguesia do Parque das Nações © 2016–{{ date('Y') }}. Todos os direitos reservados.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
    

    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                per_page: 50
            }
        };
    </script>

    {{-- SCRIPTS --}}
    <script src="{{ mix('js/dist/all.js') }}"></script>
    @stack('js')

</body>
</html>