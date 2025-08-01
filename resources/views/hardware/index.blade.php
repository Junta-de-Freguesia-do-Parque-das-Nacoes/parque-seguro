@extends('layouts/default')

@section('title0')
  @if ((Request::get('company_id')) && ($company))
    {{ $company->name }}
  @endif

  @if (Request::get('status'))
    @if (Request::get('status')=='Pending')
      {{ trans('general.pending') }}
    @elseif (Request::get('status')=='RTD')
      {{ trans('general.ready_to_deploy') }}
    @elseif (Request::get('status')=='Deployed')
      {{ trans('general.deployed') }}
    @elseif (Request::get('status')=='Undeployable')
      {{ trans('general.undeployable') }}
    @elseif (Request::get('status')=='Deployable')
      {{ trans('general.deployed') }}
    @elseif (Request::get('status')=='Requestable')
      {{ trans('admin/hardware/general.requestable') }}
    @elseif (Request::get('status')=='Archived')
      {{ trans('general.archived') }}
    @elseif (Request::get('status')=='Deleted')
      {{ trans('general.deleted') }}
    @elseif (Request::get('status')=='byod')
      {{ trans('general.byod') }}
    @endif
  @else
    {{ trans('general.all') }}
  @endif
  {{ trans('general.assets') }}

  @if (Request::has('order_number'))
    : Order #{{ strval(Request::get('order_number')) }}
  @endif
@stop

@section('title')
  @yield('title0') @parent
@stop

@section('header_right')
  @can('create', \App\Models\Asset::class)
    <a href="{{ route('hardware.create') }}" accesskey="n" class="btn btn-primary pull-right">
      {{ trans('general.create') }}
    </a>
  @endcan
@stop

{{-- Page content --}}

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">

            @php
              $labelTemplates = collect(config('labels.label_templates'))->map(function ($label) {
                return [
                  'value' => $label,
                  'label' => [
                    'CartaoparqueseguroMaior' => 'Cartão EE 👨‍👩',
                    'CartaoparqueseguroMenor' => 'Cartão Utente 👧',
                  ][$label] ?? $label
                ];
              });
            @endphp

            {{-- Toolbar personalizada --}}
            <div id="assetsBulkEditToolbar" class="mb-3 d-flex flex-wrap align-items-center">
              
              {{-- Filtro por Programa --}}
              <div class="form-group mr-3">
                <label for="programaSelect" class="mr-2">Programa:</label>
                <select id="programaSelect" class="form-control">
                  <option value="">Todos</option>
                  <option value="Há Férias No Parque">Há Férias No Parque</option>
                  <option value="Parque em Movimento Verão">Parque em Movimento Verão</option>
                  <option value="Parque em Movimento Páscoa">Parque em Movimento Páscoa</option>
                  <option value="AAAF/CAF Férias Páscoa">AAAF/CAF Férias Páscoa</option>
                  <option value="AAAF/CAF Férias Verão">AAAF/CAF Férias Verão</option>
                  <option value="Parque em Movimento Natal">Parque em Movimento Natal</option>
                  <option value="AAAF/CAF Férias Carnaval">AAAF/CAF Férias Carnaval</option>
                </select>
              </div>
              <div class="form-group mr-3">
  <label for="eeSelect" class="mr-2">Utentes sem EE Configurado:</label>
  <select id="eeSelect" class="form-control">
    <option value="">Todos</option>
    <option value="sem">Sem Encarregado de Educação</option>
  </select>
</div>

              {{-- Tipo de Cartão --}}
<div class="form-group mr-3">
  <label for="tipoCartaoSelect" class="mr-2">Tipo de Cartão:</label>
  <select id="tipoCartaoSelect" class="form-control">
    @foreach ($labelTemplates as $template)
      <option value="{{ $template['value'] }}">{{ $template['label'] }}</option>
    @endforeach
  </select>
  <div id="confirmCartaoMsg" style="display:none; position: relative; margin-left: 15px;">
  <div style="
  position: absolute;
  top: -15px;
  left: 40px;
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  border-radius: 10px;
  padding: 10px 16px;
  font-size: 2rem; /* Aumentado */
  font-weight: 500;
  white-space: nowrap;
  box-shadow: 1px 1px 6px rgba(0,0,0,0.15);
  z-index: 1;
">
  🤖 NSI diz que o Tipo de cartão para impressão foi alterado com sucesso 😜
</div>

  <div style="font-size: 1.5rem;">🤖</div>
</div>

</div>


            </div>

            @include('partials.asset-bulk-actions', ['status' => Request::get('status')])

            <table
              data-advanced-search="true"
              data-click-to-select="false"
              data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
              data-cookie-id-table="assetsListingTable"
              data-pagination="true"
              data-id-table="assetsListingTable"
              data-search="true"
              data-side-pagination="server"
              data-show-columns="true"
              data-show-export="true"
              data-show-footer="true"
              data-show-refresh="true"
              data-sort-order="asc"
              data-sort-name="name"
              data-show-fullscreen="true"
              data-toolbar="#assetsBulkEditToolbar"
              data-bulk-button-id="#bulkAssetEditButton"
              data-bulk-form-id="#assetsBulkForm"
              id="assetsListingTable"
              class="table table-striped snipe-table"
              data-url="{{ route('api.assets.index', [
                'status' => e(Request::get('status')),
                'order_number' => e(strval(Request::get('order_number'))),
                'company_id' => e(Request::get('company_id')),
                'status_id' => e(Request::get('status_id'))
              ]) }}"
              data-export-options='{
                "fileName": "export{{ (Request::has("status")) ? "-".str_slug(Request::get("status")) : "" }}-assets-{{ date("Y-m-d") }}",
                "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
              }'>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('moar_scripts')
  @include('partials.bootstrap-table')

  <script>
  $(document).ready(function () {
    function refreshTable() {
      var baseUrl = '{{ route('api.assets.index') }}';
      var params = [];

      var programa = $('#programaSelect').val();
      var semEe = $('#eeSelect').val(); // <- novo campo

      if (programa) {
        params.push('programa=' + encodeURIComponent(programa));
      }

      if (semEe) {
        params.push('sem_ee=' + encodeURIComponent(semEe));
      }

      var finalUrl = baseUrl;
      if (params.length > 0) {
        finalUrl += '?' + params.join('&');
      }

      $('#assetsListingTable').bootstrapTable('refresh', {
        url: finalUrl
      });
    }

    // Eventos de mudança nos selects
    $('#programaSelect').on('change', refreshTable);
    $('#eeSelect').on('change', refreshTable);

    // Lógica do tipoCartaoSelect (mantém tudo como estava)
    const tipoSelect = document.getElementById('tipoCartaoSelect');
    const valorAtual = '{{ \App\Models\Setting::first()->label2_template ?? '' }}';
    if (valorAtual && tipoSelect) tipoSelect.value = valorAtual;

    tipoSelect?.addEventListener('change', function () {
      const valor = tipoSelect.value;
      if (!valor) return;

      fetch("{{ route('settings.saveLabelTemplate') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ label2_template: valor })
      })
      .then(() => {
        const msg = document.getElementById('confirmCartaoMsg');
        msg.style.opacity = 0;
        msg.style.display = 'inline-block';

        // Fade in
        setTimeout(() => {
          msg.style.transition = 'opacity 0.5s';
          msg.style.opacity = 1;
        }, 10);

        // Fade out
        setTimeout(() => {
          msg.style.opacity = 0;
          setTimeout(() => {
            msg.style.display = 'none';
          }, 500);
        }, 3000);
      })
      .catch(error => {
        console.error(error);
        alert('Erro ao gravar tipo de cartão.');
      });
    });
  });
</script>

@stop
