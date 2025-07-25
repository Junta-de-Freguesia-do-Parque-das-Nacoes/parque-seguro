@extends('layouts/basic')

{{-- Conteúdo da página --}}
@section('content')

    @if ($snipeSettings->custom_forgot_pass_url)
        <!-- As configurações do administrador especificam um URL de redefinição de palavra-passe LDAP, então redirecionamos para lá -->
        <div class="col-md-4 col-md-offset-4" style="margin-top: 20px;">
            <div class="box box-header text-center">
                <h3 class="box-title">
                    <a href="{{ $snipeSettings->custom_forgot_pass_url  }}" rel="noopener">
                        Redefinir palavra-passe via LDAP
                    </a>
                </h3>
            </div>
        </div>

    @else

    <form class="form" role="form" method="POST" action="{{ url('/password/email') }}">
        {!! csrf_field() !!}
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="box login-box" style="width: 100%">
                        <div class="box-header with-border">
                            <h2 class="box-title">Enviar link para redefinir palavra-passe</h2>
                        </div>

                        <div class="login-box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                                        Introduza o seu nome de utilizador para receber um link de redefinição de palavra-passe.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Notificações -->
                                @include('notifications')

                                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                    <div class="col-md-12">
                                        <label for="username"><i class="fas fa-user" aria-hidden="true"></i> Nome de utilizador</label>
                                        <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Nome de utilizador" aria-label="username">
                                        {!! $errors->first('username', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <br>
                                    <!-- Mostrar ajuda -->
                                    <a href="#" id="show">
                                        <i class="fa fa-caret-right"></i>
                                        Mostrar ajuda
                                    </a>

                                    <!-- Ocultar ajuda -->
                                    <a href="#" id="hide" style="display:none">
                                        <i class="fa fa-caret-up"></i>
                                        Ocultar ajuda
                                    </a>

                                    <!-- Texto de ajuda -->
                                    <p class="help-block" id="help-text" style="display:none">
                                        Se tiver dificuldades em redefinir a sua palavra-passe, entre em contacto com o suporte técnico.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-lg btn-primary btn-block">
                                Enviar e-mail para redefinir palavra-passe
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    @endif
@stop

@push('js')
    <script nonce="{{ csrf_token() }}">
        $(document).ready(function () {
            $("#show").click(function(){
                $("#help-text").fadeIn(500);
                $("#show").hide();
                $("#hide").show();
            });

            $("#hide").click(function(){
                $("#help-text").fadeOut(300);
                $("#show").show();
                $("#hide").hide();
            });
        });
    </script>
@endpush
