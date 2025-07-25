@extends('layouts.default')

@section('title', 'Editar Preferências de Notificação')

@section('content')
<div class="container">
    <h2>Editar Preferências de Notificação</h2>

    {{-- Mensagem de sucesso ou erro --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Formulário de edição de preferências --}}
    <form action="{{ route('preferences.update', $asset->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>
                <input type="checkbox" name="receive_checkin_notifications" value="1" 
                       {{ $asset->receive_checkin_notifications ? 'checked' : '' }}>
                Receber notificações de Check-in
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="receive_checkout_notifications" value="1" 
                       {{ $asset->receive_checkout_notifications ? 'checked' : '' }}>
                Receber notificações de Check-out
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="receive_self_notifications" value="1" 
                       {{ $asset->receive_self_notifications ? 'checked' : '' }}>
                Receber notificações das próprias ações
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
