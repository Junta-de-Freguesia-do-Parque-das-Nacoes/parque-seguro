@extends('layouts/default')

{{-- Título da Página --}}
@section('title')
Olá, {{ $user->present()->getFullNameAttribute() }}
@parent
@stop

{{-- Conteúdo da Página da Conta --}}
@section('content')

@if ($acceptances = \App\Models\CheckoutAcceptance::forUser(Auth::user())->pending()->count())
  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-warning fade in">
        <i class="fas fa-exclamation-triangle faa-pulse animated"></i>
        <strong>
          <a href="{{ route('account.accept') }}" style="color: white;">
            Tem {{ $acceptances }} aceitação(ões) pendente(s) no seu perfil.
          </a>
        </strong>
      </div>
    </div>
  </div>
@endif

<div class="row">
  <div class="col-md-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs hidden-print">
        <li class="active">
          <a href="#details" data-toggle="tab">
            <span class="hidden-lg hidden-md">
              <i class="fas fa-info-circle fa-2x"></i>
            </span>
            <span class="hidden-xs hidden-sm">Informações</span>
          </a>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="details">
          <div class="row">
            <!-- Coluna de Botões -->
            <div class="col-md-3 col-xs-12 col-sm-push-9">
              <div class="col-md-12 text-center">
                <img src="{{ $user->present()->gravatar() }}" class="img-thumbnail hidden-print" style="margin-bottom: 20px;" alt="{{ $user->present()->fullName() }}">
              </div>
              @can('self.profile')
                <div class="col-md-12">
                  <a href="{{ route('profile') }}" style="width: 100%;" class="btn btn-sm btn-primary hidden-print">
                    Editar Perfil
                  </a>
                </div>
              @endcan
            </div>
            <!-- Fim da Coluna de Botões -->

            <div class="col-md-9 col-xs-12 col-sm-pull-3">
              <div class="row-new-striped">
                <div class="row">
                  <div class="col-md-3 col-sm-2">Nome</div>
                  <div class="col-md-9 col-sm-2">{{ $user->present()->fullName() }}</div>
                </div>

                @if (!is_null($user->company))
                <div class="row">
                  <div class="col-md-3">Empresa</div>
                  <div class="col-md-9">{{ $user->company->name }}</div>
                </div>
                @endif

                <div class="row">
                  <div class="col-md-3">Nome de Utilizador</div>
                  <div class="col-md-9">
                    @if ($user->isSuperUser())
                      <label class="label label-danger"><i class="fas fa-crown" title="Super Administrador"></i></label>&nbsp;
                    @elseif ($user->hasAccess('admin'))
                      <label class="label label-warning"><i class="fas fa-crown" title="Administrador"></i></label>&nbsp;
                    @endif
                    {{ $user->username }}
                  </div>
                </div>

                @if ($user->email)
                <div class="row">
                  <div class="col-md-3">Email</div>
                  <div class="col-md-9">
                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                  </div>
                </div>
                @endif

                @if ($user->phone)
                <div class="row">
                  <div class="col-md-3">Telefone</div>
                  <div class="col-md-9">
                    <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                  </div>
                </div>
                @endif

                <div class="row">
                  <div class="col-md-3">Último Login</div>
                  <div class="col-md-9">{{ \App\Helpers\Helper::getFormattedDateObject($user->last_login, 'datetime', false) }}</div>
                </div>

                @if ($user->created_at)
                <div class="row">
                  <div class="col-md-3">Criado em</div>
                  <div class="col-md-9">{{ \App\Helpers\Helper::getFormattedDateObject($user->created_at, 'datetime')['formatted']}}</div>
                </div>
                @endif

              </div> <!--/end striped container-->
            </div> <!-- end col-md-9 -->
          </div> <!--/.row-->
        </div><!-- /.tab-pane -->
      </div><!-- /.tab-content -->
    </div><!-- nav-tabs-custom -->
  </div>
</div>
@stop

@section('moar_scripts')
  @include ('partials.bootstrap-table')
@stop
