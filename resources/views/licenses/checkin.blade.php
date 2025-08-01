@extends('layouts/default')

{{-- Page title --}}
@section('title')
     {{ trans('Devolver a Vaga da Inscrição') }}
@parent
@stop


@section('header_right')
    <a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
        {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-7">
            <form class="form-horizontal" method="post" action="{{ route('licenses.checkin.save', ['licenseId'=>$licenseSeat->id, 'backTo'=>$backto] ) }}" autocomplete="off">
                {{csrf_field()}}

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h2 class="box-title"> {{ $licenseSeat->license->name }}</h2>
                    </div>
                    <div class="box-body">

            <!-- license name -->
            <div class="form-group">
                <label class="col-sm-2 control-label">{{ trans('admin/hardware/form.name') }}</label>
                <div class="col-md-6">
                    <p class="form-control-static">{{ $licenseSeat->license->name }}</p>
                </div>
            </div>

            <!-- Serial 
            <div class="form-group">
                <label class="col-sm-2 control-label">{{ trans('admin/licenses/form.license_key') }}</label>
                <div class="col-md-6">
                    <p class="form-control-static">
                        @can('viewKeys', $licenseSeat->license)
                            {{ $licenseSeat->license->serial }}
                        @else
                            ------------
                        @endcan
                        </p>
                </div>
            </div>
			-->
            <!-- Note -->
            <div class="form-group {{ $errors->has('notes') ? 'error' : '' }}">
                <label for="note" class="col-md-2 control-label">{{ trans('admin/hardware/form.notes') }}</label>
                <div class="col-md-7">
                    <textarea class="col-md-6 form-control" id="notes" name="notes"></textarea>
                    {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                </div>
            </div>
                        <x-redirect_submit_options
                                index_route="licenses.index"
                                :button_label="trans('Devolver vaga')"
                                :options="[
                                'index' => trans('Voltar para todas as inscrições', ['type' => trans('general.licenses')]),
                                'item' => trans('Voltar para a inscrição', ['type' => trans('')]),
                               ]"
                        />
                    </div> <!-- /.box-->
            </form>
        </div> <!-- /.col-md-7-->
    </div>


@stop
