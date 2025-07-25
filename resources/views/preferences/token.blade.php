@extends('layouts.default')

@section('title', 'Alterar Preferências de Notificação')

@section('content')
<div class="container py-4">

    <!-- Contêiner para alertas -->
    <div class="alert-container text-center" style="height: 80px; position: relative; margin-bottom: 20px;">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="display: inline-block; max-width: 600px; margin: auto;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: inline-block; max-width: 600px; margin: auto;">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-header text-center py-3">
            <h1 class="h4 mb-0 fw-bold">Alterar Preferências</h1>
        </div>

        <!-- Caixa Informativa -->
        <div class="alert alert-info mb-4" role="alert">
            <strong>Instruções:</strong> Para garantir a segurança dos seus dados, é necessário verificar seu email antes de atualizar as preferências de notificação. Clique em "Enviar Código" para receber o código no email associado e insira-o no campo indicado para continuar.
        </div>

        <div class="card-body">
            <!-- Formulário para enviar o código de verificação -->
            <form action="{{ route('preferences.sendCode', ['id' => $asset->id]) }}" method="POST" class="mb-4">
                @csrf
                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-warning">Enviar Código para o Email</button>
                </div>
            </form>
            <hr class="my-4">

            <!-- Formulário para alterar as preferências -->
            <form action="{{ route('preferences.token.update', ['id' => $asset->id, 'token' => $token]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="verification_code" class="form-label">Código de Verificação:</label>
                    <input type="text" id="verification_code" name="verification_code" class="form-control form-control-sm" style="width: 200px; margin-bottom: 10px;" required>
                    @error('verification_code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="receive_checkin_notifications" name="receive_checkin_notifications" value="1" {{ $asset->receive_checkin_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="receive_checkin_notifications">
                        Receber notificações de Entrada
                    </label>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="receive_checkout_notifications" name="receive_checkout_notifications" value="1" {{ $asset->receive_checkout_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="receive_checkout_notifications">
                        Receber notificações de Saída
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="receive_self_notifications" name="receive_self_notifications" value="1" {{ $asset->receive_self_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="receive_self_notifications">
                        Receber notificações mesmo quando sou eu a entregar ou recolher a criança
                    </label>
                </div>

                <!-- Mensagem informativa -->
                <div id="selfNotificationsInfo" class="alert alert-info mt-3" style="display: none;">
                    <strong>Aviso:</strong> A opção "Receber notificações das próprias ações" só está disponível se a opção "Receber notificações de Saída" estiver selecionada.
                </div>

                <button type="submit" class="btn btn-success w-100">Salvar Preferências</button>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutCheckbox = document.getElementById('receive_checkout_notifications');
        const selfCheckbox = document.getElementById('receive_self_notifications');
        const selfNotificationsInfo = document.getElementById('selfNotificationsInfo');

        // Função que desabilita ou habilita o checkbox "Receber notificações mesmo quando sou eu a entregar ou recolher a criança"
        function toggleSelfNotificationsState() {
            // Se "Receber notificações de Saída" não estiver marcado, desabilita o "Receber notificações das próprias ações"
            if (!checkoutCheckbox.checked) {
                selfCheckbox.disabled = true;
                selfCheckbox.checked = false; // Desmarca o checkbox
                selfNotificationsInfo.style.display = 'block'; // Exibe a mensagem
            } else {
                selfCheckbox.disabled = false;
                selfNotificationsInfo.style.display = 'none'; // Oculta a mensagem
            }
        }

        // Inicializa o estado do checkbox quando a página for carregada
        toggleSelfNotificationsState();

        // Adiciona o evento de mudança ao checkbox "Receber notificações de Saída"
        checkoutCheckbox.addEventListener('change', toggleSelfNotificationsState);
    });
</script>
