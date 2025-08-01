@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/hardware/general.view') }} {{ $asset->asset_tag }} {{ $asset->name }}
    @parent
@stop

{{-- Page content --}}
@section('content')


    <div class="row">

        @if (!$asset->model)
            <div class="col-md-12">
                <div class="callout callout-danger">
                    <p><strong>{{ trans('admin/models/message.no_association') }}</strong> {{ trans('admin/models/message.no_association_fix') }}</p>
                </div>
            </div>
        @endif

        @if ($asset->checkInvalidNextAuditDate())
            <div class="col-md-12">
                <div class="callout callout-warning">
                    <p><strong>{{ trans('general.warning',
                        [
                            'warning' => trans('admin/hardware/message.warning_audit_date_mismatch',
                                    [
                                        'last_audit_date' => Helper::getFormattedDateObject($asset->last_audit_date, 'date', false),
                                        'next_audit_date' => Helper::getFormattedDateObject($asset->next_audit_date, 'date', false)
                                    ]
                                    )
                        ]
                        ) }}</strong></p>
                </div>
            </div>
        @endif

        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs hidden-print">

                    <li class="active">
                        <a href="#details" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                          <i class="fas fa-info-circle fa-2x"></i>
                          </span>
                            <span class="hidden-xs hidden-sm">{{ trans('admin/users/general.info') }}</span>
                        </a>
                    </li>

   

                    <li>
    <a href="#software" data-toggle="tab">
        <span class="hidden-lg hidden-md">
            <i class="fa fa-certificate fa-2x"></i> {{-- ou outro ícone a gosto --}}
        </span>
        <span class="hidden-xs hidden-sm">
            {{ trans('general.licenses') }}
            {!! ($asset->licenses->count() > 0 ) ? '<span class="badge badge-secondary">'.number_format($asset->licenses->count()).'</span>' : '' !!}
        </span>
    </a>
</li>


                    <li>
                        <a href="#maintenances" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                            <i class="fas fa-wrench fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.maintenances') }}
                                {!! ($asset->assetmaintenances()->count() > 0 ) ? '<span class="badge badge-secondary">'.number_format($asset->assetmaintenances()->count()).'</span>' : '' !!}
                          </span>
                        </a>
                    </li>

					  <li>
                        <a href="#history" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                            <i class="fas fa-history fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.history') }}
                          </span>
                        </a>
                    </li>

                


                    <li>
                        <a href="#files" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                            <i class="far fa-file fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.files') }}
                                {!! ($asset->uploads->count() > 0 ) ? '<span class="badge badge-secondary">'.number_format($asset->uploads->count()).'</span>' : '' !!}
                          </span>
                        </a>
                    </li>

                    @can('view', $asset->model)
                    <li>
                        <a href="#modelfiles" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                              <i class="fa-solid fa-laptop-file fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="hidden-xs hidden-sm">
                            {{ trans('general.additional_files') }}
                                {!! ($asset->model) && ($asset->model->uploads->count() > 0 ) ? '<span class="badge badge-secondary">'.number_format($asset->model->uploads->count()).'</span>' : '' !!}
                          </span>
                        </a>
                    </li>
                    @endcan


                    @can('update', \App\Models\Asset::class)
                        <li class="pull-right">
                            <a href="#" data-toggle="modal" data-target="#uploadFileModal">
                                <i class="fas fa-paperclip" aria-hidden="true"></i>
                                {{ trans('button.upload') }}
                            </a>
                        </li>
                    @endcan

                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="details">
                    <div class="row">

                            @if ($asset->deleted_at!='')
                                <div class="col-md-12">
                                    <div class="callout callout-warning">
                                        <i class="icon fas fa-exclamation-triangle"></i>
                                        {{ trans('admin/users/message.user_deleted_warning') }}
                                    </div>
                                </div>
                            @endif
                                       

                            <!-- Start button column -->
                            <div class="col-md-3 col-xs-12 col-sm-push-9">

                                <div class="col-md-12 text-center">
                                    @if (($asset->image) || (($asset->model) && ($asset->model->image!='')))
                                        <div class="text-center col-md-12" style="padding-bottom: 15px;">
                                            <a href="{{ ($asset->getImageUrl()) ? $asset->getImageUrl() : null }}" data-toggle="lightbox">
                                                <img src="{{ ($asset->getImageUrl()) ? $asset->getImageUrl() : null }}" class="assetimg img-responsive" alt="{{ $asset->getDisplayNameAttribute() }}">
                                            </a>
                                        </div>
                                    @else
                                        <!-- generic image goes here -->
                                    @endif
                                </div>



                                @if (($asset->assetstatus) && ($asset->assetstatus->deployable=='1'))
                                    @if (($asset->assigned_to != '') && ($asset->deleted_at==''))
                                        @can('checkin', \App\Models\Asset::class)
                                            <div class="col-md-12">
                                                    <span class="tooltip-wrapper"{!! (!$asset->model ? ' data-tooltip="true" title="'.trans('admin/hardware/general.model_invalid_fix').'"' : '') !!}>
                                                        <a role="button" href="{{ route('hardware.checkin.create', $asset->id) }}" class="btn btn-sm btn-primary btn-block hidden-print{{ (!$asset->model ? ' disabled' : '') }}">
                                                        {{ trans('admin/hardware/general.checkin') }}
                                                    </a>
                                                    </span>
                                            </div>
                                        @endcan
                                  
                                    @endif
                                @endif

                                @if ($asset->deleted_at=='')
                                    @can('update', $asset)
                                        <div class="col-md-12" style="padding-top: 5px;">
                                            <a href="{{ route('hardware.edit', $asset->id) }}" class="btn btn-sm btn-primary btn-block hidden-print">
                                                {{ trans('admin/hardware/general.edit') }}
                                            </a>
                                        </div>
                                    @endcan

                                    
                                @endif

                                @can('create', $asset)
                                    <div class="col-md-12" style="padding-top: 5px;">
                                        <a href="{{ route('clone/hardware', $asset->id) }}" class="btn btn-sm btn-primary btn-block hidden-print">
                                            {{ trans('admin/hardware/general.clone') }}
                                        </a>
                                    </div>
   				    @endcan

<div class="col-md-12" style="padding-top: 5px;">
    <a href="{{ url('/utentes/' . $asset->id . '/responsaveis') }}" class="btn btn-warning btn-block">
        Ver Autorizados Recolha
    </a>
</div>

<div class="col-md-12" style="padding-top: 5px;">
    <a href="{{ url('/utentes/' . $asset->id . '/historico') }}" class="btn btn-sm btn-primary btn-block hidden-print">
        Histórico de Entradas e Saídas
    </a>
</div>
<div class="col-md-12" style="padding-top: 5px;">
    <a href="#" class="btn btn-sm btn-primary btn-block hidden-print" data-toggle="modal" data-target="#preferencesModal">
        Gerir Preferências de Notificação
    </a>
</div>
<div class="col-md-12" style="padding-top: 5px;">
    <a href="{{ url('hardware/' . $asset->id . '/sendQrEmail') }}" 
       class="btn btn-info btn-block">
        Enviar QrCode por Email
    </a>
</div>


                                @can('delete', $asset)
                                    <div class="col-md-12" style="padding-top: 30px; padding-bottom: 30px;">
                                        @if ($asset->deleted_at=='')
                                            <button class="btn btn-sm btn-block btn-danger delete-asset" data-toggle="modal" data-title="{{ trans('general.delete') }}" data-content="{{ trans('general.sure_to_delete_var', ['item' => $asset->asset_tag]) }}" data-target="#dataConfirmModal">{{ trans('general.delete') }}
                                            </button>
                                            <span class="sr-only">{{ trans('general.delete') }}</span>
                                        @else
                                            <form method="POST" action="{{ route('restore/hardware', ['assetId' => $asset->id]) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-warning btn-block">{{ trans('general.restore') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                @endcan

                                @if (($asset->assignedTo) && ($asset->deleted_at==''))
                                    <div class="col-md-12" style="text-align: left">
                                        <h2>
                                            {{ trans('admin/hardware/form.checkedout_to') }}
                                        </h2>
                                        <p>
                                            @if (($asset->checkedOutToUser()) && ($asset->assignedTo->present()->gravatar()))
                                                <img src="{{ $asset->assignedTo->present()->gravatar() }}" class="user-image-inline" alt="{{ $asset->assignedTo->present()->fullName() }}">
                                            @endif
                                        </p>
                                        {!! $asset->assignedTo->present()->glyph() . ' ' .$asset->assignedTo->present()->nameUrl() !!}


                                        <ul class="list-unstyled" style="line-height: 25px;">
                                            @if ((isset($asset->assignedTo->email)) && ($asset->assignedTo->email!=''))
                                                <li>
                                                    <i class="far fa-envelope" aria-hidden="true"></i>
                                                    <a href="mailto:{{ $asset->assignedTo->email }}">{{ $asset->assignedTo->email }}</a>
                                                </li>
                                            @endif

                                            @if ((isset($asset->assignedTo)) && ($asset->assignedTo->phone!=''))
                                                <li>
                                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                                    <a href="tel:{{ $asset->assignedTo->phone }}">{{ $asset->assignedTo->phone }}</a>
                                                </li>
                                            @endif

                                            @if((isset($asset->assignedTo)) && ($asset->assignedTo->department))
                                                <li>{{ trans('admin/hardware/general.user_department') }}: {{ $asset->assignedTo->department->name}}</li>
                                            @endif

                                            @if (isset($asset->location))
                                                <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $asset->location->name }}</li>
                                                <li>{{ $asset->location->address }}
                                                    @if ($asset->location->address2!='')
                                                        {{ $asset->location->address2 }}
                                                    @endif
                                                </li>

                                                <li>{{ $asset->location->city }}
                                                    @if (($asset->location->city!='') && ($asset->location->state!=''))
                                                        ,
                                                    @endif
                                                    {{ $asset->location->state }} {{ $asset->location->zip }}
                                                </li>
                                            @endif
                                            <li>
                                                <i class="fas fa-calendar"></i> {{ trans('admin/hardware/form.checkout_date') }}: {{ Helper::getFormattedDateObject($asset->last_checkout, 'date', false) }}
                                            </li>
                                            @if (isset($asset->expected_checkin))
                                                <li>
                                                    <i class="fas fa-calendar"></i> {{ trans('admin/hardware/form.expected_checkin') }}: {{ Helper::getFormattedDateObject($asset->expected_checkin, 'date', false) }}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                                    @if  ($snipeSettings->qr_code=='1')
                                        <div class="col-md-12 text-center" style="padding-top: 15px;">


<img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->generate(config('app.url') . '/confirmar-check/' . $asset->id)) !!} " class="img-thumbnail" style="height: 150px; width: 151px; margin-right: 10px;" alt="QR code for {{ $asset->name }}">

	</div>
                                    @endif

                                <br><br>
                            </div>

                            <!-- End button column -->

                            <div class="col-md-9 col-xs-12 col-sm-pull-3">

                                <div class="row-new-striped">
                                    @if ($asset->deleted_at!='')
                                        <div class="row">
                                            <div class="col-md-3">
                                                <span class="text-danger"><strong>{{ trans('general.deleted') }}</strong></span>
                                            </div>
                                            <div class="col-md-9">
                                                {{ \App\Helpers\Helper::getFormattedDateObject($asset->deleted_at, 'date', false) }}

                                            </div>
                                        </div>
                                    @endif



                                    @if ($asset->assetstatus)

                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>{{ trans('general.status') }}</strong>
                                            </div>
                                            <div class="col-md-9">
                                                @if (($asset->assignedTo) && ($asset->deleted_at==''))
                                                    <i class="fas fa-circle text-blue"></i>
                                                    {{ $asset->assetstatus->name }}
                                                    <label class="label label-default">{{ trans('general.deployed') }}</label>

                                                    <i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i>
                                                    {!!  $asset->assignedTo->present()->glyph()  !!}
                                                    {!!  $asset->assignedTo->present()->nameUrl() !!}
                                                @else
                                                    @if (($asset->assetstatus) && ($asset->assetstatus->deployable=='1'))
                                                        <i class="fas fa-circle text-green"></i>
                                                    @elseif (($asset->assetstatus) && ($asset->assetstatus->pending=='1'))
                                                        <i class="fas fa-circle text-orange"></i>
                                                    @else
                                                        <i class="fas fa-times text-red"></i>
                                                    @endif
                                                    <a href="{{ route('statuslabels.show', $asset->assetstatus->id) }}">
                                                        {{ $asset->assetstatus->name }}</a>
                                                    <label class="label label-default">{{ $asset->present()->statusMeta }}</label>

                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->company)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>{{ trans('general.company') }}</strong>
                                            </div>
                                            <div class="col-md-9">
                                                <a href="{{ url('/companies/' . $asset->company->id) }}">{{ $asset->company->name }}</a>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->name)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>{{ trans('admin/hardware/form.name') }}</strong>
                                            </div>
                                            <div class="col-md-9">
                                                {{ $asset->name }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->serial)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>{{ trans('admin/hardware/form.serial') }}</strong>
                                            </div>
                                            <div class="col-md-9">
                                                <span class="js-copy">{{ $asset->serial  }}</span>

                                                <i class="fa-regular fa-clipboard js-copy-link" data-clipboard-target=".js-copy" aria-hidden="true" data-tooltip="true" data-placement="top" title="{{ trans('general.copy_to_clipboard') }}">
                                                    <span class="sr-only">{{ trans('general.copy_to_clipboard') }}</span>
                                                </i>
                                            </div>
                                        </div>
                                    @endif

                                    @if ((isset($audit_log)) && ($audit_log->created_at))
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.last_audit') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {!! $asset->checkInvalidNextAuditDate() ? '<i class="fas fa-exclamation-triangle text-orange" aria-hidden="true"></i>' : '' !!}
                                                {{ Helper::getFormattedDateObject($audit_log->created_at, 'date', false) }}
                                                @if ($audit_log->user)
                                                    (by {{ link_to_route('users.show', $audit_log->user->present()->fullname(), [$audit_log->user->id]) }})
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->next_audit_date)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.next_audit_date') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {!! $asset->checkInvalidNextAuditDate() ? '<i class="fas fa-exclamation-triangle text-orange" aria-hidden="true"></i>' : '' !!}
                                                {{ Helper::getFormattedDateObject($asset->next_audit_date, 'date', false) }}
                                            </div>
                                        </div>
                                    @endif

                                    @if (($asset->model) && ($asset->model->manufacturer))
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.manufacturer') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                <ul class="list-unstyled">
                                                    @can('view', \App\Models\Manufacturer::class)

                                                        <li>
                                                            <a href="{{ route('manufacturers.show', $asset->model->manufacturer->id) }}">
                                                                {{ $asset->model->manufacturer->name }}
                                                            </a>
                                                        </li>

                                                    @else
                                                        <li> {{ $asset->model->manufacturer->name }}</li>
                                                    @endcan

                                                    @if (($asset->model) && ($asset->model->manufacturer) &&  ($asset->model->manufacturer->url!=''))
                                                        <li>
                                                            <i class="fas fa-globe-americas" aria-hidden="true"></i>
                                                            <a href="{{ $asset->present()->dynamicUrl($asset->model->manufacturer->url) }}" target="_blank">
                                                                {{ $asset->present()->dynamicUrl($asset->model->manufacturer->url) }}
                                                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (($asset->model) && ($asset->model->manufacturer) &&  ($asset->model->manufacturer->support_url!=''))
                                                        <li>
                                                            <i class="far fa-life-ring" aria-hidden="true"></i>
                                                            <a href="{{ $asset->present()->dynamicUrl($asset->model->manufacturer->support_url) }}" target="_blank">
                                                                {{ $asset->present()->dynamicUrl($asset->model->manufacturer->support_url) }}
                                                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (($asset->model) && ($asset->model->manufacturer) &&  ($asset->model->manufacturer->warranty_lookup_url!=''))
                                                        <li>
                                                            <i class="far fa-wrench" aria-hidden="true"></i>
                                                            <a href="{{ $asset->present()->dynamicUrl($asset->model->manufacturer->warranty_lookup_url) }}" target="_blank">
                                                                {{ $asset->present()->dynamicUrl($asset->model->manufacturer->warranty_lookup_url) }}

                                                                <i class="fa fa-external-link" aria-hidden="true"><span class="sr-only">{{ trans('admin/hardware/general.mfg_warranty_lookup', ['manufacturer' => $asset->model->manufacturer->name]) }}</span></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (($asset->model) && ($asset->model->manufacturer->support_phone))
                                                        <li>
                                                            <i class="fas fa-phone" aria-hidden="true"></i>
                                                            <a href="tel:{{ $asset->model->manufacturer->support_phone }}">
                                                                {{ $asset->model->manufacturer->support_phone }}
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (($asset->model) && ($asset->model->manufacturer->support_email))
                                                        <li>
                                                            <i class="far fa-envelope" aria-hidden="true"></i>
                                                            <a href="mailto:{{ $asset->model->manufacturer->support_email }}">
                                                                {{ $asset->model->manufacturer->support_email }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>
                                                {{ trans('general.category') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-9">
                                            @if (($asset->model) && ($asset->model->category))

                                                @can('view', \App\Models\Category::class)

                                                    <a href="{{ route('categories.show', $asset->model->category->id) }}">
                                                        {{ $asset->model->category->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->model->category->name }}
                                                @endcan
                                            @else
                                                Invalid category
                                            @endif
                                        </div>
                                    </div>

                                    @if ($asset->model)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.model') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @if ($asset->model)

                                                    @can('view', \App\Models\AssetModel::class)
                                                        <a href="{{ route('models.show', $asset->model->id) }}">
                                                            {{ $asset->model->name }}
                                                        </a>
                                                    @else
                                                        {{ $asset->model->name }}
                                                    @endcan

                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>
                                                {{ trans('admin/models/table.modelnumber') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-9">
                                            {{ ($asset->model) ? $asset->model->model_number : ''}}
                                        </div>
                                    </div>

                                    <!-- byod -->
				    <div class="row">
                                        <div class="col-md-3">
                                            <strong>{{ trans('general.byod') }}</strong>
                                        </div>
                                        <div class="col-md-9">
                                            {!! ($asset->byod=='1') ? '<i class="fas fa-check text-success" aria-hidden="true"></i> '.trans('general.yes') : '<i class="fas fa-times text-danger" aria-hidden="true"></i> '.trans('general.no')  !!}
                                        </div>
                                    </div>

                                    @if (($asset->model) && ($asset->model->fieldset))
                                        @foreach($asset->model->fieldset->fields as $field)
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <strong>
                                                        {{ $field->name }}
                                                    </strong>
                                                </div>
                                                <div class="col-md-9{{ (($field->format=='URL') && ($asset->{$field->db_column_name()}!='')) ? ' ellipsis': '' }}">
                                                    @if (($field->field_encrypted=='1') && ($asset->{$field->db_column_name()}!=''))
                                                        <i class="fas fa-lock" data-tooltip="true" data-placement="top" title="{{ trans('admin/custom_fields/general.value_encrypted') }}" onclick="showHideEncValue(this)" id="text-{{ $field->id }}"></i>
                                                    @endif

                                                    @if ($field->isFieldDecryptable($asset->{$field->db_column_name()} ))
                                                        @can('assets.view.encrypted_custom_fields')
                                                            @php
                                                                $fieldSize=strlen(Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}))
                                                            @endphp
                                                            @if ($fieldSize>0)
                                                                <span id="text-{{ $field->id }}-to-hide">{{ str_repeat('*', $fieldSize) }}</span>
                                                                <span class="js-copy-{{ $field->id }}" id="text-{{ $field->id }}-to-show" style="font-size: 0px;">
                                                                @if (($field->format=='URL') && ($asset->{$field->db_column_name()}!=''))
                                                                        <a href="{{ Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}) }}" target="_new">{{ Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}) }}</a>
                                                                    @elseif (($field->format=='DATE') && ($asset->{$field->db_column_name()}!=''))
                                                                        {{ \App\Helpers\Helper::gracefulDecrypt($field, \App\Helpers\Helper::getFormattedDateObject($asset->{$field->db_column_name()}, 'date', false)) }}
                                                                    @else
                                                                        {{ Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}) }}
                                                                    @endif
                                                                </span>
                                                                <i class="fa-regular fa-clipboard js-copy-link" data-clipboard-target=".js-copy-{{ $field->id }}" aria-hidden="true" data-tooltip="true" data-placement="top" title="{{ trans('general.copy_to_clipboard') }}">
                                                                    <span class="sr-only">{{ trans('general.copy_to_clipboard') }}</span>
                                                                </i>
                                                            @endif
                                                        @else
                                                            {{ strtoupper(trans('admin/custom_fields/general.encrypted')) }}
                                                        @endcan

                                                    @else
                                                        @if (($field->format=='BOOLEAN') && ($asset->{$field->db_column_name()}!=''))
                                                            {!! ($asset->{$field->db_column_name()} == 1) ? "<span class='fas fa-check-circle' style='color:green' />" : "<span class='fas fa-times-circle' style='color:red' />" !!}
                                                        @elseif (($field->format=='URL') && ($asset->{$field->db_column_name()}!=''))
                                                            <a href="{{ $asset->{$field->db_column_name()} }}" target="_new">{{ $asset->{$field->db_column_name()} }}</a>
                                                        @elseif (($field->format=='DATE') && ($asset->{$field->db_column_name()}!=''))
                                                            {{ \App\Helpers\Helper::getFormattedDateObject($asset->{$field->db_column_name()}, 'date', false) }}
                                                        @else
                                                            {!! nl2br(e($asset->{$field->db_column_name()})) !!}
                                                        @endif

                                                    @endif

                                                    @if ($asset->{$field->db_column_name()}=='')
                                                        &nbsp;
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif


                                   

                                    
                                   
                                   

                                    @if ($asset->supplier)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.supplier') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @can ('superuser')
                                                    <a href="{{ route('suppliers.show', $asset->supplier_id) }}">
                                                        {{ $asset->supplier->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->supplier->name }}
                                                @endcan
                                            </div>
                                        </div>
                                    @endif


                                    @if ($asset->warranty_months)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.warranty') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {{ $asset->warranty_months }}
                                                {{ trans('admin/hardware/form.months') }}

                                                @if (($asset->model) && ($asset->model->manufacturer) && ($asset->model->manufacturer->warranty_lookup_url!=''))
                                                    <a href="{{ $asset->present()->dynamicUrl($asset->model->manufacturer->warranty_lookup_url) }}" target="_blank">
                                                        <i class="fa fa-external-link" aria-hidden="true"><span class="sr-only">{{ trans('admin/hardware/general.mfg_warranty_lookup', ['manufacturer' => $asset->model->manufacturer->name]) }}</span></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.warranty_expires') }}
                                                    @if ($asset->purchase_date)
                                                        {!! $asset->present()->warranty_expires() < date("Y-m-d") ? '<i class="fas fa-exclamation-triangle text-orange" aria-hidden="true"></i>' : '' !!}
                                                    @endif

                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @if ($asset->purchase_date)
                                                    {{ Helper::getFormattedDateObject($asset->present()->warranty_expires(), 'date', false) }}
                                                    -
                                                    {{ Carbon::parse($asset->present()->warranty_expires())->diffForHumans(['parts' => 2]) }}
                                                @else
                                                    {{ trans('general.na_no_purchase_date') }}
                                                @endif
                                            </div>
                                        </div>

                                    @endif

                                    @if (($asset->model) && ($asset->depreciation))
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.depreciation') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {{ $asset->depreciation->name }}
                                                ({{ $asset->depreciation->months }}
                                                {{ trans('admin/hardware/form.months') }})
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.fully_depreciated') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @if ($asset->purchase_date)
                                                    {{ Helper::getFormattedDateObject($asset->depreciated_date()->format('Y-m-d'), 'date', false) }}
                                                    -
                                                    {{ Carbon::parse($asset->depreciated_date())->diffForHumans(['parts' => 2]) }}
                                                @else
                                                    {{ trans('general.na_no_purchase_date') }}
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                    @if (($asset->asset_eol_date) && ($asset->purchase_date))
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.eol_rate') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {{ Carbon::parse($asset->asset_eol_date)->diffInMonths($asset->purchase_date) }}
                                                {{ trans('admin/hardware/form.months') }}

                                            </div>
                                        </div>
                                    @endif
                                    @if ($asset->asset_eol_date)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.eol_date') }}
                                                    @if ($asset->purchase_date)
                                                        {!! $asset->asset_eol_date < date("Y-m-d") ? '<i class="fas fa-exclamation-triangle text-orange" aria-hidden="true"></i>' : '' !!}
                                                    @endif
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @if ($asset->asset_eol_date)
                                                    {{ Helper::getFormattedDateObject($asset->asset_eol_date, 'date', false) }}
                                                    -
                                                    {{ Carbon::parse($asset->asset_eol_date)->diffForHumans(['parts' => 2]) }}
                                                @else
                                                    {{ trans('general.na_no_purchase_date') }}
                                                @endif
                                                @if ($asset->eol_explicit)
                                                    <i class="fas fa-exclamation-triangle text-orange"
                                                       aria-hidden="true"
                                                       data-tooltip="true"
                                                       data-placement="top"
                                                       data-title="Explicit EOL"
                                                       title="Explicit EOL">
                                                    </i>
                                                @endif
                                            </div>
                                        </div>
                                    @endif


                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>
                                                {{ trans('admin/hardware/form.notes') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-9">
                                            {!! nl2br(Helper::parseEscapedMarkedownInline($asset->notes)) !!}
                                        </div>
                                    </div>

                                    @if ($asset->location)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.location') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @can('superuser')
                                                    <a href="{{ route('locations.show', ['location' => $asset->location->id]) }}">
                                                        {{ $asset->location->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->location->name }}
                                                @endcan
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->defaultLoc)
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('admin/hardware/form.default_location') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                @can('superuser')
                                                    <a href="{{ route('locations.show', ['location' => $asset->defaultLoc->id]) }}">
                                                        {{ $asset->defaultLoc->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->defaultLoc->name }}
                                                @endcan
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->created_at!='')
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.created_at') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {{ Helper::getFormattedDateObject($asset->created_at, 'datetime', false) }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->updated_at!='')
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>
                                                    {{ trans('general.updated_at') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-9">
                                                {{ Helper::getFormattedDateObject($asset->updated_at, 'datetime', false) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                                                  
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>
                                                Labels
                                            </strong>
                                        </div>
                                        <div class="col-md-9">
                                            {{ Form::open([
                                                      'method' => 'POST',
                                                      'route' => ['hardware/bulkedit'],
                                                      'class' => 'form-inline',
                                                       'id' => 'bulkForm']) }}
                                            <input type="hidden" name="bulk_actions" value="labels" />
                                            <input type="hidden" name="ids[{{$asset->id}}]" value="{{ $asset->id }}" />
                                            <button class="btn btn-sm btn-default" id="bulkEdit"{{ (!$asset->model ? ' disabled' : '') }}{!! (!$asset->model ? ' data-tooltip="true" title="'.trans('admin/hardware/general.model_invalid').'"' : '') !!}><i class="fas fa-children" aria-hidden="true"></i> {{ trans_choice('button.generate_labels', 1) }}</button>

                                            {{ Form::close() }}

                                        </div>
                                        
                                    </div>

                                    @if ($notasEe->count())
    <div class="row" style="margin-top: 25px;">
        <div class="col-md-3">
            <strong>
                📝 Notas dos EE
            </strong>
        </div>
        <div class="col-md-9">
            <ul class="list-group">
                @foreach ($notasEe as $nota)
                    <li class="list-group-item" style="margin-bottom: 8px;">
                        <div>
                            <strong>{{ $nota->created_at->format('d/m/Y H:i') }}</strong>
                            @if ($nota->responsavel)
                                <span class="text-muted"> — {{ $nota->responsavel->nome_completo }}</span>
                            @endif
                        </div>
                        <div style="margin-top: 5px;">{{ $nota->conteudo }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

                                    
                                </div> <!--/end striped container-->
                            </div> <!-- end col-md-9 -->
                        </div> <!--/.row-->
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane fade" id="software">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Licenses assets table -->
                                @if ($asset->licenses->count() > 0)
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4">{{ trans('general.name') }}</th>
                                            
                                            <th class="col-md-4"><span class="line"></span>Data de inicio programa</th>

                                            <th class="col-md-4"><span class="line"></span>Data de término programa</th>

                                            
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($asset->licenseseats as $seat)
                                            @if ($seat->license)
                                                <tr>
                                                    <td><a href="{{ route('licenses.show', $seat->license->id) }}">{{ $seat->license->name }}</a></td>
                                                   
                                                    <td>
                                                        {{ Helper::getFormattedDateObject($seat->license->purchase_date, 'date', false) }}
                                                    </td>

                                                    <td>
                                                        {{ Helper::getFormattedDateObject($seat->license->termination_date, 'date', false) }}
                                                    </td>
                                                  
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else

                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif
                            </div><!-- /col -->
                        </div> <!-- row -->
                    </div> <!-- /.tab-pane software -->

                    <div class="tab-pane fade" id="components">
                        <!-- checked out assets table -->
                        <div class="row">
                            <div class="col-md-12">
                                @if($asset->components->count() > 0)
                                    <table class="table table-striped">
                                        <thead>
                                        <th>{{ trans('general.name') }}</th>
                                        <th>{{ trans('general.qty') }}</th>
                                        <th>{{ trans('general.purchase_cost') }}</th>
                                        <th>{{trans('admin/hardware/form.serial')}}</th>
                                        <th>{{trans('general.checkin')}}</th>
                                        <th></th>
                                        </thead>
                                        <tbody>
                                            <?php $totalCost = 0; ?>
                                        @foreach ($asset->components as $component)


                                            @if (is_null($component->deleted_at))
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('components.show', $component->id) }}">{{ $component->name }}</a>
                                                    </td>
                                                    <td>{{ $component->pivot->assigned_qty }}</td>
                                                    <td>{{ Helper::formatCurrencyOutput($component->purchase_cost) }} each</td>
                                                    <td>{{ $component->serial }}</td>
                                                    <td>
                                                        <a href="{{ route('components.checkin.show', $component->pivot->id) }}" class="btn btn-sm bg-purple" data-tooltip="true">{{ trans('general.checkin') }}</a>
                                                    </td>

                                                        <?php $totalCost = $totalCost + ($component->purchase_cost *$component->pivot->assigned_qty) ?>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>

                                        <tfoot>
                                        <tr>
                                            <td colspan="2">
                                            </td>
                                            <td>{{ $totalCost }}</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @else
                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div> <!-- /.tab-pane components -->


                    <div class="tab-pane fade" id="assets">
                        <div class="row">
                            <div class="col-md-12">

                                @if ($asset->assignedAssets->count() > 0)


                                    {{ Form::open([
                                              'method' => 'POST',
                                              'route' => ['hardware/bulkedit'],
                                              'class' => 'form-inline',
                                               'id' => 'bulkForm']) }}
                                    <div id="toolbar">
                                        <label for="bulk_actions"><span class="sr-only">{{ trans('general.bulk_actions')}}</span></label>
                                        <select name="bulk_actions" class="form-control select2" style="width: 150px;" aria-label="bulk_actions">
                                            <option value="edit">{{ trans('button.edit') }}</option>
                                            <option value="delete">{{ trans('button.delete')}}</option>
                                            <option value="labels">{{ trans_choice('button.generate_labels', 2) }}</option>
                                        </select>
                                        <button class="btn btn-primary" id="{{ (isset($id_button)) ? $id_button : 'bulkAssetEditButton' }}" disabled>{{ trans('button.go') }}</button>
                                    </div>

                                    <!-- checked out assets table -->
                                    <div class="table-responsive">

                                        <table
                                                data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                                                data-cookie-id-table="assetsTable"
                                                data-pagination="true"
                                                data-id-table="assetsTable"
                                                data-search="true"
                                                data-side-pagination="server"
                                                data-show-columns="true"
                                                data-show-fullscreen="true"
                                                data-show-export="true"
                                                data-show-refresh="true"
                                                data-sort-order="asc"
                                                data-bulk-button-id="#bulkAssetEditButton"
                                                id="assetsListingTable"
                                                class="table table-striped snipe-table"
                                                data-url="{{route('api.assets.index',['assigned_to' => $asset->id, 'assigned_type' => 'App\Models\Asset']) }}"
                                                data-export-options='{
                              "fileName": "export-assets-{{ str_slug($asset->name) }}-assets-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>

                                        </table>


                                        {{ Form::close() }}
                                    </div>

                                @else

                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif


                            </div><!-- /col -->
                        </div> <!-- row -->
                    </div> <!-- /.tab-pane software -->


                    <div class="tab-pane fade" id="maintenances">
                        <div class="row">
                            <div class="col-md-12">
                                @can('update', \App\Models\Asset::class)
                                    <div id="maintenance-toolbar">
                                        <a href="{{ route('maintenances.create', ['asset_id' => $asset->id]) }}" class="btn btn-primary">{{ trans('button.add_maintenance') }}</a>
                                    </div>
                                @endcan

                                <!-- Asset Maintenance table -->
                                <table
                                        data-columns="{{ \App\Presenters\AssetMaintenancesPresenter::dataTableLayout() }}"
                                        class="table table-striped snipe-table"
                                        id="assetMaintenancesTable"
                                        data-pagination="true"
                                        data-id-table="assetMaintenancesTable"
                                        data-search="true"
                                        data-side-pagination="server"
                                        data-toolbar="#maintenance-toolbar"
                                        data-show-columns="true"
                                        data-show-fullscreen="true"
                                        data-show-refresh="true"
                                        data-show-export="true"
                                        data-export-options='{
                           "fileName": "export-{{ $asset->asset_tag }}-maintenances",
                           "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                         }'
                                        data-url="{{ route('api.maintenances.index', array('asset_id' => $asset->id)) }}"
                                        data-cookie-id-table="assetMaintenancesTable"
                                        data-cookie="true">
                                </table>
                            </div> <!-- /.col-md-12 -->
                        </div> <!-- /.row -->
                    </div> <!-- /.tab-pane maintenances -->

                    <div class="tab-pane fade" id="history">
                        <!-- checked out assets table -->
                        <div class="row">
                            <div class="col-md-12">
                                <table
                                        class="table table-striped snipe-table"
                                        id="assetHistory"
                                        data-pagination="true"
                                        data-id-table="assetHistory"
                                        data-search="true"
                                        data-side-pagination="server"
                                        data-show-columns="true"
                                        data-show-fullscreen="true"
                                        data-show-refresh="true"
                                        data-sort-order="desc"
                                        data-sort-name="created_at"
                                        data-show-export="true"
                                        data-export-options='{
                         "fileName": "export-asset-{{  $asset->id }}-history",
                         "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                       }'

                                        data-url="{{ route('api.activity.index', ['item_id' => $asset->id, 'item_type' => 'asset']) }}"
                                        data-cookie-id-table="assetHistory"
                                        data-cookie="true">
                                    <thead>
                                    <tr>
                                        <th data-visible="true" data-field="icon" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter">{{ trans('admin/hardware/table.icon') }}</th>
                                        <th data-visible="true" data-field="action_date" data-sortable="true" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
                                        <th data-visible="true" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>
                                        <th data-visible="true" data-field="action_type">{{ trans('general.action') }}</th>
                                        <th class="col-sm-2" data-field="file" data-visible="false" data-formatter="fileUploadNameFormatter">{{ trans('general.file_name') }}</th>
                                        <th data-visible="true" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
                                        <th data-visible="true" data-field="target" data-formatter="polymorphicItemFormatter">{{ trans('general.target') }}</th>
                                        <th data-field="note">{{ trans('general.notes') }}</th>
                                        <th data-field="signature_file" data-visible="false"  data-formatter="imageFormatter">{{ trans('general.signature') }}</th>
                                        <th data-visible="false" data-field="file" data-visible="false"  data-formatter="fileUploadFormatter">{{ trans('general.download') }}</th>
                                        <th data-field="log_meta" data-visible="true" data-formatter="changeLogFormatter">{{ trans('admin/hardware/table.changed')}}</th>
                                        <th data-field="remote_ip" data-visible="false" data-sortable="true">{{ trans('admin/settings/general.login_ip') }}</th>
                                        <th data-field="user_agent" data-visible="false" data-sortable="true">{{ trans('admin/settings/general.login_user_agent') }}</th>
                                        <th data-field="action_source" data-visible="true" data-sortable="true" data-formatter="actionSourceFormatter">{{ trans('general.action_source') }}</th>

                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div> <!-- /.row -->
                    </div> <!-- /.tab-pane history -->

                    <div class="tab-pane fade" id="files">
                        <div class="row">
                            <div class="col-md-12">

                                @if ($asset->uploads->count() > 0)
                                    <table
                                            class="table table-striped snipe-table"
                                            id="assetFileHistory"
                                            data-pagination="true"
                                            data-id-table="assetFileHistory"
                                            data-search="true"
                                            data-side-pagination="client"
                                            data-sortable="true"
                                            data-show-columns="true"
                                            data-show-fullscreen="true"
                                            data-show-refresh="true"
                                            data-sort-order="desc"
                                            data-sort-name="created_at"
                                            data-show-export="true"
                                            data-export-options='{
                         "fileName": "export-asset-{{ $asset->id }}-files",
                         "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                       }'
                                            data-cookie-id-table="assetFileHistory">
                                        <thead>
                                        <tr>
                                            <th data-visible="true" data-field="icon" data-sortable="true">{{trans('general.file_type')}}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="image">{{ trans('general.image') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="filename" data-sortable="true">{{ trans('general.file_name') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="filesize">{{ trans('general.filesize') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="notes" data-sortable="true">{{ trans('general.notes') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="download">{{ trans('general.download') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="created_at" data-sortable="true">{{ trans('general.created_at') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="actions">{{ trans('table.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($asset->uploads as $file)
                                            <tr>
                                                <td><i class="{{ Helper::filetype_icon($file->filename) }} icon-med" aria-hidden="true"></i></td>
                                                <td>
                                                    @if ( Helper::checkUploadIsImage($file->get_src('assets')))
                                                        <a href="{{ route('show/assetfile', ['assetId' => $asset->id, 'fileId' =>$file->id]) }}" data-toggle="lightbox" data-type="image" data-title="{{ $file->filename }}" data-footer="{{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}">
                                                            <img src="{{ route('show/assetfile', ['assetId' => $asset->id, 'fileId' =>$file->id]) }}" style="max-width: 50px;">
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (Storage::exists('private_uploads/assets/'.$file->filename))
                                                        {{ $file->filename }}
                                                    @else
                                                        <del>{{ $file->filename }}</del>
                                                    @endif
                                                </td>
                                                <td data-value="{{ (Storage::exists('private_uploads/assets/'.$file->filename) ? Storage::size('private_uploads/assets/'.$file->filename) : '') }}">
                                                    {{ @Helper::formatFilesizeUnits(Storage::exists('private_uploads/assets/'.$file->filename) ? Storage::size('private_uploads/assets/'.$file->filename) : '') }}
                                                </td>
                                                <td>
                                                    @if ($file->note)
                                                        {{ $file->note }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (($file->filename) && (Storage::exists('private_uploads/assets/'.$file->filename)))
                                                        <a href="{{ route('show/assetfile', [$asset->id, $file->id, 'download'=>'true']) }}" class="btn btn-sm btn-default">
                                                            <i class="fas fa-download" aria-hidden="true"></i>
                                                        </a>

                                                        <a href="{{ route('show/assetfile', [$asset->id, $file->id, 'inline'=>'true']) }}" class="btn btn-sm btn-default" target="_blank">
                                                            <i class="fa fa-external-link" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($file->created_at)
                                                        {{ Helper::getFormattedDateObject($file->created_at, 'datetime', false) }}
                                                    @endif
						</td>


                                                <td>
                                                    @can('update', \App\Models\Asset::class)
                                                        <a class="btn delete-asset btn-sm btn-danger btn-sm" href="{{ route('delete/assetfile', [$asset->id, $file->id]) }}" data-tooltip="true" data-title="Delete" data-content="{{ trans('general.delete_confirm', ['item' => $file->filename]) }}"><i class="fas fa-trash icon-white" aria-hidden="true"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                @else

                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif

                            </div> <!-- /.col-md-12 -->
                        </div> <!-- /.row -->
                    </div> <!-- /.tab-pane files -->

                    @can('view', $asset->model)
                    <div class="tab-pane fade" id="modelfiles">
                        <div class="row">
                            <div class="col-md-12">

                                @if (($asset->model) && ($asset->model->uploads->count() > 0))
                                    <table
                                            class="table table-striped snipe-table"
                                            id="assetModelFileHistory"
                                            data-pagination="true"
                                            data-id-table="assetModelFileHistory"
                                            data-search="true"
                                            data-side-pagination="client"
                                            data-sortable="true"
                                            data-show-columns="true"
                                            data-show-fullscreen="true"
                                            data-show-refresh="true"
                                            data-sort-order="desc"
                                            data-sort-name="created_at"
                                            data-show-export="true"
                                            data-export-options='{
                         "fileName": "export-assetmodel-{{ $asset->model->id }}-files",
                         "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                       }'
                                            data-cookie-id-table="assetFileHistory">
                                        <thead>
                                        <tr>
                                            <th data-visible="true" data-field="icon" data-sortable="true">{{trans('general.file_type')}}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="image">{{ trans('general.image') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="filename" data-sortable="true">{{ trans('general.file_name') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="filesize">{{ trans('general.filesize') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="notes" data-sortable="true">{{ trans('general.notes') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="download">{{ trans('general.download') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="created_at" data-sortable="true">{{ trans('general.created_at') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="actions">{{ trans('table.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($asset->model->uploads as $file)
                                            <tr>
                                                <td><i class="{{ Helper::filetype_icon($file->filename) }} icon-med" aria-hidden="true"></i></td>
                                                <td>
                                                    @if ( Helper::checkUploadIsImage($file->get_src('assetmodels')))
                                                        <a href="{{ route('show/modelfile', ['modelID' => $asset->model->id, 'fileId' =>$file->id]) }}" data-toggle="lightbox" data-type="image" data-title="{{ $file->filename }}" data-footer="{{ Helper::getFormattedDateObject($asset->last_checkout, 'datetime', false) }}">
                                                            <img src="{{ route('show/modelfile', ['modelID' => $asset->model->id, 'fileId' =>$file->id]) }}" style="max-width: 50px;">
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (Storage::exists('private_uploads/assetmodels/'.$file->filename))
                                                        {{ $file->filename }}
                                                    @else
                                                        <del>{{ $file->filename }}</del>
                                                    @endif
                                                </td>
                                                <td data-value="{{ (Storage::exists('private_uploads/assetmodels/'.$file->filename)) ? Storage::size('private_uploads/assetmodels/'.$file->filename) : '' }}">
                                                    {{ (Storage::exists('private_uploads/assetmodels/'.$file->filename)) ? Helper::formatFilesizeUnits(Storage::size('private_uploads/assetmodels/'.$file->filename)) : '' }}
                                                </td>
                                                <td>
                                                    @if ($file->note)
                                                        {{ $file->note }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (($file->filename) && (Storage::exists('private_uploads/assetmodels/'.$file->filename)))
                                                        <a href="{{ route('show/modelfile', [$asset->model->id, $file->id]) }}" class="btn btn-sm btn-default">
                                                            <i class="fas fa-download" aria-hidden="true"></i>
                                                        </a>

                                                        <a href="{{ route('show/modelfile', [$asset->model->id, $file->id, 'inline'=>'true']) }}" class="btn btn-sm btn-default" target="_blank">
                                                            <i class="fa fa-external-link" aria-hidden="true"></i>
                                                        </a>

                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($file->created_at)
                                                        {{ Helper::getFormattedDateObject($file->created_at, 'datetime', false) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('update', \App\Models\AssetModel::class)
                                                        <a class="btn delete-asset btn-sm btn-danger btn-sm" href="{{ route('delete/modelfile', [$asset->model->id, $file->id]) }}" data-tooltip="true" data-title="Delete" data-content="{{ trans('general.delete_confirm', ['item' => $file->filename]) }}"><i class="fas fa-trash icon-white" aria-hidden="true"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                @else

                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif

                            </div> <!-- /.col-md-12 -->
                        </div> <!-- /.row -->
                    </div> <!-- /.tab-pane files -->
                    @endcan
            </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->
    </div>

<div class="modal fade" id="preferencesModal" tabindex="-1" role="dialog" aria-labelledby="preferencesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="preferencesModalLabel">Gerir Preferências de Notificação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Formulário -->
            <form id="preferences-form" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT"> <!-- Simula PUT -->

                <div class="modal-body">
                    <!-- Mensagem de alerta -->
                    <div id="preferences-alert" class="alert d-none" role="alert"></div>

                    <!-- Checkbox 1 -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="receive_checkin_notifications" name="receive_checkin_notifications" value="1"
                               {{ $asset->receive_checkin_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="receive_checkin_notifications">Receber Notificações de Check-in</label>
                    </div>

                    <!-- Checkbox 2 -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="receive_checkout_notifications" name="receive_checkout_notifications" value="1"
                               {{ $asset->receive_checkout_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="receive_checkout_notifications">Receber Notificações de Check-out</label>
                    </div>

                    <!-- Checkbox 3 -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="receive_self_notifications" name="receive_self_notifications" value="1"
                               {{ $asset->receive_self_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="receive_self_notifications">Receber Notificações de Ações Próprias</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="save-preferences" class="btn btn-primary">Salvar Preferências</button>
                </div>
            </form>
        </div>
    </div>
</div>





    @can('update', \App\Models\Asset::class)
        @include ('modals.upload-file', ['item_type' => 'asset', 'item_id' => $asset->id])
    @endcan
@stop

            @section('moar_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const preferencesForm = document.getElementById('preferences-form');
    const saveButton = document.getElementById('save-preferences');
    const alertBox = document.getElementById('preferences-alert');

    // AJAX para salvar preferências
    saveButton.addEventListener('click', function () {
        const formData = new FormData(preferencesForm);

        fetch("{{ route('preferences.update.single', $asset->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertBox.className = 'alert alert-success';
                alertBox.textContent = data.message;
                alertBox.classList.remove('d-none');
                setTimeout(() => alertBox.classList.add('d-none'), 3000);
            } else {
                throw new Error(data.message || 'Erro ao salvar preferências.');
            }
        })
        .catch(error => {
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = error.message;
            alertBox.classList.remove('d-none');
            setTimeout(() => alertBox.classList.add('d-none'), 3000);
        });
    });

    // Limpar mensagens ao abrir o modal
    const preferencesModal = document.getElementById('preferencesModal');
    if (preferencesModal) {
        preferencesModal.addEventListener('shown.bs.modal', function () {
            if (alertBox) {
                alertBox.classList.add('d-none');
                alertBox.textContent = '';
            }
        });
    }
});

function actionSourceFormatter(value, row) {
    return row.action_source_label || '-';
  }
</script>
    @include ('partials.bootstrap-table')

@stop
