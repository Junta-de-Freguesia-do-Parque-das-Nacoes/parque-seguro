@if ($setupCompleted = \App\Models\Setting::setupCompleted())
@component('mail::message')
@endif

{{ trans('mail.test_mail_text') }}

Thanks,
Parque Seguro
@if ($setupCompleted)
@endcomponent
@endif
