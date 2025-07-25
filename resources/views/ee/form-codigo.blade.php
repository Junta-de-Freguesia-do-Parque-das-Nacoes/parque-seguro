@extends('layouts.ee-master')

@section('title', 'Código de Acesso')

@section('content')

 


    {{-- Conteúdo Principal: Formulário de Código de Acesso --}}
    <section class="content">
        <style>
            /* SEGUNDO BLOCO CSS - Card e Formulário da Página "Código de Acesso" (COM AJUSTES) */

            .access-code-card-container {
                padding: 0 1rem 3rem 1rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: auto;
                justify-content: flex-start;
            }

            .card.access-code-card {
                width: 100%;
                max-width: 480px;
                background-color: #fff;
                border-radius: 0.5rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .card-header-custom {
                background-color: #004080;
                color: white;
                padding: 1.25rem 1rem;
                border-top-left-radius: calc(0.5rem - 1px);
                border-top-right-radius: calc(0.5rem - 1px);
                text-align: center;
            }
            .card-header-custom .login-logo {
                max-height: 72px; width: auto; display: block; margin: 0 auto 12px auto;
            }
            .card-header-custom h4 {
                margin-bottom: 0; font-size: 1.5rem;
            }

            .card-body.access-code-body {
                padding-top: 1rem;
                padding-right: 2rem;
                padding-bottom: 2rem;
                padding-left: 2rem;
            }

            .form-section + .form-section { margin-top: 1.5rem; }
            .form-label.visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            .form-control { display: block; width: 100%; padding: 0.5rem 0.75rem; font-size: 1rem; font-weight: 400; line-height: 1.5; color: #212529; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; appearance: none; border-radius: 0.25rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; }
            .form-control:focus { border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
            .form-control-lg { padding: 0.75rem 1rem; font-size: 1.15rem; border-radius: 0.3rem; }
            input[readonly].form-control { background-color: #e9ecef; opacity: 1; }
            .input-group-custom { position: relative; }
            .input-group-custom .form-control { padding-left: 2.75rem; }
            .input-group-custom .input-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d; z-index: 2; pointer-events: none; }
            .form-text { font-size: 0.875em; color: #6c757d; }
            .form-text.text-muted { color: #6c757d !important; }
            .form-text.mt-2 { margin-top: 0.5rem !important; }

            .btn { display: inline-block; box-sizing: border-box; font-weight: 400; line-height: 1.5; text-align: center; text-decoration: none; vertical-align: middle; cursor: pointer; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 0.5rem 1rem; font-size: 1rem; border-radius: 0.25rem; transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; }
            .btn-lg { padding: 0.75rem 1.25rem; font-size: 1.15rem; border-radius: 0.3rem; }
            .btn-primary-custom { color: #ffffff; background-color: #004080; border-color: #004080; }
            .btn-primary-custom:hover { color: #ffffff; background-color: #002f66; border-color: #002f66; }
            .btn-secondary-custom { color: #004080; background-color: #e9ecef; border: 1px solid #ced4da; }
            .btn-secondary-custom:hover { color: #002f66; background-color: #d6d8db; border-color: #bfc4c9; }

            .d-flex { display: flex !important; }
            .flex-column { flex-direction: column !important; }
            .gap-2 { gap: 0.5rem !important; }
            .mt-3 { margin-top: 1rem !important; } /* Adicionado para o novo link */
            .mt-4 { margin-top: 1.5rem !important; }
            .mt-5 { margin-top: 3rem !important; }
            .text-center { text-align: center !important; }
            .flex-fill { flex: 1 1 auto !important; }
            .w-100 { width: 100% !important; display: block !important; }
            .d-none { display: none !important; }

            .alert { position: relative; padding: 1rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
            .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
            .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
            .alert-dismissible .btn-close { position: absolute; top: 0; right: 0; z-index: 2; padding: 1.25rem 1rem; }
            .btn-close { box-sizing: content-box; width: 1em; height: 1em; padding: 0.25em 0.25em; color: #000; background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat; border: 0; border-radius: 0.25rem; opacity: 0.5; }
            .btn-close:hover { opacity: 0.75; }
            .alert ul { padding-left: 20px; margin-bottom: 0; }

            .tooltip-wrapper { position: relative; display: inline-block; cursor: help; }
            .tooltip-wrapper a { display: inline-block; padding: 6px 10px; color: #004080; text-decoration: none; font-size: 1.1rem; }
            .tooltip-wrapper a:hover { color: #002f66; text-decoration: underline; }
            .custom-tooltip { display: none; position: absolute; bottom: 125%; left: 50%; transform: translateX(-50%); background-color: #004080; color: #fff; padding: 12px 16px; border-radius: 4px; font-size: 1.3rem; white-space: normal; width: max-content; min-width: 260px; max-width: 320px; text-align: left; box-shadow: 0 2px 6px rgba(0,0,0,0.2); z-index: 1000; }
            .tooltip-wrapper:hover .custom-tooltip, .tooltip-wrapper:focus-within .custom-tooltip { display: block; }
            .custom-tooltip strong { font-weight: bold; }

            .spinner-border { display: inline-block; width: 1rem; height: 1rem; vertical-align: -0.125em; border: 0.20em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; }
            .spinner-border-sm { width: 1rem; height: 1rem; border-width: 0.2em; }
            @keyframes spinner-border { to { transform: rotate(360deg); } }

            /* ESTILOS PARA O LINK "VOLTAR AO LOGIN" */
            .link-voltar {
                display: inline-block;
                color: #004080;
                text-decoration: none;
                padding: 0.5rem 0; /* Apenas padding vertical para espaçamento */
                font-size: 0.9rem;
                transition: color 0.2s ease, text-decoration 0.2s ease;
            }
            .link-voltar:hover,
            .link-voltar:focus {
                color: #002f66;
                text-decoration: underline;
            }
            .link-voltar .fas { /* Estilo para o ícone FontAwesome, se usado */
                margin-right: 0.4em;
            }


            @media (max-width: 575.98px) {
                .access-code-card-container { padding-left: 0.75rem; padding-right: 0.75rem; padding-bottom: 2rem; }
                .card-header-custom .login-logo { max-height: 60px; margin-bottom: 10px; }
                .card-header-custom h4 { font-size: 1.25rem; }
                .card-body.access-code-body { padding-top: 0.75rem; padding-right: 1.5rem; padding-bottom: 1.5rem; padding-left: 1.5rem; }
                .form-control-lg { font-size: 1rem; padding: 0.6rem 0.8rem; }
                .input-group-custom .form-control { padding-left: 2.5rem; }
                .input-group-custom .input-icon { left: 0.8rem; }
                .btn-lg { font-size: 1rem; padding: 0.6rem 1rem; }
                .tooltip-wrapper a { font-size: 1rem; }
                .custom-tooltip { font-size: 1.2rem; padding: 10px 12px; max-width: calc(100vw - 32px); min-width: auto; left: 50%; transform: translateX(-50%); }
                .text-center.mt-5 { margin-top: 2rem !important; } /* Espaço acima do tooltip */
                .text-center.mt-3 { margin-top: 1.5rem !important; } /* Espaço acima do link "voltar" */
            }
            @media (min-width: 576px) { .flex-sm-row { flex-direction: row !important; } }
            @media (min-width: 576px) and (max-width: 767.98px) { .card-header-custom h4 { font-size: 1.4rem; } }
        </style>

        <div class="access-code-card-container">
            <div class="card access-code-card">
                <div class="card-header card-header-custom">
                    <img src="{{ asset('img/logoportal_ee.png') }}" alt="Símbolo EE" class="login-logo">
                    <h4 style="margin-bottom:0;">Inserir Código de Acesso</h4>
                </div>
                <div class="card-body access-code-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible show" role="alert">
                            {{ session('success') }}
                            @if(session('email_masc'))
                                <br><small>Código enviado para: <strong>{{ session('email_masc') }}</strong></small>
                            @elseif (isset($email))
                                <br><small>Código enviado para: <strong>{{ $email }}</strong></small>
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    @endif
                    @if (session('error'))
    <div class="alert alert-danger alert-dismissible  show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

                    @if ($errors->any())
                        <div class="alert alert-danger pb-0 alert-dismissible show" role="alert">
                            <ul style="margin-bottom:0; padding-left: 20px;">
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ee.verificar-codigo') }}" id="formVerificarCodigo">
                        @csrf
                        <div class="form-section">
                            <label for="email" class="form-label visually-hidden">Endereço de Email</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control form-control-lg"
                                       value="{{ old('email', $email ?? request('email')) }}" readonly>
                            </div>
                        </div>
                        <div class="form-section">
                            <label for="codigo" class="form-label visually-hidden">Código Recebido</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fas fa-key"></i></span>
                                <input type="text" name="codigo" id="codigo" class="form-control form-control-lg"
                                       placeholder="Insira o código de 6 dígitos" required autofocus>
                            </div>
                            <small id="codigoHelp" class="form-text text-muted mt-2">
                                O código foi enviado para o seu email.
                            </small>
                        </div>
                    </form>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="submit" form="formVerificarCodigo" class="btn btn-primary-custom btn-lg flex-fill" id="submitBtn">
                            <span id="submitText">Verificar Código</span>
                            <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>

                        @if (request('email') || isset($email))
                        <form method="GET" action="{{ route('ee.reenviar-codigo') }}" class="flex-fill" id="formReenviarCodigo">
                            <input type="hidden" name="email" value="{{ $email ?? request('email') }}">
                            <button type="submit" class="btn btn-secondary-custom btn-lg w-100">
                                Não recebeu?
                            </button>
                        </form>
                        @endif
                    </div>

                    <div class="text-center mt-5">
                        <span class="tooltip-wrapper">
                            <a href="#" class="text-decoration-none" aria-describedby="tooltip-ajuda">
                                💡 Precisa de ajuda para aceder?
                            </a>
                            <div class="custom-tooltip" id="tooltip-ajuda" role="tooltip">
                                Apenas Encarregados de Educação registados com email têm acesso.<br>
                                Se pretende acesso, contacte:<br>
                                <strong>parque.seguro@jf-parquedasnacoes.pt</strong>
                            </div>
                        </span>
                    </div>

                    {{-- NOVO LINK "VOLTAR AO LOGIN" --}}
                    <div class="text-center mt-3">
    <a href="{{ route('ee.login') }}" class="btn btn-secondary-custom btn-lg w-100"> {{-- Classes adicionadas aqui --}}
        <i class="fas fa-arrow-left"></i> Voltar ao Login
    </a>
</div>

                </div> {{-- Fim .card-body --}}
            </div> {{-- Fim .card --}}
        </div> {{-- Fim .access-code-card-container --}}

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formVerificar = document.getElementById('formVerificarCodigo');
            if (formVerificar) {
                formVerificar.addEventListener('submit', function (event) {
                    const btn = document.getElementById('submitBtn');
                    if (btn) {
                        btn.disabled = true;
                        const submitText = document.getElementById('submitText');
                        if (submitText) submitText.textContent = 'A verificar...';
                        const spinner = document.getElementById('spinner');
                        if (spinner) spinner.classList.remove('d-none');
                    }
                });
            }

            const tooltipLink = document.querySelector('.tooltip-wrapper a');
            const tooltip = document.querySelector('.custom-tooltip');
            const tooltipWrapper = document.querySelector('.tooltip-wrapper');

            if (tooltipLink && tooltip && tooltipWrapper) {
                tooltipLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    tooltip.style.display = tooltip.style.display === 'block' ? 'none' : 'block';
                });
                tooltipLink.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        tooltip.style.display = tooltip.style.display === 'block' ? 'none' : 'block';
                    }
                });
                document.addEventListener('click', function(event) {
                    if (tooltip.style.display === 'block' && !tooltipWrapper.contains(event.target)) {
                        tooltip.style.display = 'none';
                    }
                });
                tooltipLink.addEventListener('blur', function() {
                    setTimeout(() => {
                        if (!tooltipWrapper.matches(':hover') && document.activeElement !== tooltipLink && !tooltip.contains(document.activeElement)) {
                           tooltip.style.display = 'none';
                        }
                    }, 150);
                });
                tooltipWrapper.addEventListener('mouseleave', function() {
                   if (document.activeElement !== tooltipLink && !tooltip.contains(document.activeElement) ) {
                        tooltip.style.display = 'none';
                   }
                });
            }
        });
        </script>
    </section> {{-- Fim section.content --}}
@endsection