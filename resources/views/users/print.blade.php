<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('Alocado a', ['name' => $show_user->present()->fullName()]) }} - {{ date('Y-m-d H:i', time()) }}</title>

    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">

    <link rel="stylesheet" href="{{ url(mix('css/dist/bootstrap-table.css')) }}">

    {{-- stylesheets --}}
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">

    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": 50
            }
        };
    </script>

    <style>
        body {
            font-family: "Arial, Helvetica", sans-serif;
            padding: 20px;
        }
        table.inventory {
            width: 100%;
            border: 1px solid #d3d3d3;
        }

        @page {
            size: A4;
        }

        .print-logo {
            max-height: 40px;
        }

        h4 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
    </style>

    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": 50
            }
        };
    </script>

</head>
<body>

<h3 align="center"><img alt="" src="/uploads/brasao.png" /></h3>
<h1 align="center">Núcleo Sistemas de Informação</h1>
<h2 style="padding-top: 50px;">Lista de Imobilizados Tangíveis e Intangíveis</h1>

@if ($snipeSettings->logo_print_assets=='1')
    @if ($snipeSettings->brand == '3')

 
    @elseif ($snipeSettings->brand == '2')
        @if ($snipeSettings->logo!='')
            <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}">
        @endif
    @else
        <h2>{{ $snipeSettings->site_name }}</h2>
    @endif
@endif

<h3>
    {{ trans('general.assigned_to', ['name' => $show_user->present()->fullName()]) }}
    {{ ($show_user->employee_num!='') ? ' (#'.$show_user->employee_num.') ' : '' }}
    {{ ($show_user->jobtitle!='' ? ' - '.$show_user->jobtitle : '') }}
</h3>
@php
setlocale(LC_TIME, 'pt_PT.UTF-8');
echo "A lista apresentada foi regenerada em " . strftime("%d de %B de %Y às %H:%M:%S");
@endphp

</body>

    @if ($assets->count() > 0)
        @php
            $counter = 1;
        @endphp

        <div id="assets-toolbar">
            <h4>{{ trans_choice('general.countable.assets', $assets->count(), ['count' => $assets->count()]) }}
            </h4>
        </div>

        <table
                class="snipe-table table table-striped inventory"
                id="AssetsAssigned"
                data-pagination="false"
                data-id-table="AssetsAssigned"
                data-search="false"
                data-side-pagination="client"
                data-sortable="true"
                data-toolbar="#assets-toolbar"
                data-show-columns="true"
                data-sort-order="desc"
                data-sort-name="created_at"
                data-show-columns-toggle-all="true"
                data-cookie-id-table="AssetsAssigned">
            <thead>
                <th data-field="asset_id" data-sortable="false" data-visible="true" data-switchable="false">#</th>
                <th data-field="asset_image" data-sortable="true" data-visible="false" data-switchable="true">{{ trans('general.image') }}</th>
                <th data-field="asset_tag" data-sortable="true" data-visible="true" data-switchable="false">{{ trans('admin/hardware/table.asset_tag') }}</th>
                <th data-field="asset_name" data-sortable="true" data-visible="true">{{ trans('general.name') }}</th>
                <th data-field="asset_category" data-sortable="true" data-visible="true">{{ trans('general.category') }}</th>
                <th data-field="asset_model" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.model') }}</th>
                <th data-field="rtd_location" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.default_location') }}</th>
                <th data-field="asset_location" data-sortable="true" data-visible="false">{{ trans('general.location') }}</th>
                <th data-field="asset_serial" data-sortable="true" data-visible="true">{{ trans('admin/hardware/form.serial') }}</th>
                <th data-field="asset_checkout_date" data-sortable="true" data-visible="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th data-field="signature" data-sortable="true" data-visible="true">{{ trans('general.signature') }}</th>
            </thead>
            <tbody>
            @foreach ($assets as $asset)
                @php
                    if ($asset->model->category->getEula()) $eulas[] = $asset->model->category->getEula()
                @endphp
                <tr>
                    <td>{{ $counter }}</td>
                    <td>
                        @if ($asset->getImageUrl())
                            <img src="{{ $asset->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                        @endif
                    </td>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                    <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                    <td>{{ ($asset->defaultLoc) ? $asset->defaultLoc->name : '' }}</td>
                    <td>{{ ($asset->location) ? $asset->location->name : '' }}</td>
                    <td>{{ $asset->serial }}</td>
                    <td>
                        {{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                    <td>
                        @if (($asset->assetlog->first()) && ($asset->assetlog->first()->accept_signature!=''))
                            <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $asset->assetlog->first()->accept_signature }}">
                        @endif
                    </td>
                </tr>
                @if ($settings->show_assigned_assets)
                    @php
                        $assignedCounter = 1;
                    @endphp
                    @foreach ($asset->assignedAssets as $asset)
                        <tr>
                            <td>{{ $counter }}.{{ $assignedCounter }}</td>
                            <td>
                                @if ($asset->getImageUrl())
                                    <img src="{{ $asset->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                                @endif
                            </td>
                            <td>{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : trans('general.invalid_category') }}</td>
                            <td>{{ ($asset->model) ? $asset->model->name : trans('general.invalid_model') }}</td>
                            <td>{{ ($asset->defaultLoc) ? $asset->defaultLoc->name : '' }}</td>
                            <td>{{ ($asset->location) ? $asset->location->name : '' }}</td>
                            <td>{{ $asset->serial }}</td>
                            <td>
                                {{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}</td>
                            <td>
                                @if (($asset->assetlog->first()) && ($asset->assetlog->first()->accept_signature!=''))
                                    <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $asset->assetlog->first()->accept_signature }}">
                                @endif
                            </td>
                        </tr>
                        @php
                            $assignedCounter++
                        @endphp
                    @endforeach
                @endif
                @php
                    $counter++
                @endphp
            @endforeach
            </tbody>
        </table>
    @endif

    @if ($licenses->count() > 0)
        <div id="licenses-toolbar">
            <h4>{{ trans_choice('general.countable.licenses', $licenses->count(), ['count' => $licenses->count()]) }}</h4>
			<th colspan="4">Acessos e Licenças Digitais</th>
        </div>

        <table
                class="snipe-table table table-striped inventory"
                id="licensessAssigned"
                data-toolbar="#licenses-toolbar"
                data-pagination="false"
                data-id-table="licensessAssigned"
                data-search="false"
                data-side-pagination="client"
                data-sortable="true"
                data-show-columns="true"
                data-sort-order="desc"
                data-sort-name="created_at"
                data-show-columns-toggle-all="true"
                data-cookie-id-table="licensessAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false">#</th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('admin/licenses/form.license_key') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
            </tr>
            </thead>
            @php
                $lcounter = 1;
            @endphp

            @foreach ($licenses as $license)
                @php
                    if ($license->category->getEula()) $eulas[] = $license->category->getEula()
                @endphp
                <tr>
                    <td>{{ $lcounter }}</td>
                    <td>{{ $license->name }}</td>
                    <td>
                        @can('viewKeys', $license)
                            {{ $license->serial }}
                        @else
                            <i class="fa-lock" aria-hidden="true"></i> {{ str_repeat('x', 15) }}
                        @endcan
                    </td>
                    <td>{{  $license->pivot->updated_at }}</td>
                </tr>
                @php
                    $lcounter++
                @endphp
            @endforeach
        </table>
    @endif


    @if ($accessories->count() > 0)
        <div id="accessories-toolbar">
            <h4>{{ trans_choice('general.countable.accessories', $accessories->count(), ['count' => $accessories->count()]) }}</h4>
        </div>

        <table
                class="snipe-table table table-striped inventory"
                id="accessoriesAssigned"
                data-toolbar="#accessories-toolbar"
                data-pagination="false"
                data-id-table="accessoriesAssigned"
                data-search="false"
                data-side-pagination="client"
                data-sortable="true"
                data-show-columns="true"
                data-sort-order="desc"
                data-sort-name="created_at"
                data-show-columns-toggle-all="true"
                data-cookie-id-table="accessoriesAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false">#</th>
                <th data-field="accessory_image" data-sortable="true"  data-visible="true">{{ trans('general.image') }}</th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('general.category') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('general.signature') }}</th>
            </tr>
            </thead>
            @php
                $acounter = 1;
            @endphp

            @foreach ($accessories as $accessory)
                @if ($accessory)
                    @php
                        if ($accessory->category->getEula()) $eulas[] = $accessory->category->getEula()
                    @endphp
                    <tr>
                        <td>{{ $acounter }}</td>
                        <td>
                            @if ($accessory->getImageUrl())
                                <img src="{{ $accessory->getImageUrl() }}" class="thumbnail" style="max-height: 50px;">
                            @endif
                        </td>
                        <td>{{ ($accessory->manufacturer) ? $accessory->manufacturer->name : '' }} {{ $accessory->name }} {{ $accessory->model_number }}</td>
                        <td>{{ $accessory->category->name }}</td>
                        <td>{{ $accessory->pivot->created_at }}</td>

                        <td>
                            @if (($accessory->assetlog->first()) && ($accessory->assetlog->first()->accept_signature!=''))
                            <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $accessory->assetlog->first()->accept_signature }}">
                            @endif
                        </td>
                    </tr>
                    @php
                        $acounter++
                    @endphp
                @endif
            @endforeach
        </table>
    @endif

    @if ($consumables->count() > 0)
        <div id="consumables-toolbar">
            <h4>{{ trans_choice('general.countable.consumables', $consumables->count(), ['count' => $consumables->count()]) }}</h4>
        </div>

        <table
                class="snipe-table table table-striped inventory"
                id="consumablesAssigned"
                data-pagination="false"
                data-toolbar="#consumables-toolbar"
                data-id-table="consumablesAssigned"
                data-search="false"
                data-side-pagination="client"
                data-sortable="true"
                data-show-columns="true"
                data-sort-order="desc"
                data-sort-name="created_at"
                data-show-columns-toggle-all="true"
                data-cookie-id-table="consumablesAssigned">
            <thead>
            <tr>
                <th style="width: 20px;" data-sortable="false" data-switchable="false"></th>
                <th style="width: 40%;" data-sortable="true" data-switchable="false">{{ trans('general.name') }}</th>
                <th style="width: 50%;" data-sortable="true">{{ trans('general.category') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('admin/hardware/table.checkout_date') }}</th>
                <th style="width: 10%;" data-sortable="true">{{ trans('general.signature') }}</th>

            </tr>
            </thead>
            @php
                $ccounter = 1;
            @endphp

            @foreach ($consumables as $consumable)
                @if ($consumable)
                    @php
                        if ($consumable->category->getEula()) $eulas[] = $consumable->category->getEula()
                    @endphp
                    <tr>
                        <td>{{ $ccounter }}</td>
                        <td>
                        @if ($consumable->deleted_at!='')
                            <td>{{ ($consumable->manufacturer) ? $consumable->manufacturer->name : '' }}  {{ $consumable->name }} {{ $consumable->model_number }}</td>
                            @else
                            {{ ($consumable->manufacturer) ? $consumable->manufacturer->name : '' }}  {{ $consumable->name }} {{ $consumable->model_number }}
                            @endif
                            </td>
                            <td>{{ ($consumable->category) ? $consumable->category->name : ' invalid/deleted category' }} </td>
                            <td>{{  $consumable->pivot->created_at }}</td>
                            <td>
                                @if (($consumable->assetlog->first()) && ($consumable->assetlog->first()->accept_signature!=''))
                                    <img style="width:auto;height:100px;" src="{{ asset('/') }}display-sig/{{ $consumable->assetlog->first()->accept_signature }}">
                                @endif
                            </td>
                    </tr>
                    @php
                        $ccounter++
                    @endphp
                @endif
            @endforeach
        </table>
    @endif

    <p></p>
    <div class="pull-right">
        <button class="btn btn-default hidden-print" type="button" data-toggle="collapse" data-target="#eula-row" aria-expanded="false" aria-controls="eula-row" title="EULAs">
            <i class="fa fa-eye-slash"></i>
        </button>
    </div>

    <table style="margin-top: 80px;">
        <tr class="collapse" id="eula-row">
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">EULA</td>
            <td style="padding-right: 10px; vertical-align: top; padding-bottom: 80px;" colspan="3">
                @php
                    if (!empty($eulas)) $eulas = array_unique($eulas);
                @endphp
                @if (!empty($eulas))
                    @foreach ($eulas as $key => $eula)
                        {!! $eula !!}
                    @endforeach
                @endif
            </td>
		</tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">O Colaborador:</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr style="height: 80px;">
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">Nome</td>
            <td style="padding-right: 10px; vertical-align: top;">Assinatura</td>
            <td style="padding-right: 10px; vertical-align: top;">Data</td>
        </tr>
        <tr>
            <td style="padding-right: 10px; vertical-align: top; font-weight: bold;">O Técnico NSI:</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td style="padding-right: 10px; vertical-align: top;">______________________________________</td>
            <td>_____________</td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-right: 10px; vertical-align: top;">Nome</td>
            <td style="padding-right: 10px; vertical-align: top;">Assinatura</td>
            <td style="padding-right: 10px; vertical-align: top;">Data</td>
            <td></td>
        </tr>
@php
setlocale(LC_TIME, 'pt_PT.UTF-8');
echo "A lista apresentada foi regenerada em " . strftime("%d de %B de %Y às %H:%M:%S");
@endphp
    </table>

{{-- Javascript files --}}
<script src="{{ url(mix('js/dist/all.js')) }}" nonce="{{ csrf_token() }}"></script>

<script src="{{ url(mix('js/dist/bootstrap-table.js')) }}"></script>

<script>
    $('.snipe-table').bootstrapTable('destroy').each(function () {
        console.log('BS table loaded');

        data_export_options = $(this).attr('data-export-options');
        export_options = data_export_options ? JSON.parse(data_export_options) : {};
        export_options['htmlContent'] = false; // this is already the default; but let's be explicit about it
        export_options['jspdf']= {"orientation": "l"};
        // the following callback method is necessary to prevent XSS vulnerabilities
        // (this is taken from Bootstrap Tables's default wrapper around jQuery Table Export)
        export_options['onCellHtmlData'] = function (cell, rowIndex, colIndex, htmlData) {
            if (cell.is('th')) {
                return cell.find('.th-inner').text()
            }
            return htmlData
        }
        $(this).bootstrapTable({
            classes: 'table table-responsive table-no-bordered',
            ajaxOptions: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            // reorderableColumns: true,
            stickyHeader: true,
            stickyHeaderOffsetLeft: parseInt($('body').css('padding-left'), 10),
            stickyHeaderOffsetRight: parseInt($('body').css('padding-right'), 10),
            undefinedText: '',
            iconsPrefix: 'fa',
            cookieStorage: '{{ config('session.bs_table_storage') }}',
            cookie: true,
            cookieExpire: '2y',
            mobileResponsive: true,
            maintainSelected: true,
            trimOnSearch: false,
            showSearchClearButton: true,
            paginationFirstText: "{{ trans('general.first') }}",
            paginationLastText: "{{ trans('general.last') }}",
            paginationPreText: "{{ trans('general.previous') }}",
            paginationNextText: "{{ trans('general.next') }}",
            pageList: ['10','20', '30','50','100','150','200'{!! ((config('app.max_results') > 200) ? ",'500'" : '') !!}{!! ((config('app.max_results') > 500) ? ",'".config('app.max_results')."'" : '') !!}],
            pageSize: {{  (($snipeSettings->per_page!='') && ($snipeSettings->per_page > 0)) ? $snipeSettings->per_page : 20 }},
            paginationVAlign: 'both',
            queryParams: function (params) {
                var newParams = {};
                for(var i in params) {
                    if(!keyBlocked(i)) { // only send the field if it's not in blockedFields
                        newParams[i] = params[i];
                    }
                }
                return newParams;
            },
            formatLoadingMessage: function () {
                return '<h2><i class="fas fa-spinner fa-spin" aria-hidden="true"></i> {{ trans('general.loading') }} </h4>';
            },
            icons: {
                advancedSearchIcon: 'fas fa-search-plus',
                paginationSwitchDown: 'fa-caret-square-o-down',
                paginationSwitchUp: 'fa-caret-square-o-up',
                fullscreen: 'fa-expand',
                columns: 'fa-columns',
                refresh: 'fas fa-sync-alt',
                export: 'fa-download',
                clearSearch: 'fa-times'
            },
            exportOptions: export_options,

            exportTypes: ['xlsx', 'excel', 'csv', 'pdf','json', 'xml', 'txt', 'sql', 'doc' ],
            onLoadSuccess: function () {
                $('[data-tooltip="true"]').tooltip(); // Needed to attach tooltips after ajax call
            }

        });
    });
</script>
<p> </p>
<h5 align="center"><span style="color:#000000">Alameda dos Oceanos, n.&ordm; 83, 1990-212 Lisboa | 211 388 800 | atendimento@jf-parquedasnacoes.pt | NIF: 510 878 393 | </span><a href="http://www.jf-parquedasnacoes.pt" title="http://www.jf-parquedasnacoes.pt">www.jf-parquedasnacoes.pt</a></h5>

<h6 align="center">N&uacute;cleo Sistemas de Informa&ccedil;&atilde;o &copy; 2016-2024 JF- Parque das Na&ccedil;&otilde;es</h6>

</body>
</html>
