<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($snipeSettings) && ($snipeSettings->site_name) ? $snipeSettings->site_name : 'Parque Seguro' }}</title>

    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">
    {{-- stylesheets --}}
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">

    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": 50
            }
        };
    </script>


    @if (($snipeSettings) && ($snipeSettings->header_color))
        <style>
        .main-header .navbar, .main-header .logo {
        background-color: {{ $snipeSettings->header_color }};
        background: -webkit-linear-gradient(top,  {{ $snipeSettings->header_color }} 0%,{{ $snipeSettings->header_color }} 100%);
        background: linear-gradient(to bottom, {{ $snipeSettings->header_color }} 0%,{{ $snipeSettings->header_color }} 100%);
        border-color: {{ $snipeSettings->header_color }};
        }
        .skin-blue .sidebar-menu > li:hover > a, .skin-blue .sidebar-menu > li.active > a {
        border-left-color: {{ $snipeSettings->header_color }};
        }

        .btn-primary {
        background-color: {{ $snipeSettings->header_color }};
        border-color: {{ $snipeSettings->header_color }};
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

        </style>
    @endif

    @if (($snipeSettings) && ($snipeSettings->custom_css))
        <style>
            {!! $snipeSettings->show_custom_css() !!}
        </style>
    @endif

</head>

<body class="hold-transition login-page">

    @if (($snipeSettings) && ($snipeSettings->logo!=''))
        <center>
            <a href="{{ config('app.url') }}"><img id="login-logo" src="{{ Storage::disk('public')->url('').e($snipeSettings->logo) }}"></a>
        </center>
    @endif
  <!-- Content -->
  @yield('content')



    <div class="text-center" style="padding-top: 100px;">
        @if (($snipeSettings) && ($snipeSettings->privacy_policy_link!=''))
        <a target="_blank" rel="noopener" href="{{  $snipeSettings->privacy_policy_link }}" target="_new">{{ trans('admin/settings/general.privacy_policy') }}</a>
    @endif
    </div>

    {{-- Javascript files --}}
    <script src="{{ url(mix('js/dist/all.js')) }}" nonce="{{ csrf_token() }}"></script>


    @stack('js')
</body>
{{-- RODAPÉ --}}
<footer class="footer text-center">
    <div>
        Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações
    </div>
    <div class="footer-links">
        <a href="#" data-toggle="modal" data-target="#modalSobre">Sobre esta aplicação</a> |
        <a href="https://www.jf-parquedasnacoes.pt/protecao-de-dados" target="_blank">Política de Proteção de Dados</a>
    </div>
</footer>

<div class="modal fade" id="modalSobre" tabindex="-1" role="dialog" aria-labelledby="modalSobreLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSobreLabel">Sobre esta aplicação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Esta aplicação, "Parque Seguro", foi desenvolvida e personalizada pelo **Núcleo de Sistemas de Informação** da Junta de Freguesia do Parque das Nações, entre 2024 e {{ date('Y') }}.</p>

                <p>Baseia-se no projeto **Snipe-IT**, © Grokability, Inc., licenciado sob a <a href="https://www.gnu.org/licenses/agpl-3.0.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0 (AGPL v3)</a>. As modificações realizadas pela Junta de Freguesia do Parque das Nações também são distribuídas sob a mesma licença.</p>

                <p>**Não existe qualquer garantia para esta aplicação**, na medida permitida pela lei aplicável. O programa é fornecido "tal como está".</p>

                <p>Os utilizadores podem redistribuir e/ou modificar esta aplicação sob os termos da AGPL v3.</p>

                <p>O **código-fonte modificado** está publicamente disponível no nosso repositório GitHub: <a href="https://github.com/Junta-de-Freguesia-do-Parque-das-Nacoes/snipeit-parque-seguro" target="_blank" rel="noopener noreferrer">github.com/Junta-de-Freguesia-do-Parque-das-Nacoes/snipeit-parque-seguro</a>. Para mais informações, pode também contactar: <a href="mailto:nsi@jf-parquedasnacoes.pt">nsi@jf-parquedasnacoes.pt</a>.</p>

                <p class="text-muted"><small>Junta de Freguesia do Parque das Nações © 2016–{{ date('Y') }}. Todos os direitos reservados.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
</html>
