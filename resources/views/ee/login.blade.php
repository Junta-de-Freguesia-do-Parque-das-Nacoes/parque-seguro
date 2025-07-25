@extends('layouts.ee-master')

@section('title', 'Login EE')

@section('content')

    {{-- Conteúdo Principal: Formulário de Login --}}
    <section class="content">
        <style>
            /* Estilos Específicos para o Card de Login */

            .login-card-container {
                padding: 0 1rem 3rem 1rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: auto;
                justify-content: flex-start;
            }

            .card.login-card {
                width: 100%;
                max-width: 480px; /* Consistente com a página anterior */
                background-color: #fff;
                border-radius: 0.5rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .card-header-custom.login-header {
                background-color: #004080;
                color: white;
                padding: 1.25rem 1rem;
                border-top-left-radius: calc(0.5rem - 1px);
                border-top-right-radius: calc(0.5rem - 1px);
                text-align: center;
            }

            .login-logo {
                max-height: 72px;
                width: auto;
                display: block;
                margin: 0 auto 12px auto;
            }

            .login-header h4 {
                margin-bottom: 0;
                font-size: 1.3rem; /* Ajuste para texto mais longo "Autenticação..." */
            }

            /* Paddings do card-body para serem idênticos à página de "Código de Acesso" que usava p-4 pt-3 */
            .card-body.login-body {
                padding-top: 1rem;    /* Equivalente ao pt-3 */
                padding-right: 2rem;   /* Equivalente ao p-4 */
                padding-bottom: 2rem;  /* Equivalente ao p-4 */
                padding-left: 2rem;    /* Equivalente ao p-4 */
            }

            .form-label.visually-hidden {
                position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
                overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
            }

            .form-control {
                display: block; width: 100%; padding: 0.5rem 0.75rem; font-size: 1rem;
                font-weight: 400; line-height: 1.5; color: #212529; background-color: #fff;
                background-clip: padding-box; border: 1px solid #ced4da; appearance: none;
                border-radius: 0.25rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            .form-control:focus {
                border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
            .form-control-lg {
                padding: 0.75rem 1rem; font-size: 1.15rem; border-radius: 0.3rem;
            }

            .input-group-custom { position: relative; }
            .input-group-custom .form-control { padding-left: 2.75rem; }
            .input-group-custom .input-icon {
                position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
                color: #6c757d; z-index: 2; pointer-events: none;
            }

            .form-text { font-size: 0.875em; color: #6c757d; }
            .form-text.text-muted { color: #6c757d !important; }
            .form-text.mt-2 { margin-top: 0.5rem !important; }

            /* ESTILOS DO BOTÃO (REFORÇADOS) */
            .btn {
                display: inline-block;
                box-sizing: border-box; /* Adicionado para consistência no cálculo de tamanho */
                font-weight: 400;
                line-height: 1.5;
                text-align: center;
                text-decoration: none;
                vertical-align: middle;
                cursor: pointer;
                user-select: none;
                background-color: transparent;
                border: 1px solid transparent;
                padding: 0.5rem 1rem; /* Padding base */
                font-size: 1rem; /* Tamanho de fonte base */
                border-radius: 0.25rem;
                transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            .btn-lg {
                padding: 0.75rem 1.25rem; /* Padding para botões grandes */
                font-size: 1.15rem; /* Tamanho de fonte para botões grandes */
                border-radius: 0.3rem;
            }

            .btn-primary-custom {
                color: #ffffff; /* Cor do texto EXPLICITAMENTE branca */
                background-color: #004080; /* Cor de fundo EXPLICITAMENTE azul */
                border-color: #004080; /* Cor da borda EXPLICITAMENTE azul */
                /* A propriedade display será 'inline-block' herdada de .btn.
                   Se .w-100 for aplicado, ele se tornará 'block' ou terá width 100%. */
            }
            .btn-primary-custom:hover {
                color: #ffffff;
                background-color: #002f66; /* Azul mais escuro */
                border-color: #002f66;
            }
            /* FIM DOS ESTILOS DO BOTÃO */


            .alert {
                position: relative; padding: 1rem 1rem; margin-bottom: 1rem;
                border: 1px solid transparent; border-radius: 0.25rem;
            }
            .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
            .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
            .alert.mt-2 { margin-top: 0.5rem !important; }
            .alert.w-100 { width: 100% !important; }


            .h-captcha-container {
                 display: flex;
                 justify-content: center;
                 margin-bottom: 1rem;
            }

            .spinner-border {
                display: inline-block; width: 1rem; height: 1rem; vertical-align: -0.125em;
                border: 0.20em solid currentColor; border-right-color: transparent;
                border-radius: 50%; animation: spinner-border .75s linear infinite;
            }
            .spinner-border-sm { width: 1rem; height: 1rem; border-width: 0.2em; }
            @keyframes spinner-border { to { transform: rotate(360deg); } }
            .d-none { display: none !important; }


            .mb-3 { margin-bottom: 1rem !important; }
            .w-100 { width: 100% !important; display: block !important; } /* Adicionado display: block para w-100 em botões */
            .mt-3 { margin-top: 1rem !important; }
            .text-center { text-align: center !important; }


            @media (max-width: 575.98px) {
                .login-card-container {
                    padding-left: 0.75rem; padding-right: 0.75rem; padding-bottom: 2rem;
                }
                .login-header h4 {
                    font-size: 1.2rem;
                }
                /* Paddings do card-body para mobile, consistentes com p-4 (1.5rem) e pt-3 (0.75rem) reduzidos */
                .card-body.login-body {
                    padding-top: 0.75rem;   /* Equivalente ao pt-3 reduzido */
                    padding-right: 1.5rem;  /* Equivalente ao p-4 reduzido */
                    padding-bottom: 1.5rem; /* Equivalente ao p-4 reduzido */
                    padding-left: 1.5rem;   /* Equivalente ao p-4 reduzido */
                }
                .form-control-lg { font-size: 1rem; padding: 0.6rem 0.8rem; }
                .input-group-custom .form-control { padding-left: 2.5rem; }
                .input-group-custom .input-icon { left: 0.8rem; }
                .btn-lg { font-size: 1rem; padding: 0.6rem 1rem; }
            }
        </style>

        <div class="login-card-container">
            <div class="card login-card">
                <div class="card-header card-header-custom login-header">
                    <img src="{{ asset('img/logoportal_ee.png') }}" alt="Símbolo EE" class="login-logo">
                    <h4 style="margin-bottom:0;">Autenticação – Encarregado de Educação</h4>
                </div>

                <div class="card-body login-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif


                    <form method="POST" action="{{ route('ee.sendCode') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label visually-hidden">Endereço de Email</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control form-control-lg"
                                       placeholder="exemplo@dominio.pt" value="{{ old('email') }}" required autofocus
                                       aria-describedby="emailHelp" autocomplete="email">
                            </div>
                            <small id="emailHelp" class="form-text text-muted mt-2">
                                Será enviado um código para este email.
                            </small>
                        </div>

                        <div class="h-captcha-container">
                           <div class="h-captcha" data-sitekey="{{ config('services.hcaptcha.sitekey') }}"></div>

                        </div>

                        @error('h-captcha-response')
                            <div class="alert alert-danger mt-2 w-100" role="alert">
                                {{ $message }}
                            </div>
                        @enderror

                        <button type="submit" class="btn btn-primary-custom btn-lg w-100 mt-3" id="submitBtn">
                            <span id="submitText">Receber Código</span>
                            <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form[action="{{ route('ee.sendCode') }}"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function () {
                    const btn = document.getElementById('submitBtn');
                    if (btn) {
                        btn.disabled = true;
                        const submitText = document.getElementById('submitText');
                        if (submitText) submitText.textContent = 'A enviar...';
                        
                        const spinner = document.getElementById('spinner');
                        if (spinner) spinner.classList.remove('d-none');
                    }
                });
            }
        });
        </script>
        <script src="https://hcaptcha.com/1/api.js" async defer></script>

    </section>
@endsection