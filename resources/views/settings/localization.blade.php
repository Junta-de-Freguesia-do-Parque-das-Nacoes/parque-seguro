@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/settings/general.localization_title') }}
    @parent
@stop

@section('header_right')
    <a href="{{ route('settings.index') }}" class="btn btn-primary"> {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')

    <style>
        .checkbox label {
            padding-right: 40px;
        }
    </style>

    {{ Form::open(['method' => 'POST', 'files' => false, 'autocomplete' => 'off', 'class' => 'form-horizontal', 'role' => 'form']) }}
    <!-- CSRF Token -->
    {{ csrf_field() }}

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h2 class="box-title">
                        <i class="fas fa-globe-americas" aria-hidden="true"></i> {{ trans('admin/settings/general.localization') }}
                    </h2>
                </div>
                <div class="box-body">
                    <div class="col-md-12">

                        <!-- Language -->
                        <div class="form-group {{ $errors->has('locale') ? 'error' : '' }}">
                            <div class="col-md-3 col-xs-12">
                                {{ Form::label('locale', trans('admin/settings/general.default_language')) }}
                            </div>
                            <div class="col-md-5 col-xs-12">
                                {{ Form::select('locale', config('app.available_locales') ?? [], old('locale', $setting->locale), ['class' => 'form-control']) }}
                                {!! $errors->first('locale', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                            </div>
                        </div>

                        <!-- Name Display Format -->
                        <div class="form-group {{ $errors->has('name_display_format') ? 'error' : '' }}">
                            <div class="col-md-3 col-xs-12">
                                {{ Form::label('name_display_format', trans('general.name_display_format')) }}
                            </div>
                            <div class="col-md-5 col-xs-12">
                                {{ Form::select('name_display_format', [
                                    'first_last' => trans('general.first_last'),
                                    'last_first' => trans('general.last_first')
                                ], old('name_display_format', $setting->name_display_format), ['class' => 'form-control']) }}
                                {!! $errors->first('name_display_format', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                            </div>
                        </div>

                        <!-- Date & Time Format -->
                        <div class="form-group {{ $errors->has('time_display_format') ? 'error' : '' }}">
                            <div class="col-md-3 col-xs-12">
                                {{ Form::label('date_display_format', trans('general.time_and_date_display')) }}
                            </div>
                            <div class="col-md-5 col-xs-12">
                                {{ Form::select('date_display_format', config('app.date_formats') ?? [], old('date_display_format', $setting->date_display_format), ['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-3 col-xs-12">
                                {{ Form::select('time_display_format', config('app.time_formats') ?? [], old('time_display_format', $setting->time_display_format), ['class' => 'form-control']) }}
                            </div>
                            {!! $errors->first('time_display_format', '<div class="col-md-9 col-md-offset-3"><span class="alert-msg" aria-hidden="true">:message</span></div>') !!}
                        </div>

                        <!-- Currency -->
                        <div class="form-group {{ $errors->has('default_currency') ? 'error' : '' }}">
                            <div class="col-md-3 col-xs-12">
                                {{ Form::label('default_currency', trans('admin/settings/general.default_currency')) }}
                            </div>
                            <div class="col-md-9 col-xs-12">
                                {{ Form::text('default_currency', old('default_currency', $setting->default_currency), ['class' => 'form-control', 'placeholder' => 'USD', 'maxlength' => '3', 'style' => 'width: 60px; display: inline-block;']) }}

                                {{ Form::select('digit_separator', [
                                    'comma' => trans('admin/settings/general.separator_comma'),
                                    'dot' => trans('admin/settings/general.separator_dot')
                                ], old('digit_separator', $setting->digit_separator), ['class' => 'form-control', 'style' => 'width: auto; display: inline-block;']) }}

                                {!! $errors->first('default_currency', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                            </div>
                        </div>

                    </div>
                </div> <!--/.box-body-->

                <div class="box-footer">
                    <div class="text-left col-md-6">
                        <a class="btn btn-link text-left" href="{{ route('settings.index') }}">{{ trans('button.cancel') }}</a>
                    </div>
                    <div class="text-right col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.save') }}
                        </button>
                    </div>
                </div>
            </div> <!-- /box -->
        </div> <!-- /.col-md-8-->
    </div> <!-- /.row-->

    {{ Form::close() }}

@stop
