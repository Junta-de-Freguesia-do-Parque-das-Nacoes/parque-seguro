@extends('layouts/default', [
    'helpText' => trans('admin/custom_fields/general.about_fieldsets_text'),
    'helpPosition' => 'right',
])

{{-- Page title --}}
@section('title')
  {{ trans('admin/custom_fields/general.custom_fields') }}
@parent
@stop

@section('content')

@section('header_right')
<a href="{{ route('fields.index') }}" class="btn btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')

    <!-- Horizontal Form -->
    @if ($field->id)
        {{ Form::open(['route' => ['fields.update', $field->id], 'class'=>'form-horizontal']) }}
        {{ method_field('PUT') }}
    @else
        {{ Form::open(['route' => 'fields.store', 'class'=>'form-horizontal']) }}
    @endif

    @csrf
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
        <div class="box-header with-border text-right">
            <button type="submit" class="btn btn-primary"> {{ trans('general.save') }}</button>
        </div>
      <div class="box-body">

          <div class="col-md-8">

          <!-- Name -->
          <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-3 control-label">
              {{ trans('admin/custom_fields/general.field_name') }}
            </label>
            <div class="col-md-8 required">
                {{ Form::text('name', old('name', $field->name), array('class' => 'form-control', 'aria-label'=>'name')) }}
                {!! $errors->first('name', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

          <!-- Element Type -->
          <div class="form-group {{ $errors->has('element') ? ' has-error' : '' }}">
            <label for="element" class="col-md-3 control-label">
              {{ trans('admin/custom_fields/general.field_element') }}
            </label>
            <div class="col-md-8 required">
                <select name="element" class="field_element select2 form-control" aria-label="element">
                    @php
                        $elements = [
                            'text' => trans('admin/custom_fields/general.text'),
                            'textarea' => trans('admin/custom_fields/general.textarea'),
                            'select' => trans('admin/custom_fields/general.select'),
                            'radio' => trans('admin/custom_fields/general.radio'),
                            'checkbox' => trans('admin/custom_fields/general.checkbox'),
                        ];
                    @endphp
                    @foreach ($elements as $key => $label)
                        <option value="{{ $key }}" {{ old('element', $field->element) == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('element', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

          <!-- Element values -->
          <div class="form-group {{ $errors->has('field_values') ? ' has-error' : '' }}" id="field_values_text">
            <label for="field_values" class="col-md-3 control-label">
              {{ trans('admin/custom_fields/general.field_values') }}
            </label>
            <div class="col-md-8 required">
              {!! Form::textarea('field_values', old('field_values', $field->field_values), ['style' => 'width: 100%', 'rows' => 4, 'class' => 'form-control', 'aria-label'=>'field_values']) !!}
              {!! $errors->first('field_values', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
              <p class="help-block">{{ trans('admin/custom_fields/general.field_values_help') }}</p>
            </div>
          </div>

          <!-- Format -->
          <div class="form-group {{ $errors->has('format') ? ' has-error' : '' }}" id="format_values">
            <label for="format" class="col-md-3 control-label">
              {{ trans('admin/custom_fields/general.field_format') }}
            </label>
              @php
              $field_format = '';
              if (stripos($field->format, 'regex') === 0){
                $field_format = 'CUSTOM REGEX';
              }
              @endphp
            <div class="col-md-8 required">
              {{ Form::select("format",Helper::predefined_formats(), ($field_format == '') ? $field->format : $field_format, array('class'=>'format select2 form-control', 'aria-label'=>'format', 'style' => 'width:100%;')) }}
              {!! $errors->first('format', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

          <!-- Custom Format -->
          <div class="form-group {{ $errors->has('custom_format') ? ' has-error' : '' }}" id="custom_regex" style="display:none;">
            <label for="custom_format" class="col-md-3 control-label">
              {{ trans('admin/custom_fields/general.field_custom_format') }}
            </label>
            <div class="col-md-8 required">
                {{ Form::text('custom_format', old('custom_format', (($field->format!='') && (stripos($field->format,'regex')===0)) ? $field->format : ''), array('class' => 'form-control', 'id' => 'custom_format','aria-label'=>'custom_format', 'placeholder'=>'regex:/^[0-9]{15}$/')) }}
                <p class="help-block">{!! trans('admin/custom_fields/general.field_custom_format_help') !!}</p>
                {!! $errors->first('custom_format', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

          <!-- Help Text -->
          <div class="form-group {{ $errors->has('help_text') ? ' has-error' : '' }}">
              <label for="help_text" class="col-md-3 control-label">
                  {{ trans('admin/custom_fields/general.help_text') }}
              </label>
              <div class="col-md-8">
                  {{ Form::text('help_text', old('help_text', $field->help_text), array('class' => 'form-control', 'aria-label'=>'help_text')) }}
                  <p class="help-block">{{ trans('admin/custom_fields/general.help_text_description') }}</p>
                  {!! $errors->first('help_text', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
              </div>
          </div>
        </div>
      </div>

      <div class="box-footer text-right">
        <button type="submit" class="btn btn-primary"> {{ trans('general.save') }}</button>
      </div>

    </div> <!--.box.box-default-->
  </div>
</div>
{{ Form::close() }}
@stop

@section('moar_scripts')
<script nonce="{{ csrf_token() }}">
    $(document).ready(function(){
        $(".format").change(function(){
            if ($(this).val() === 'CUSTOM REGEX') {
                $("#custom_regex").show();
            } else {
                $("#custom_regex").hide();
            }
        }).change();

        $(".field_element").change(function(){
            const value = $(this).val();
            if (value === 'radio' || value === 'checkbox') {
                $("#format_values").hide();
            } else {
                $("#format_values").show();
            }
        }).change();
    });
</script>
@stop
