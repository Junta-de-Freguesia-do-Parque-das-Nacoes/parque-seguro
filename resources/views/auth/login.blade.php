@extends('layouts/basic')

{{-- Conteúdo da página --}}
@section('content')

    <form role="form" action="{{ url('/login') }}" method="POST" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

    <div class="container">
        <!-- Mensagem de aviso -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="alert alert-warning text-center">
                    <strong>Aviso:</strong> A leitura dos QR Codes e o acesso a este sistema são exclusivos para os serviços da Junta de Freguesia do Parque das Nações (JFPN). Caso não pertença aos serviços autorizados, o acesso não é permitido.
                </div>
            </div>
        </div>

        <!-- Hack para evitar preenchimento automático do Chrome -->
        <input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" aria-hidden="true">
        <input type="password" name="password_fake" id="password_fake" value="" style="display:none;" aria-hidden="true">

        <div class="container">
            <div class="row">

                <div class="col-md-4 col-md-offset-4">

                    @if (($snipeSettings->google_login=='1') && ($snipeSettings->google_client_id!='') && ($snipeSettings->google_client_secret!=''))
                        <br><br>
                        <a href="{{ route('google.redirect')  }}" class="btn btn-block btn-social btn-google btn-lg">
                            <i class="fa-brands fa-google"></i>
                            Iniciar sessão com o Google
                        </a>

                        <div class="separator">{{ strtoupper('ou') }}</div>
                    @endif

                    <div class="box login-box">
                        <div class="box-header with-border">
                            <h1 class="box-title"> Iniciar Sessão</h1>
                        </div>

                        <div class="login-box-body">
                            <div class="row">

                                @if ($snipeSettings->login_note)
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            {!!  Helper::parseEscapedMarkedown($snipeSettings->login_note)  !!}
                                        </div>
                                    </div>
                                @endif

                                <!-- Notificações -->
                                @include('notifications')

                                @if (!config('app.require_saml'))
                                    <div class="col-md-12">
                                        <fieldset>

                                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                                <label for="username"><i class="fas fa-user" aria-hidden="true"></i> Nome de Utilizador</label>
                                                <input class="form-control" placeholder="Nome de Utilizador" name="username" type="text" id="username" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}" autofocus>
                                                {!! $errors->first('username', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                            </div>

                                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label for="password"><i class="fa fa-key" aria-hidden="true"></i> Palavra-passe</label>
                                                <input class="form-control" placeholder="Palavra-passe" name="password" type="password" id="password" autocomplete="{{ (config('auth.login_autocomplete') === true) ? 'on' : 'off'  }}">
                                                {!! $errors->first('password', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                            </div>

                                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label class="form-control">
                                                    <input name="remember" type="checkbox" value="1"> Lembrar-me
                                                </label>
                                            </div>
                                        </fieldset>
                                    </div> <!-- end col-md-12 -->
                                @endif
                            </div> <!-- end row -->

                            @if (!config('app.require_saml') && $snipeSettings->saml_enabled)
                                <div class="row">
                                    <div class="text-right col-md-12">
                                        <a href="{{ route('saml.login')  }}">Iniciar sessão com SAML</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="box-footer">
                            @if (config('app.require_saml'))
                                <a class="btn btn-primary btn-block" href="{{ route('saml.login')  }}">Iniciar sessão com SAML</a>
                            @else
                                <button class="btn btn-primary btn-block">Iniciar Sessão</button>
                            @endif

                            @if ($snipeSettings->custom_forgot_pass_url)
                            <div class="col-md-12 text-right" style="padding-top: 15px;">
    <a href="{{ route('password.request') }}" rel="noopener"></a>
    
    
</div>



                            @elseif (!config('app.require_saml'))
                                <div class="col-md-12 text-right" style="padding-top: 15px;">
                                    <a href="{{ route('password.request')  }}">Esqueceu-se da palavra-passe?</a>
                                </div>
                            @endif

                        </div>

                    </div> <!-- end login box -->

                </div> <!-- col-md-4 -->

            </div> <!-- end row -->
        </div> <!-- end container -->
    </form>

    <script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.Capacitor) {
      const googleBtn = document.querySelector('.btn-google');
      if (googleBtn) {
        googleBtn.style.display = 'none';
      }

      const separator = document.querySelector('.separator');
      if (separator) {
        separator.style.display = 'none';
      }
    }
  });
</script>

@stop
