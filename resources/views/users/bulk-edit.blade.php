@extends('layouts/default')
{{-- Page title --}}
@section('title')
    {{ trans('general.bulk_edit') }}
    @parent
@stop

@section('header_right')
    <a href="{{ URL::previous() }}" class="btn btn-sm btn-primary pull-right">
        {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')

    <style>
        .radio {
            margin-left: -20px;
        }
    </style>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <p>{{ trans('admin/users/general.bulk_update_help') }}</p>

            <div class="callout callout-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                {{ trans('admin/users/general.bulk_update_warn', ['user_count' => count($users)]) }}
            </div>

            <form class="form-horizontal" method="post" action="{{ route('users/bulkeditsave') }}" autocomplete="off" role="form">
                {{ csrf_field() }}

                <div class="box box-default">
                    <div class="box-body">

                        <!-- Department -->
                        @include ('partials.forms.edit.department-select', ['translated_name' => trans('general.department'), 'fieldname' => 'department_id'])

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <label class="form-control">
                                    {{ Form::checkbox('null_department_id', '1', false) }}
                                    {{ trans_choice('general.set_users_field_to_null', count($users), ['field' => trans('general.department'), 'user_count' => count($users)]) }}
                                </label>
                            </div>
                        </div>

                        <!-- Location -->
                        @include ('partials.forms.edit.location-select', ['translated_name' => trans('general.location'), 'fieldname' => 'location_id'])

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <label class="form-control">
                                    {{ Form::checkbox('null_location_id', '1', false) }}
                                    {{ trans_choice('general.set_users_field_to_null', count($users), ['field' => trans('general.location'), 'user_count' => count($users)]) }}
                                </label>
                            </div>
                        </div>

                        <!-- Company -->
                        @if (\App\Models\Company::canManageUsersCompanies())
                            @include ('partials.forms.edit.company-select', ['translated_name' => trans('general.select_company'), 'fieldname' => 'company_id'])

                            <div class="form-group">
                                <div class="col-md-9 col-md-offset-3">
                                    <label class="form-control">
                                        {{ Form::checkbox('null_company_id', '1', false) }}
                                        {{ trans_choice('general.set_users_field_to_null', count($users), ['field' => trans('general.company'), 'user_count' => count($users)]) }}
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- Manager -->
                        @include ('partials.forms.edit.user-select', ['translated_name' => trans('admin/users/table.manager'), 'fieldname' => 'manager_id'])

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <label class="form-control">
                                    {{ Form::checkbox('null_manager_id', '1', false) }}
                                    {{ trans_choice('general.set_users_field_to_null', count($users), ['field' => trans('admin/users/table.manager'), 'user_count' => count($users)]) }}
                                </label>
                            </div>
                        </div>

                        <!-- Language (sem select2) -->
                        <div class="form-group {{ $errors->has('locale') ? 'has-error' : '' }}">
                            <label class="col-md-3 control-label" for="locale">{{ trans('general.language') }}</label>
                            <div class="col-md-8">
                                <select name="locale" class="form-control">
                                    @foreach (config('app.locales') as $key => $value)
                                        <option value="{{ $key }}" {{ old('locale', $user->locale) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                {!! $errors->first('locale', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                            <label class="col-md-3 control-label" for="city">{{ trans('general.city') }}</label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="city" id="city" aria-label="city" />
                                {!! $errors->first('city', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                            </div>
                        </div>

                        <!-- Remote -->
                        <div class="form-group">
                            <div class="col-sm-3 control-label">
                                {{ trans('admin/users/general.remote') }}
                            </div>
                            <div class="col-sm-9">
                                <label class="form-control">
                                    {{ Form::radio('remote', '', true) }} {{ trans('general.do_not_change') }}
                                </label>
                                <label class="form-control">
                                    {{ Form::radio('remote', '1', old('remote')) }} {{ trans('admin/users/general.remote_label') }}
                                </label>
                                <label class="form-control">
                                    {{ Form::radio('remote', '0', old('remote')) }} {{ trans('admin/users/general.not_remote_label') }}
                                </label>
                            </div>
                        </div>

                        <!-- LDAP Sync -->
                        <div class="form-group">
                            <div class="col-sm-3 control-label">
                                {{ trans('general.ldap_sync') }}
                            </div>
                            <div class="col-sm-9">
                                <label class="form-control">
                                    {{ Form::radio('ldap_import', '', true) }} {{ trans('general.do_not_change') }}
                                </label>
                                <label class="form-control">
                                    {{ Form::radio('ldap_import', '0', old('ldap_import')) }} {{ trans('general.ldap_import') }}
                                </label>
                            </div>
                        </div>

                        <!-- Login Enabled -->
                        <div class="form-group">
                            <div class="col-sm-3 control-label">
                                {{ trans('general.login_enabled') }}
                            </div>
                            <div class="col-sm-9">
                                <label class="form-control">
                                    {{ Form::radio('activated', '', true) }} {{ trans('general.do_not_change') }}
                                </label>
                                <label class="form-control">
                                    {{ Form::radio('activated', '1', old('activated')) }} {{ trans('admin/users/general.user_activated') }}
                                </label>
                                <label class="form-control">
                                    {{ Form::radio('activated', '0', old('activated')) }} {{ trans('admin/users/general.user_deactivated') }}
                                </label>
                            </div>
                        </div>

                        <!-- Groups -->
                        <div class="form-group{{ $errors->has('groups') ? ' has-error' : '' }}">
                            <label class="col-md-3 control-label" for="groups"> {{ trans('general.groups') }}</label>
                            <div class="col-md-6">
                                <div class="controls">
                                    <select name="groups[]" id="groups" multiple="multiple" class="form-control">
                                        <option value="">{{ trans('admin/users/general.remove_group_memberships') }}</option>
                                        @foreach ($groups as $id => $group)
                                            <option value="{{ $id }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">{{ trans('admin/users/table.groupnotes') }}</span>
                                </div>
                            </div>
                        </div>

                        @if (!empty($users) && is_array($users))
    @foreach ($users as $user)
        <input type="hidden" name="ids[{{ $user->id }}]" value="{{ $user->id }}">
    @endforeach
@endif


                    </div>

                    <div class="box-footer text-right">
                        <a class="btn btn-link pull-left" href="{{ URL::previous() }}">{{ trans('button.cancel') }}</a>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check icon-white"></i> {{ trans('general.update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
