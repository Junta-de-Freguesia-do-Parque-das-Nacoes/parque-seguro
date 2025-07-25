@extends('layouts/default')

@section('content')
<div class="container text-center mt-5">
    <img src="{{ asset('apk/ac.png') }}" alt="Logo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">

    <h2 class="mt-4">Download da App Parque Seguro</h2>

    <a href="{{ asset('apk/app-parqueseguro.apk') }}" class="btn btn-success btn-lg mt-3">
        📲 Descarregar APK
    </a>

    <div class="mt-4">
        <p>Ou digitaliza o QR Code:</p>
        <img src="{{ asset('apk/qrcode_app_parqueseguro.png') }}" alt="QR Code" style="max-width: 250px;">
    </div>
</div>
@endsection
