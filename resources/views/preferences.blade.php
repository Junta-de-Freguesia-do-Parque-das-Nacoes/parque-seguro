@extends('layouts.default')

@section('title', 'Configurar Preferências')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header text-center py-3">
            <h1 class="h4 mb-0 fw-bold">Configurar Preferências de Notificações</h1>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('preferences.verifyAndUpdate', $asset->id) }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="verificationCode">Código de Verificação:</label>
                    <input type="text" name="verification_code" id="verificationCode" class="form-control" required>
                    <small class="form-text text-muted">
                        Insira o código de verificação enviado para o seu e-mail.
                    </small>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="checkinNotifications" name="receive_checkin_notifications" 
                        {{ $asset->receive_checkin_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="checkinNotifications">Receber notificações de Check-in</label>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="checkoutNotifications" name="receive_checkout_notifications" 
                        {{ $asset->receive_checkout_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="checkoutNotifications">Receber notificações de Check-out</label>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="selfNotifications" name="receive_self_notifications" 
                        {{ $asset->receive_self_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="selfNotifications">Receber notificações das próprias ações</label>
                </div>

                <button type="submit" class="btn btn-success w-100">Salvar Preferências</button>
            </form>

            <form action="{{ route('preferences.sendCode', $asset->id) }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-warning w-100">Enviar Código de Verificação</button>
            </form>
        </div>
    </div>
</div>
@endsection
