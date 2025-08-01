<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
dir="{{ Helper::determineLanguageDirection() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
        @show
        :: {{ $snipeSettings->site_name }}
    </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <meta name="apple-mobile-web-app-capable" content="yes">


    <link rel="apple-touch-icon"
          href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->logo)) :  config('app.url').'/img/snipe-logo-bug.png' }}">
    <link rel="apple-touch-startup-image"
          href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->logo)) :  config('app.url').'/img/snipe-logo-bug.png' }}">
    <link rel="shortcut icon" type="image/ico"
          href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }} ">


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="language" content="{{ Helper::mapBackToLegacyLocale(app()->getLocale()) }}">
    <meta name="language-direction" content="{{ Helper::determineLanguageDirection() }}">
    <meta name="baseUrl" content="{{ config('app.url') }}/">

    <script nonce="{{ csrf_token() }}">
        window.Laravel = {csrfToken: '{{ csrf_token() }}'};
    </script>

    {{-- stylesheets --}}
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">
    @if (($snipeSettings) && ($snipeSettings->allow_user_skin==1) && Auth::check() && Auth::user()->present()->skin != '')
        <link rel="stylesheet" href="{{ url(mix('css/dist/skins/skin-'.Auth::user()->present()->skin.'.min.css')) }}">
    @else
        <link rel="stylesheet"
              href="{{ url(mix('css/dist/skins/skin-'.($snipeSettings->skin!='' ? $snipeSettings->skin : 'blue').'.css')) }}">
    @endif
    {{-- page level css --}}
    @stack('css')



    @if (($snipeSettings) && ($snipeSettings->header_color!=''))
        <style nonce="{{ csrf_token() }}">
            .main-header .navbar, .main-header .logo {
                background-color: {{ $snipeSettings->header_color }};
                background: -webkit-linear-gradient(top,  {{ $snipeSettings->header_color }} 0%,{{ $snipeSettings->header_color }} 100%);
                background: linear-gradient(to bottom, {{ $snipeSettings->header_color }} 0%,{{ $snipeSettings->header_color }} 100%);
                border-color: {{ $snipeSettings->header_color }};
            }

            .skin-{{ $snipeSettings->skin!='' ? $snipeSettings->skin : 'blue' }} .sidebar-menu > li:hover > a, .skin-{{ $snipeSettings->skin!='' ? $snipeSettings->skin : 'blue' }} .sidebar-menu > li.active > a {
                border-left-color: {{ $snipeSettings->header_color }};
            }

            .btn-primary {
                background-color: {{ $snipeSettings->header_color }};
                border-color: {{ $snipeSettings->header_color }};
            }
        </style>
    @endif

    {{-- Custom CSS --}}
    @if (($snipeSettings) && ($snipeSettings->custom_css))
        <style>
            {!! $snipeSettings->show_custom_css() !!}
        </style>
    @endif


    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": {{ $snipeSettings->per_page }}
            }
        };
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <script src="{{ url(asset('js/html5shiv.js')) }}" nonce="{{ csrf_token() }}"></script>
    <script src="{{ url(asset('js/respond.js')) }}" nonce="{{ csrf_token() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>



</head>

@if (($snipeSettings) && ($snipeSettings->allow_user_skin==1) && Auth::check() && Auth::user()->present()->skin != '')
    <body class="sidebar-mini skin-{{ $snipeSettings->skin!='' ? Auth::user()->present()->skin : 'blue' }} {{ (session('menu_state')!='open') ? 'sidebar-mini sidebar-collapse' : ''  }}">
    @else
        <body class="sidebar-mini skin-{{ $snipeSettings->skin!='' ? $snipeSettings->skin : 'blue' }} {{ (session('menu_state')!='open') ? 'sidebar-mini sidebar-collapse' : ''  }}">
        @endif


        <a class="skip-main" href="#main">{{ trans('general.skip_to_main_content') }}</a>
        <div class="wrapper">

            <header class="main-header">

                <!-- Logo -->


                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button above the compact sidenav -->
                    <a href="#" style="color: white" class="sidebar-toggle btn btn-white" data-toggle="push-menu"
                       role="button">
                        <span class="sr-only">{{ trans('general.toggle_navigation') }}</span>
                    </a>
                    <div class="nav navbar-nav navbar-left">
                        <div class="left-navblock">
                            @if ($snipeSettings->brand == '3')
                                <a class="logo navbar-brand no-hover" href="{{ config('app.url') }}">
                                    @if ($snipeSettings->logo!='')
                                        <img class="navbar-brand-img"
                                             src="{{ Storage::disk('public')->url($snipeSettings->logo) }}"
                                             alt="{{ $snipeSettings->site_name }} logo">
                                    @endif
                                    {{ $snipeSettings->site_name }}
                                </a>
                            @elseif ($snipeSettings->brand == '2')
                                <a class="logo navbar-brand no-hover" href="{{ config('app.url') }}">
                                    @if ($snipeSettings->logo!='')
                                        <img class="navbar-brand-img"
                                             src="{{ Storage::disk('public')->url($snipeSettings->logo) }}"
                                             alt="{{ $snipeSettings->site_name }} logo">
                                    @endif
                                    <span class="sr-only">{{ $snipeSettings->site_name }}</span>
                                </a>
                            @else
                                <a class="logo navbar-brand no-hover" href="{{ config('app.url') }}">
                                    {{ $snipeSettings->site_name }}
                                </a>
                            @endif
                        </div>
                    </div>


             
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            @can('index', \App\Models\Asset::class)
							

                            @php
    $userGroups = Auth::user()->groups()->pluck('name')->toArray();
@endphp

@if (!in_array('monitores', $userGroups))                    
                            <li aria-hidden="true">
							 <li aria-hidden="true"{!! (Request::is('hardware*') ? ' class="active"' : '') !!}>
                                    <a href="{{ url('hardware') }}" accesskey="1" tabindex="-1" title="Utentes">
                                        <i class="fas fa-children fa-fw"></i>
                                        <span class="sr-only">{{ trans('general.assets') }}</span>
                                    </a>
                                </li>
						

                                <li aria-hidden="true" {!! (Request::is('responsaveis*') ? 'class="active"' : '') !!}>
    <a href="{{ route('responsaveis.listar') }}" accesskey="9" tabindex="-1" title="Lista de Responsáveis">
        <i class="fas fa-users fa-fw"></i> <!-- Ícone de Responsáveis -->
        <span class="sr-only">Responsáveis</span>
    </a>
</li>



<li aria-hidden="true"{!! (Request::is('presencas-ausencias*') ? ' class="active"' : '') !!}>
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/presencas-ausencias" accesskey="8" tabindex="-1" title="Presenças e Ausências">
        <i class="fas fa-user-check fa-fw"></i> <!-- Ícone de Presenças e Ausências -->
        <span class="sr-only">Presenças e Ausências</span>
       
    </a>
</li>

<li aria-hidden="true"{!! (Request::is('historico*') ? ' class="active"' : '') !!}>
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/historico" accesskey="7" tabindex="-1" title="Histórico Entradas/Saídas">
        <i class="fas fa-history fa-fw"></i> <!-- Ícone de Histórico -->
        <span class="sr-only">Histórico</span>
        
    </a>
</li>


@endif

<li aria-hidden="true"{!! (Request::is('hardware/maintenances*') ? ' class="active"' : '') !!}>
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/hardware/maintenances" accesskey="8" tabindex="-1" title="Incidentes">
        <i class="fas fa-exclamation-triangle fa-fw"></i> <!-- Ícone de Triângulo de Alerta -->
        <span class="sr-only">Incidentes</span>
        
    </a>
</li>
<li aria-hidden="true"{!! (Request::is('app-download*') ? ' class="active"' : '') !!}>
    <a href="{{ route('app-download') }}" 
       accesskey="9" 
       tabindex="-1" 
       title="Aplicação Parque Seguro">
        <i class="fas fa-mobile-alt fa-fw"></i> <!-- Ícone de telemóvel -->
        <span class="sr-only">Aplicação Parque Seguro</span>
    </a>
</li>
@php
    $userGroups = Auth::user()->groups()->pluck('name')->toArray();
@endphp

@if (!in_array('monitores', $userGroups))
<li aria-hidden="true"{!! (Request::is('email-logs*') ? ' class="active"' : '') !!}>
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/email-logs" accesskey="9" tabindex="-1" title="Logs de Emails">
        <i class="fas fa-envelope fa-fw"></i> <!-- Ícone de envelope -->
        <span class="sr-only">Logs de Emails</span>
        
    </a>
</li>



<!--

<li aria-hidden="true"{!! (Request::is('bulk-send-modal*') ? ' class="active"' : '') !!}>
    <a href="#" 
       data-toggle="modal" 
       data-target="#bulkSendModal" 
       accesskey="9" 
       tabindex="-1" 
       title="Enviar Emails em Massa">
        <i class="fas fa-envelope-open-text fa-fw"></i> 
        <span class="sr-only">Enviar Emails em Massa</span>
        
    </a>
</li>
    
-->

<li aria-hidden="true"{!! (Request::is('send.bulk.qr*') ? ' class="active"' : '') !!}>
   <a href="#" 
   onclick="abrirEnvioQrCode(event)" 
   accesskey="9" 
   tabindex="-1" 
   title="Enviar QR Codes para Selecionados">
   <i class="fas fa-envelope-open-text fa-fw"></i> 
   
</a>
</li>


<li aria-hidden="true"{!! (Request::is('programas/gestao*') ? ' class="active"' : '') !!}>
    <a href="{{ url('programas/gestao') }}" 
       accesskey="9" 
       title="Gestão de Inscrições por Programa">
        <i class="fas fa-calendar-check fa-fw"></i> <!-- Ícone de calendário com check -->
        <span class="sr-only">Gestão de Inscrições por Programa</span>
    </a>
</li>

<li aria-hidden="true" {!! (Request::is('mapa-presencas*') ? ' class="active"' : '') !!}>
    <a href="{{ url('mapa-presencas') }}"
       title="Mapa de Presenças por Utente">
        <i class="fas fa-th-list fa-fw"></i>
        
    </a>
</li>



@php
    $notificacoesNaoLidas = Auth::check() ? Auth::user()->notificacoes()->wherePivot('lida', 0)->count() : 0;
@endphp



<li class="dropdown notifications-menu" id="notificacoes-dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Notificações">
        <i class="fas fa-bell"></i>
       @if ($notificacoesNaoLidas > 0)
    <span class="label label-warning" id="notificacoes-count">{{ $notificacoesNaoLidas }}</span>
@endif

    </a>
    <ul class="dropdown-menu">
        <li class="header">Notificações recentes</li>
        <li>
            <!-- Inner Menu -->
            <ul class="menu" id="notificacoes-lista">
                <li><a href="#">A carregar...</a></li>
            </ul>
        </li>
        <li class="footer text-center">
            <a href="{{ route('notificacoes.index') }}">Ver todas</a>
        </li>
    </ul>
</li>


                               
@endif
                               
                            @endcan
                            @can('view', \App\Models\License::class)
                                <li aria-hidden="true"{!! (Request::is('licenses*') ? ' class="active"' : '') !!}>
                                    <a href="{{ route('licenses.index') }}" accesskey="2" tabindex="-1">
                                        <i class="fas fa-scroll"></i>
                                        <span class="sr-only">{{ trans('general.licenses') }}</span>
                                    </a>
                                </li>
								
                            @endcan
                            @can('index', \App\Models\Accessory::class)
                                <li aria-hidden="true"{!! (Request::is('accessories*') ? ' class="active"' : '') !!}>
                                    <a href="{{ route('accessories.index') }}" accesskey="3" tabindex="-1">
                                        <i class="fas fa-puzzle-piece"></i>
                                        <span class="sr-only">{{ trans('general.accessories') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('index', \App\Models\Consumable::class)
                                <li aria-hidden="true"{!! (Request::is('consumables*') ? ' class="active"' : '') !!}>
                                    <a href="{{ url('consumables') }}" accesskey="4" tabindex="-1">
                                        <i class="fas fa-box fa-fw"></i>
                                        <span class="sr-only">{{ trans('general.consumables') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view', \App\Models\Component::class)
                                <li aria-hidden="true"{!! (Request::is('components*') ? ' class="active"' : '') !!}>
                                    <a href="{{ route('components.index') }}" accesskey="5" tabindex="-1">
                                        <i class="fas fa-tools fa-fw"></i>
                                        <span class="sr-only">{{ trans('general.components') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('index', \App\Models\Asset::class)
                                
                            @endcan

                            @can('admin')
                                <li class="dropdown" aria-hidden="true">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                                        {{ trans('general.create') }}
                                        <strong class="caret"></strong>
                                    </a>
                                    <ul class="dropdown-menu">
                                        @can('create', \App\Models\Asset::class)
                                            <li {!! (Request::is('hardware/create') ? 'class="active>"' : '') !!}>
                                                <a href="{{ route('hardware.create') }}" tabindex="-1">
                                                    <i class="fas fa-children fa-fw" aria-hidden="true"></i>
                                                    {{ trans('general.asset') }}
                                                </a>
                                            </li>
                                        @endcan
                                      
                                        @if (!in_array('monitores', $userGroups))
                                      
                                        @can('create', \App\Models\Responsavel::class)
   
                                        <li {!! (Request::is('responsaveis/create') ? 'class="active"' : '') !!}>
        <a href="{{ route('responsaveis.create') }}" tabindex="-1">
            <i class="fas fa-user-plus fa-fw" aria-hidden="true"></i>
            Responsável
        </a>
    </li>
@endcan
@endif

                                        @can('create', \App\Models\License::class)
                                            <li {!! (Request::is('licenses/create') ? 'class="active"' : '') !!}>
                                                <a href="{{ route('licenses.create') }}" tabindex="-1">
                                                    <i class="fas fa-scroll" aria-hidden="true"></i>
                                                    {{ trans('general.license') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('create', \App\Models\Accessory::class)
                                            <li {!! (Request::is('accessories/create') ? 'class="active"' : '') !!}>
                                                <a href="{{ route('accessories.create') }}" tabindex="-1">
                                                    <i class="fas fa-puzzle-piece" aria-hidden="true"></i>
                                                    {{ trans('general.accessory') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('create', \App\Models\Consumable::class)
                                            <li {!! (Request::is('consunmables/create') ? 'class="active"' : '') !!}>
                                                <a href="{{ route('consumables.create') }}" tabindex="-1">
                                                    <i class="fas fa-box fa-fw" aria-hidden="true"></i>
                                                    {{ trans('general.consumable') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('create', \App\Models\Component::class)
                                            <li {!! (Request::is('components/create') ? 'class="active"' : '') !!}>
                                                <a href="{{ route('components.create') }}" tabindex="-1">
                                                    <i class="fas fa-tools fa-fw" aria-hidden="true"></i>
                                                    {{ trans('general.component') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('create', \App\Models\User::class)
                                            <li {!! (Request::is('users/create') ? 'class="active"' : '') !!}>
                                                <a href="{{ route('users.create') }}" tabindex="-1">
                                                    <i class="fas fa-user fa-fw" aria-hidden="true"></i>
                                                    {{ trans('general.user') }}
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan

                            <li aria-hidden="true">
									<a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/qr-code-scanner" accesskey="6" tabindex="-1" title="Scan QrCode">
										<i class="fas fa-qrcode fa-fw"></i> <!-- Ícone de QR Code -->
										<span class="sr-only">Leitor QR Code</span>
									</a>
								</li>

                            @can('admin')
                                @if ($snipeSettings->show_alerts_in_menu=='1')
                                    <!-- Tasks: style can be found in dropdown.less -->
                                    <?php $alert_items = Helper::checkLowInventory(); ?>

                                    <li class="dropdown tasks-menu">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="far fa-flag" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('general.alerts') }}</span>
                                            @if (count($alert_items))
                                                <span class="label label-danger">{{ count($alert_items) }}</span>
                                            @endif
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class="header">{{ trans('general.quantity_minimum', array('count' => count($alert_items))) }}</li>
                                            <li>
                                                <!-- inner menu: contains the actual data -->
                                                <ul class="menu">

                                                    @for($i = 0; count($alert_items) > $i; $i++)

                                                        <li><!-- Task item -->
                                                            <a href="{{route($alert_items[$i]['type'].'.show', $alert_items[$i]['id'])}}">
                                                                <h2 class="task_menu">{{ $alert_items[$i]['name'] }}
                                                                    <small class="pull-right">
                                                                        {{ $alert_items[$i]['remaining'] }} {{ trans('general.remaining') }}
                                                                    </small>
                                                                </h2>
                                                                <div class="progress xs">
                                                                    <div class="progress-bar progress-bar-yellow"
                                                                         style="width: {{ $alert_items[$i]['percent'] }}%"
                                                                         role="progressbar"
                                                                         aria-valuenow="{{ $alert_items[$i]['percent'] }}"
                                                                         aria-valuemin="0" aria-valuemax="100">
                                                                        <span class="sr-only">{{ $alert_items[$i]['percent'] }}% Complete</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <!-- end task item -->
                                                    @endfor
                                                </ul>
                                            </li>
                                            {{-- <li class="footer">
                                              <a href="#">{{ trans('general.tasks_view_all') }}</a>
                                            </li> --}}
                                        </ul>
                                    </li>
                                @endcan
                            @endif



                            <!-- User Account: style can be found in dropdown.less -->
                            @if (Auth::check())
                                <li class="dropdown user user-menu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        @if (Auth::user()->present()->gravatar())
                                            <img src="{{ Auth::user()->present()->gravatar() }}" class="user-image"
                                                 alt="">
                                        @else
                                            <i class="fas fa-user" aria-hidden="true"></i>
                                        @endif

                                        <span class="hidden-xs">{{ Auth::user()->getFullNameAttribute() }} <strong
                                                    class="caret"></strong></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                       
                                       

                                       
                                       

                                        


                                        @can('self.api')
                                            <li>
                                                <a href="{{ route('user.api') }}">
                                                    <i class="fa-solid fa-user-secret fa-fw"
                                                       aria-hidden="true"></i></i> {{ trans('general.manage_api_keys') }}
                                                </a>
                                            </li>
                                        @endcan
                                        <li class="divider" style="margin-top: -1px; margin-bottom: -1px"></li>
                                        <li>

                                            <a href="{{ route('logout.get') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="fa fa-sign-out fa-fw"></i> {{ trans('general.logout') }}
                                            </a>

                                            <form id="logout-form" action="{{ route('logout.post') }}" method="POST"
                                                  style="display: none;">
                                                {{ csrf_field() }}
                                            </form>

                                        </li>
                                    </ul>
                                </li>
                            @endif


                            @can('superadmin')
                                <li>
                                    <a href="{{ route('settings.index') }}">
                                        <i class="fa fa-cogs fa-fw" aria-hidden="true"></i>
                                        <span class="sr-only">{{ trans('general.admin') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </nav>
                <a href="#" style="float:left" class="sidebar-toggle-mobile visible-xs btn" data-toggle="push-menu"
                   role="button">
                    <span class="sr-only">{{ trans('general.toggle_navigation') }}</span>
                    <i class="fas fa-bars"></i>
                </a>
                <!-- Sidebar toggle button-->
            </header>

            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu" data-widget="tree">
                        @can('admin')
                            <li {!! (\Request::route()->getName()=='home' ? ' class="active"' : '') !!} class="firstnav">
                                <a href="{{ route('home') }}">
                                    <i class="fas fa-tachometer-alt fa-fw" aria-hidden="true"></i>
                                    <span>{{ trans('general.dashboard') }}</span>
                                </a>
                            </li>
                        @endcan

                        @auth
                                @php
    $userGroups = Auth::user()->groups()->pluck('name')->toArray();
@endphp

@if (!in_array('monitores', $userGroups))

                        @can('index', \App\Models\Asset::class)
                            <li class="treeview{{ ((Request::is('statuslabels/*') || Request::is('hardware*')) ? ' active' : '') }}">
                                <a href="#"><i class="fas fa-children fa-fw" aria-hidden="true"></i>
                                    <span>{{ trans('general.assets') }}</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>

                               
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="{{ url('hardware') }}">
                                            <i class="far fa-circle text-grey fa-fw" aria-hidden="true"></i>
                                            {{ trans('general.list_all') }}
                                            <span class="badge">
                                                {{ (isset($total_assets)) ? $total_assets : '' }}
                                            </span>
                                        </a>
                                    </li>
									
									
									

                                    <?php $status_navs = \App\Models\Statuslabel::where('show_in_nav', '=', 1)->withCount('assets as asset_count')->get(); ?>
                                    @if (count($status_navs) > 0)
                                        @foreach ($status_navs as $status_nav)
                                            <li{!! (Request::is('statuslabels/'.$status_nav->id) ? ' class="active"' : '') !!}>
                                                <a href="{{ route('statuslabels.show', ['statuslabel' => $status_nav->id]) }}">
                                                    <i class="fas fa-circle text-grey fa-fw"
                                                       aria-hidden="true"{!!  ($status_nav->color!='' ? ' style="color: '.e($status_nav->color).'"' : '') !!}></i>
                                                    {{ $status_nav->name }}
                                                    <span class="badge badge-secondary">{{ $status_nav->asset_count }}</span></a></li>
                                        @endforeach
                                    @endif


                                    
                                    
                                    
                                    <li{!! (Request::query('status') == 'byod' ? ' class="active"' : '') !!}><a
                                                href="{{ url('hardware?status=byod') }}"><i
                                                    class="fas fa-times text-red fa-fw"></i>
                                            {{ trans('general.byod') }}
                                            <span class="badge">{{ (isset($total_byod_sidebar)) ? $total_byod_sidebar : '' }}</span>
                                        </a>
                                    </li>
                                    <li{!! (Request::query('status') == 'Archived' ? ' class="active"' : '') !!}><a
                                                href="{{ url('hardware?status=Archived') }}"><i
                                                    class="fas fa-times text-red fa-fw"></i>
                                            {{ trans('admin/hardware/general.archived') }}
                                            <span class="badge">{{ (isset($total_archived_sidebar)) ? $total_archived_sidebar : '' }}</span>
                                        </a>
                                    </li>
                                    

                                    @can('audit', \App\Models\Asset::class)
                                        <li{!! (Request::is('hardware/audit/due') ? ' class="active"' : '') !!}>
                                            <a href="{{ route('assets.audit.due') }}">
                                                <i class="fas fa-history text-yellow fa-fw"></i> {{ trans('general.audit_due') }}
                                                <span class="badge">{{ (isset($total_due_and_overdue_for_audit)) ? $total_due_and_overdue_for_audit : '' }}</span>
                                            </a>
                                        </li>
                                    @endcan

                                    

                                    

                                    @can('create', \App\Models\Asset::class)
                                        <li{!! (Request::query('Deleted') ? ' class="active"' : '') !!}>
                                            <a href="{{ url('hardware?status=Deleted') }}">
                                                {{ trans('general.deleted') }}
                                            </a>
                                        </li>
                                        
                                    @endcan
                                    @can('admin')
                                        <li>
                                            <a href="{{ url('hardware/history') }}">
                                                {{ trans('general.import-history') }}
                                            </a>
                                        </li>
                                    @endcan
                                  
                                </ul>
                            </li>
                        @endcan
						


<li aria-hidden="true" {!! (Request::is('responsaveis*') ? 'class="active"' : '') !!}>
    <a href="{{ url('responsaveis') }}" accesskey="9" tabindex="-1" title="Lista de Responsáveis">
        <i class="fas fa-users fa-fw"></i> <!-- Ícone de Responsáveis -->
        <span class="sr-only">Responsáveis</span>
        <span>Responsáveis</span>
    </a>
</li>


									<li aria-hidden="true"{!! (Request::is('presencas-ausencias*') ? ' class="active"' : '') !!}>
    <a href="{{ url('presencas-ausencias') }}" accesskey="8" tabindex="-1" title="Presenças e Ausências">
        <i class="fas fa-user-check fa-fw"></i> <!-- Ícone de Presenças e Ausências -->
        <span class="sr-only">Presenças e Ausências</span>
        <span>Presenças e Ausências</span>
    </a>
</li>


    <li aria-hidden="true"{!! (Request::is('historico*') ? ' class="active"' : '') !!}>
    <a href="{{ url('historico') }}" accesskey="7" tabindex="-1" title="Histórico Entradas/Saídas">
        <i class="fas fa-history fa-fw"></i> <!-- Ícone de Histórico -->
        <span class="sr-only">Histórico</span>
        <span>Histórico de Entradas/Saídas</span>
    </a>
</li>
@endif

<li aria-hidden="true"{!! (Request::is('hardware/maintenances*') ? ' class="active"' : '') !!}>
    <a href="{{ url('hardware/maintenances') }}" accesskey="8" tabindex="-1" title="Incidentes">
        <i class="fas fa-exclamation-triangle fa-fw"></i> <!-- Ícone de Triângulo de Alerta -->
        <span class="sr-only">Incidentes</span>
        <span>Incidentes dos Utentes</span>
    </a>
</li>

@if (!in_array('monitores', $userGroups))
    <li aria-hidden="true"{!! (Request::is('email-logs*') ? ' class="active"' : '') !!}>
        <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/email-logs" accesskey="9" tabindex="-1" title="Logs de Emails">
            <i class="fas fa-envelope fa-fw"></i> <!-- Ícone de envelope -->
            <span class="sr-only">Logs de Emails</span>
            <span>Logs de Emails</span>
        </a>
    </li>

<!--
<li aria-hidden="true"{!! (Request::is('send.bulk.qr*') ? ' class="active"' : '') !!}>
    <a href="#" 
       data-toggle="modal" 
       data-target="#bulkSendModal" 
       accesskey="9" 
       tabindex="-1" 
       title="Enviar Emails em Massa">
        <i class="fas fa-envelope-open-text fa-fw"></i> 
        <span>Enviar Email em Massa de QR_Code</span>
    </a>
</li>
 -->
 
<li aria-hidden="true"{!! (Request::is('send.bulk.qr*') ? ' class="active"' : '') !!}>
    <a href="#" 
   onclick="abrirEnvioQrCode(event)" 
   accesskey="9" 
   tabindex="-1" 
   title="Enviar QR Codes para Selecionados">
   <i class="fas fa-envelope-open-text fa-fw"></i> 
   <span>Enviar QR Codes para Selecionados</span>
</a>
</li>

                                            

<li aria-hidden="true"{!! (Request::is('programas/gestao*') ? ' class="active"' : '') !!}>
    <a href="{{ url('programas/gestao') }}" 
       accesskey="9" 
       title="Gestão de Inscrições por Programa">
        <i class="fas fa-calendar-check fa-fw"></i> <!-- Ícone de calendário com check -->
        <span>Gestão de Inscrições por Programa</span>
    </a>
</li>

<li aria-hidden="true" {!! (Request::is('mapa-presencas*') ? ' class="active"' : '') !!}>
    <a href="{{ url('mapa-presencas') }}"
       title="Mapa de Presenças por Utente">
        <i class="fas fa-th-list fa-fw"></i>
        <span>Mapa de Presenças</span>
    </a>
</li>


@endif

<li aria-hidden="true"{!! (Request::is('qr-code-scanner*') ? ' class="active"' : '') !!}>
    <a href="{{ url('qr-code-scanner') }}" accesskey="6" tabindex="-1" title="Leitor QR Code">
        <i class="fas fa-qrcode fa-fw"></i> <!-- Ícone de QR Code -->
        <span class="sr-only">Leitor QR Code</span>
        <span>Leitor QR Code</span>
    </a>
</li>
@endauth

								
					   @can('view', \App\Models\License::class)
                            <li{!! (Request::is('licenses*') ? ' class="active"' : '') !!}>
                                <a href="{{ route('licenses.index') }}">
                                    <i class="fas fa-scroll"></i>
                                    <span>{{ trans('general.licenses') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('index', \App\Models\Accessory::class)
                            <li{!! (Request::is('accessories*') ? ' class="active"' : '') !!}>
                                <a href="{{ route('accessories.index') }}">
                                    <i class="fas fa-puzzle-piece"></i>
                                    <span>{{ trans('general.accessories') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view', \App\Models\Consumable::class)
                            <li{!! (Request::is('consumables*') ? ' class="active"' : '') !!}>
                                <a href="{{ url('consumables') }}">
                                    <i class="fas fa-box fa-fw"></i>
                                    <span>{{ trans('general.consumables') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view', \App\Models\Component::class)
                            <li{!! (Request::is('components*') ? ' class="active"' : '') !!}>
                                <a href="{{ route('components.index') }}">
                                    <i class="fas fa-tools fa-fw"></i>
                                    <span>{{ trans('general.components') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view', \App\Models\PredefinedKit::class)
                            <li{!! (Request::is('kits') ? ' class="active"' : '') !!}>
                                <a href="{{ route('kits.index') }}">
                                    <i class="fa fa-object-group fa-fw"></i>
                                    <span>{{ trans('general.kits') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view', \App\Models\User::class)
                            <li{!! (Request::is('users*') ? ' class="active"' : '') !!}>
                                <a href="{{ route('users.index') }}" accesskey="6">
                                    <i class="fas fa-users fa-fw"></i>
                                    <span>{{ trans('general.people') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('import')
                            <li{!! (Request::is('import/*') ? ' class="active"' : '') !!}>
                                <a href="{{ route('imports.index') }}">
                                    <i class="fas fa-cloud-upload-alt fa-fw" aria-hidden="true"></i>
                                    <span>{{ trans('general.import') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('backend.interact')
                            <li class="treeview {!! in_array(Request::route()->getName(),App\Helpers\Helper::SettingUrls()) ? ' active': '' !!}">
                                <a href="#" id="settings">
                                    <i class="fas fa-cog" aria-hidden="true"></i>
                                    <span>{{ trans('general.settings') }}</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>

                                <ul class="treeview-menu">
                                    @if(Gate::allows('view', App\Models\CustomField::class) || Gate::allows('view', App\Models\CustomFieldset::class))
                                        <li {!! (Request::is('fields*') ? ' class="active"' : '') !!}>
                                            <a href="{{ route('fields.index') }}">
                                                {{ trans('admin/custom_fields/general.custom_fields') }}
                                            </a>
                                        </li>
                                    @endif

                                    @can('view', \App\Models\Statuslabel::class)
                                        <li {!! (Request::is('statuslabels*') ? ' class="active"' : '') !!}>
                                            <a href="{{ route('statuslabels.index') }}">
                                                {{ trans('general.status_labels') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\AssetModel::class)
                                        <li>
                                            <a href="{{ route('models.index') }}" {{ (Request::is('/assetmodels') ? ' class="active"' : '') }}>
                                                {{ trans('general.asset_models') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Category::class)
                                        <li>
                                            <a href="{{ route('categories.index') }}" {{ (Request::is('/categories') ? ' class="active"' : '') }}>
                                                {{ trans('general.categories') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Manufacturer::class)
                                        <li>
                                            <a href="{{ route('manufacturers.index') }}" {{ (Request::is('/manufacturers') ? ' class="active"' : '') }}>
                                                {{ trans('general.manufacturers') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Supplier::class)
                                        <li>
                                            <a href="{{ route('suppliers.index') }}" {{ (Request::is('/suppliers') ? ' class="active"' : '') }}>
                                                {{ trans('general.suppliers') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Department::class)
                                        <li>
                                            <a href="{{ route('departments.index') }}" {{ (Request::is('/departments') ? ' class="active"' : '') }}>
                                                {{ trans('general.departments') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Location::class)
                                        <li>
                                            <a href="{{ route('locations.index') }}" {{ (Request::is('/locations') ? ' class="active"' : '') }}>
                                                {{ trans('general.locations') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Company::class)
                                        <li>
                                            <a href="{{ route('companies.index') }}" {{ (Request::is('/companies') ? ' class="active"' : '') }}>
                                                {{ trans('general.companies') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('view', \App\Models\Depreciation::class)
                                        <li>
                                            <a href="{{ route('depreciations.index') }}" {{ (Request::is('/depreciations') ? ' class="active"' : '') }}>
                                                {{ trans('general.depreciation') }}
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan

                        @can('reports.view')
                            <li class="treeview{{ (Request::is('reports*') ? ' active' : '') }}">
                                <a href="#" class="dropdown-toggle">
                                    <i class="fas fa-chart-bar fa-fw"></i>
                                    <span>{{ trans('general.reports') }}</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>

                                <ul class="treeview-menu">
                                    <li>
                                        <a href="{{ route('reports.activity') }}" {{ (Request::is('reports/activity') ? ' class="active"' : '') }}>
                                            {{ trans('general.activity_report') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('reports/custom') }}" {{ (Request::is('reports/custom') ? ' class="active"' : '') }}>
                                            {{ trans('general.custom_report') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('reports.audit') }}" {{ (Request::is('reports.audit') ? ' class="active"' : '') }}>
                                            {{ trans('general.audit_report') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('reports/depreciation') }}" {{ (Request::is('reports/depreciation') ? ' class="active"' : '') }}>
                                            {{ trans('general.depreciation_report') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('reports/licenses') }}" {{ (Request::is('reports/licenses') ? ' class="active"' : '') }}>
                                            {{ trans('general.license_report') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('reports/asset_maintenances') }}" {{ (Request::is('reports/asset_maintenances') ? ' class="active"' : '') }}>
                                            {{ trans('general.asset_maintenance_report') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('reports/unaccepted_assets') }}" {{ (Request::is('reports/unaccepted_assets') ? ' class="active"' : '') }}>
                                            {{ trans('general.unaccepted_asset_report') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('reports/accessories') }}" {{ (Request::is('reports/accessories') ? ' class="active"' : '') }}>
                                            {{ trans('general.accessory_report') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endcan

                        @can('viewRequestable', \App\Models\Asset::class)
                            <li{!! (Request::is('account/requestable-assets') ? ' class="active"' : '') !!}>
                                <a href="{{ route('requestable-assets') }}">
                                    <i class="fa fa-laptop fa-fw"></i>
                                    <span>{{ trans('general.requestable_items') }}</span>
                                </a>
                            </li>
                        @endcan


                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->

            <div class="content-wrapper" role="main" id="setting-list">
                <barepay></barepay>

                @if ($debug_in_production)
                    <div class="row" style="margin-bottom: 0px; background-color: red; color: white; font-size: 15px;">
                        <div class="col-md-12"
                             style="margin-bottom: 0px; background-color: #b50408 ; color: white; padding: 10px 20px 10px 30px; font-size: 16px;">
                            <i class="fas fa-exclamation-triangle fa-3x pull-left"></i>
                            <strong>{{ strtoupper(trans('general.debug_warning')) }}:</strong>
                            {!! trans('general.debug_warning_text') !!}
                        </div>
                    </div>
                @endif

                <!-- Content Header (Page header) -->
                <section class="content-header" style="padding-bottom: 30px;">
                    <h1 class="pull-left pagetitle">@yield('title') </h1>

                    @if (isset($helpText))
                        @include ('partials.more-info',
                                               [
                                                   'helpText' => $helpText,
                                                   'helpPosition' => (isset($helpPosition)) ? $helpPosition : 'left'
                                               ])
                    @endif
                    <div class="pull-right">
                        @yield('header_right')
                    </div>


                </section>


                <section class="content" id="main" tabindex="-1">

                    <!-- Notifications -->
                    <div class="row">
                        @if (config('app.lock_passwords'))
                            <div class="col-md-12">
                                <div class="callout callout-info">
                                    {{ trans('general.some_features_disabled') }}
                                </div>
                            </div>
                        @endif

                        @include('notifications')
                    </div>


                    <!-- Content -->
                    <div id="{!! (Request::is('*api*') ? 'app' : 'webui') !!}">
                        @yield('content')
                    </div>

                </section>

            </div><!-- /.content-wrapper -->


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

                <p>O **código-fonte modificado** está publicamente disponível no nosso repositório GitHub: <a href="https://github.com/Junta-de-Freguesia-do-Parque-das-Nacoes/parque-seguro" target="_blank" rel="noopener noreferrer">github.com/Junta-de-Freguesia-do-Parque-das-Nacoes/parque-seguro</a>. Para mais informações, pode também contactar: <a href="mailto:nsi@jf-parquedasnacoes.pt">nsi@jf-parquedasnacoes.pt</a>.</p>

                <p class="text-muted"><small>Junta de Freguesia do Parque das Nações © 2016–{{ date('Y') }}. Todos os direitos reservados.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

        </div><!-- ./wrapper -->


        <!-- end main container -->

        <div class="modal modal-danger fade" id="dataConfirmModal" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h2 class="modal-title" id="myModalLabel">&nbsp;</h2>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <form method="post" id="deleteForm" role="form">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}

                            <button type="button" class="btn btn-default pull-left"
                                    data-dismiss="modal">{{ trans('general.cancel') }}</button>
                            <button type="submit" class="btn btn-outline"
                                    id="dataConfirmOK">{{ trans('general.yes') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal modal-warning fade" id="restoreConfirmModal" tabindex="-1" role="dialog"
             aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="confirmModalLabel">&nbsp;</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <form method="post" id="restoreForm" role="form">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}

                            <button type="button" class="btn btn-default pull-left"
                                    data-dismiss="modal">{{ trans('general.cancel') }}</button>
                            <button type="submit" class="btn btn-outline"
                                    id="dataConfirmOK">{{ trans('general.yes') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<!-- Modal -->
<div class="modal fade" id="bulkSendModal" tabindex="-1" role="dialog" aria-labelledby="bulkSendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('send.bulk.qr.filtered') }}">
            @csrf
            <input type="hidden" name="ids" id="idsSelecionados">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkSendModalLabel">Confirmar Envio dos Selecionados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
    <p>⚠️ <strong>Atenção!</strong></p>
    <p>
        Está prestes a enviar QR Codes para os Encarregados de Educação de 
        <strong id="selected-count" style="font-size: 1.2em;">0</strong> 
        utente(s) selecionado(s).
    </p>
    <p>Apenas os responsáveis com um e-mail válido registado receberão a informação do QR Code.</p>
    <p>Tem a certeza de que deseja continuar?</p>
</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar e Enviar</button>
                </div>
            </div>
        </form>
    </div>
</div>


        {{-- Javascript files --}}
        <script src="{{ url(mix('js/dist/all.js')) }}" nonce="{{ csrf_token() }}"></script>
        <script src="{{ url('js/select2/i18n/'.Helper::mapBackToLegacyLocale(app()->getLocale()).'.js') }}"></script>

        <!-- v5-beta: This pGenerator call must remain here for v5 - until fixed - so that the JS password generator works for the user create modal. -->
        <script src="{{ url('js/pGenerator.jquery.js') }}"></script>

        {{-- Page level javascript --}}
        @stack('js')

        @section('moar_scripts')
        @show


        <script nonce="{{ csrf_token() }}">

            var clipboard = new ClipboardJS('.js-copy-link');

            clipboard.on('success', function(e) {
                // Get the clicked element
                var clickedElement = $(e.trigger);
                // Get the target element selector from data attribute
                var targetSelector = clickedElement.data('data-clipboard-target');
                // Show the alert that the content was copied
                clickedElement.tooltip('hide').attr('data-original-title', '{{ trans('general.copied') }}').tooltip('show');
            });

            // Reference: https://jqueryvalidation.org/validate/
            var validator = $('#create-form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'alert-msg',
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    $(element).hasClass('select2') || $(element).hasClass('js-data-ajax')
                        // If the element is a select2 then place the error above the input
                        ? element.parents('.required').append(error)
                        // Otherwise place it after
                        : error.insertAfter(element);
                },
                highlight: function(inputElement) {
                    $(inputElement).parent().addClass('has-error');
                    $(inputElement).closest('.help-block').remove();
                },
                onfocusout: function(element) {
                    return $(element).valid();
                },

            });

            $.extend($.validator.messages, {
                required: "{{ trans('validation.generic.required') }}",
                email: "{{ trans('validation.generic.email') }}"
            });


            function showHideEncValue(e) {
                // Use element id to find the text element to hide / show
                var targetElement = e.id+"-to-show";
                var hiddenElement = e.id+"-to-hide";
                if($(e).hasClass('fa-lock')) {
                    $(e).removeClass('fa-lock').addClass('fa-unlock');
                    // Show the encrypted custom value and hide the element with asterisks
                    document.getElementById(targetElement).style.fontSize = "100%";
                    document.getElementById(hiddenElement).style.display = "none";
                } else {
                    $(e).removeClass('fa-unlock').addClass('fa-lock');
                    // ClipboardJS can't copy display:none elements so use a trick to hide the value
                    document.getElementById(targetElement).style.fontSize = "0px";
                    document.getElementById(hiddenElement).style.display = "";
                 }
             }

            $(function () {

                // Invoke Bootstrap 3's tooltip
                $('[data-tooltip="true"]').tooltip({
                    container: 'body',
                    animation: true,
                });

                $('[data-toggle="popover"]').popover();
                $('.select2 span').addClass('needsclick');
                $('.select2 span').removeAttr('title');

                // This javascript handles saving the state of the menu (expanded or not)
                $('body').bind('expanded.pushMenu', function () {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('account.menuprefs', ['state'=>'open']) }}",
                        _token: "{{ csrf_token() }}"
                    });

                });

                $('body').bind('collapsed.pushMenu', function () {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('account.menuprefs', ['state'=>'close']) }}",
                        _token: "{{ csrf_token() }}"
                    });
                });

            });

            // Initiate the ekko lightbox
            $(document).on('click', '[data-toggle="lightbox"]', function (event) {
                event.preventDefault();
                $(this).ekkoLightbox();
            });
            //This prevents multi-click checkouts for accessories, components, consumables
            $(document).ready(function () {
                $('#checkout_form').submit(function (event) {
                    event.preventDefault();
                    $('#submit_button').prop('disabled', true);
                    this.submit();
                });
            });

            // Select encrypted custom fields to hide them in the asset list
            $(document).ready(function() {
                // Selector for elements with css-padlock class
                var selector = 'td.css-padlock';

                // Function to add original value to elements
                function addValue($element) {
                    // Get original value of the element
                    var originalValue = $element.text().trim();

                    // Show asterisks only for not empty values
                    if (originalValue !== '') {
                        // This is necessary to avoid loop because value is generated dynamically
                        if (originalValue !== '' && originalValue !== asterisks) $element.attr('value', originalValue);

                        // Hide the original value and show asterisks of the same length
                        var asterisks = '*'.repeat(originalValue.length);
                        $element.text(asterisks);

                        // Add click event to show original text
                        $element.click(function() {
                            var $this = $(this);
                            if ($this.text().trim() === asterisks) {
                                $this.text($this.attr('value'));
                            } else {
                                $this.text(asterisks);
                            }
                        });
                    }
                }
                // Add value to existing elements
                $(selector).each(function() {
                    addValue($(this));
                });

                // Function to handle mutations in the DOM because content is generated dynamically
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        // Check if new nodes have been inserted
                        if (mutation.type === 'childList') {
                            mutation.addedNodes.forEach(function(node) {
                                if ($(node).is(selector)) {
                                    addValue($(node));
                                } else {
                                    $(node).find(selector).each(function() {
                                        addValue($(this));
                                    });
                                }
                            });
                        }
                    });
                });

                // Configure the observer to observe changes in the DOM
                var config = { childList: true, subtree: true };
                observer.observe(document.body, config);
            });

			
        </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.atualizarNotificacoes = function () {
        // Atualizar contador de notificações não lidas
        fetch('{{ route('notificacoes.contagem') }}')
            .then(res => res.json())
            .then(data => {
                const count = data.nao_lidas || 0;
                const label = document.getElementById('notificacoes-count');
                if (label) {
                    if (count > 0) {
                        label.textContent = count;
                        label.style.display = 'inline';
                    } else {
                        label.style.display = 'none';
                    }
                }
            });

        // Atualizar lista de notificações recentes no dropdown
        fetch('{{ route('notificacoes.recentes') }}')
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById('notificacoes-lista');
                if (lista) {
                    lista.innerHTML = '';
                    if (data.length === 0) {
                        lista.innerHTML = '<li><a href="#">Sem notificações.</a></li>';
                    } else {
                        data.forEach(notificacao => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <a href="/notificacoes/${notificacao.id}/lida">
                                    <i class="fas fa-info-circle text-aqua"></i> ${notificacao.html}
                                </a>
                            `;
                            lista.appendChild(li);
                        });
                    }
                }
            });
    };

    // Executa imediatamente ao carregar
    atualizarNotificacoes();

    // Atualiza automaticamente a cada 60 segundos
    setInterval(atualizarNotificacoes, 60000);
});

</script>

<script>
function abrirEnvioQrCode(event) {
    event.preventDefault(); 

    // Obtém os dados dos itens selecionados na tabela
    var selections = $('#assetsListingTable').bootstrapTable('getSelections');

    // DEBUG: Isto vai mostrar na consola do navegador o que a linha acima encontrou.
    console.log('Itens selecionados:', selections);

    if (selections.length === 0) {
        alert("A função não detetou utentes selecionados. Verifique a consola do navegador (F12) para mais detalhes.");
        return;
    }

    // Extrai os IDs dos itens selecionados
    var ids = selections.map(function(item) {
        return item.id;
    });

    // Atualiza o texto no modal com o número de utentes selecionados
    document.getElementById('selected-count').textContent = selections.length;

    // Coloca os IDs no campo hidden do modal
    document.getElementById('idsSelecionados').value = ids.join(',');

    // Abre o modal de confirmação
    $('#bulkSendModal').modal('show');
}
</script>


        @if ((Session::get('topsearch')=='true') || (Request::is('/')))
            <script nonce="{{ csrf_token() }}">
                $("#tagSearch").focus();
            </script>
        @endif

        </body>
</html>
@stack('scripts')

<style>
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
    flex-direction: column; /* Faz com que os filhos (as divs) se empilhem verticalmente */
    align-items: center; /* Centraliza os itens horizontalmente (as divs) */
    justify-content: center; /* Centraliza os itens verticalmente, se houver espaço */
    /* Remova text-align: center; se estiver presente, pois align-items: center faz o trabalho. */
}

.footer-links {
    margin-top: 5px; /* Adiciona um pequeno espaçamento entre a linha do copyright e os links */
    /* Opcional: Se quiser mais espaçamento entre "Sobre esta aplicação" e "Política...",
       pode adicionar mais margin ou gap. Exemplo: */
    /* display: flex; */
    /* gap: 10px; */
}

.footer-links a {
    color: white; /* Garante que os links continuem brancos */
    text-decoration: none; /* Remove sublinhado padrão */
    /* Adicione padding lateral se quiser mais espaço entre o link e o "|" */
    padding: 0 5px;
}

.footer-links a:hover {
    text-decoration: underline; /* Adiciona sublinhado ao passar o rato */
}

</style>
