@extends('layouts.ee-master')
@section('title', 'Área do Encarregado de Educação')

@if(session('force_reload'))
    <script>
        window.location.reload();
    </script>
@endif

@section('content')

<div class="card-custom" style="padding: 15px;">
   
{{-- Banner RGPD e Privacidade --}}
@if (!session('rgpd_consent_ee'))
    <div id="aviso-rgpd" style="
        position: fixed;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 10000;
        font-size: 1.3rem; /* Ligeiramente ajustado para mais texto */
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    ">
        <div style="text-align: justify; width: 100%;">
            🔐 <strong>Aviso de Proteção de Dados e Cookies</strong>
            Recordamos que, ao efetuar a inscrição, consentiu com o tratamento dos seus dados pessoais e dos seus educandos pela Freguesia do Parque das Nações (FPN). Este tratamento destina-se à gestão da inscrição, segurança dos menores (“Parque Seguro”), comunicações institucionais e gestão dos programas da FPN, em conformidade com o RGPD. Pode exercer os seus direitos (acesso, retificação, etc.) contactando o nosso Encarregado da Proteção de Dados através do e-mail <a href="mailto:epd@jf-parquedasnacoes.pt" style="text-decoration: underline;">epd@jf-parquedasnacoes.pt</a>.
            <br><br>
            Este portal utiliza cookies para otimizar a sua navegação. Para informações detalhadas sobre todo o tratamento de dados, incluindo os termos que aceitou na inscrição, consulte a nossa
            <a href="https://www.jf-parquedasnacoes.pt/protecao-de-dados" target="_blank" style="text-decoration: underline;">Política de Proteção de Dados completa</a>.
            Ao clicar em "Compreendi e Aceitar", confirma a sua concordância com a utilização de cookies e os nossos
            <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" style="text-decoration: underline;">Termos e Condições</a> de utilização deste portal.
        </div>
        <button onclick="aceitarRgpd()" style="
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px; /* Ligeiramente maior */
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.4rem;
            align-self: flex-end;
        ">
            Compreendi e Aceito
        </button>
    </div>
@endif


    
    {{-- Loop dos Educandos --}}
    @foreach ($educandos as $utente)
<div class="card-utente">
<div class="utente-header">
    <div class="foto-wrapper">
        <!-- Nome no topo no mobile -->
        <div class="utente-nome nome-mobile">{{ $utente->name }}</div>

        <!-- Foto do utente -->
        <img src="{{ $utente->image ? route('ee.utente.foto', ['filename' => $utente->image]) : asset('img/anoninochild.jpg') }}"
             alt="Foto de {{ $utente->name }}"
             class="utente-foto"
             id="foto-preview-{{ $utente->id }}">

        @if (!$utente->image)
        <div class="alerta-sem-foto">
            ⚠️ Para maior segurança, adicione uma foto do educando.
            <br> Facilita a identificação nas recolhas.
        </div>
        @endif

        <!-- Controles: botão de upload + badge -->
        <div class="foto-controles">
            <input type="file" class="input-foto" accept="image/*" id="input-foto-{{ $utente->id }}" hidden>
            <button type="button" class="btn-utente verde" onclick="document.getElementById('input-foto-{{ $utente->id }}').click();">
                📤 Atualizar Foto
            </button>
            <div class="badge-foto-sucesso" id="foto-sucesso-{{ $utente->id }}">✅ Foto atualizada</div>

            <!-- QR Code fica por baixo do botão -->
            <img src="data:image/png;base64,{{ $qrcodes[$utente->id] ?? '' }}"
     alt="QR Code do utente"
     class="img-thumbnail qr-thumbnail"
     onclick="abrirModalQr({{ $utente->id }}, '{{ addslashes($utente->name) }}')">

        </div>
    </div>

    <div class="col-info">

        @php
            $idade = $utente->_snipeit_data_nascimento_34 ? Carbon::parse($utente->_snipeit_data_nascimento_34)->age : '';
        @endphp

        <div style="font-size: 1.2rem; font-weight: bold; margin-top: 8px;">
    <strong>Idade:</strong> {{ $idade }} anos
</div>


        <div class="col-botoes mt-3">
            <a href="{{ route('ee.utente.editar', $utente->id) }}" class="btn-utente azul">
                ✏️ Editar dados e submeter documentos da criança
            </a>
        </div>
    </div>
</div>





{{-- Preferências de Notificação --}}
<div class="preferencias-box" data-utente-id="{{ $utente->id }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px;">

    <h4 style="width: 100%; font-size: 1.6rem; font-weight: 600; margin-bottom: 12px; color: #333;">Notificações por e-mail relativo a {{ $utente->name }}</h4>

    @php
        $notificacoes = [
            'checkin' => ['label' => '📥 Receber notificações de Entrada', 'checked' => $utente->receive_checkin_notifications, 'cor' => ['#d4edda', '#155724', '#c3e6cb']],
            'checkout' => ['label' => '📤 Receber notificações de Saída', 'checked' => $utente->receive_checkout_notifications, 'cor' => ['#fff3cd', '#856404', '#ffeeba']],
            'self' => ['label' => '🙋 Notificações quando sou eu a entregar ou recolher', 'checked' => $utente->receive_self_notifications, 'cor' => ['#d1ecf1', '#0c5460', '#bee5eb']],
        ];
    @endphp

    @foreach ($notificacoes as $tipo => $info)
        @php
            // Gera um ID único para o checkbox (prefixo 'notif_')
            $checkboxId = "notif_toggle_" . $tipo . "_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $utente->id ?? '') . "_" . $loop->index;
        @endphp
        <div class="preferencia-item-com-toggle" {{-- Classe generalizada --}}
             style="flex-grow: 1; min-width: 280px; background-color: {{ $info['cor'][0] }}; color: {{ $info['cor'][1] }}; border: 1px solid {{ $info['cor'][2] }}; border-radius: 5px; padding: 10px; display: flex; justify-content: space-between; align-items: center; font-size: 1.4rem; cursor: pointer;"
             onclick="document.getElementById('{{ $checkboxId }}').click();">

            <span>{{ $info['label'] }}</span>

            <div class="toggle-area" style="display: flex; align-items: center; gap: 8px;">
                <label class="toggle-switch" for="{{ $checkboxId }}">
                    <input type="checkbox"
                           id="{{ $checkboxId }}"
                           class="preferencia-toggle" {{-- Crucial para o JS --}}
                           data-tipo="{{ $tipo }}"
                           {{ $info['checked'] ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <strong class="toggle-state-text">{{ $info['checked'] ? 'Sim' : 'Não' }}</strong>
            </div>
        </div>
    @endforeach
</div>

{{-- Preferências de Autorizações --}}
<div class="preferencias-box" data-utente-id="{{ $utente->id }}" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px;">

    <h4 style="width: 100%; font-size: 1.6rem; font-weight: 600; margin-bottom: 12px; color: #333;">Autorizações</h4>

   @php
    $autorizações = [
        'sair_sozinho' => [
            'label' => '🚶‍♂️ Pode sair sozinho',
            'checked' => $utente->_snipeit_pode_sair_sozinho_66,
            'cor' => ['#fff3cd', '#856404', '#ffeeba'],
        ],
    ];
@endphp


    @foreach ($autorizações as $tipo => $info)
        @php
            // Gera um ID único para o checkbox (prefixo 'auth_')
            $checkboxId = "auth_toggle_" . $tipo . "_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $utente->id ?? '') . "_" . $loop->index;
        @endphp
        <div class="preferencia-item-com-toggle" {{-- Classe generalizada --}}
             style="flex-grow: 1; min-width: 280px; background-color: {{ $info['cor'][0] }}; color: {{ $info['cor'][1] }}; border: 1px solid {{ $info['cor'][2] }}; border-radius: 5px; padding: 10px; display: flex; justify-content: space-between; align-items: center; font-size: 1.4rem; cursor: pointer;"
             onclick="document.getElementById('{{ $checkboxId }}').click();">

            <span>{{ $info['label'] }}</span>

            <div class="toggle-area" style="display: flex; align-items: center; gap: 8px;">
                <label class="toggle-switch" for="{{ $checkboxId }}">
                    <input type="checkbox"
                           id="{{ $checkboxId }}"
                           class="preferencia-toggle" {{-- Crucial para o JS --}}
                           data-tipo="{{ $tipo }}"
                           {{ $info['checked'] ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <strong class="toggle-state-text">{{ $info['checked'] ? 'Sim' : 'Não' }}</strong>
            </div>
        </div>
    @endforeach
</div>
{{-- Adicione este CSS à sua folha de estilos ou numa tag <style> na página --}}
<style>
    .autorizacao-item:hover {
        filter: brightness(95%); /* Feedback visual ao passar o rato */
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px; /* Largura do toggle */
        height: 28px; /* Altura do toggle */
        /* O cursor já está no .autorizacao-item, mas pode ser útil aqui se a label não preencher toda a área clicável designada */
    }

    /* Esconde o checkbox original */
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute; /* Remove do fluxo para não ocupar espaço */
    }

    /* Estilo da base do toggle (o "trilho") */
    .slider {
        position: absolute;
        cursor: pointer; /* Cursor para a parte visual do switch */
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /* Cor quando desligado */
        -webkit-transition: .4s;
        transition: .4s;
    }

    /* Estilo do botão deslizante */
    .slider:before {
        position: absolute;
        content: "";
        height: 20px; /* Altura do botão */
        width: 20px;  /* Largura do botão */
        left: 4px;
        bottom: 4px;
        background-color: white; /* Cor do botão */
        -webkit-transition: .4s;
        transition: .4s;
    }

    /* Mudança de cor e posição quando o input está checked (ligado) */
    input:checked + .slider {
        background-color: #28a745; /* Cor verde para "Sim" / Ligado */
    }

    input:focus + .slider { /* Efeito de foco para acessibilidade */
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(22px); /* Distância de deslize: Largura do toggle (50) - diâmetro do botão (20) - 2*margem interna (4*2) = 22px aprox. (50-20-8 = 22) */
        -ms-transform: translateX(22px);
        transform: translateX(22px);
    }

    /* Para cantos arredondados */
    .slider.round {
        border-radius: 28px; /* Altura do toggle */
    }

    .slider.round:before {
        border-radius: 50%; /* Botão circular */
    }

    .toggle-state-text {
        font-weight: bold;
        user-select: none; /* Evita que o texto "Sim/Não" seja selecionado acidentalmente */
        white-space: nowrap; /* Impede que "Sim" / "Não" quebre linha */
    }
</style>



        <div class="alert alert-success mensagem-preferencia"
             style="height: 70px; font-size: 1.4rem; display: flex; align-items: center; gap: 8px; opacity: 0; visibility: hidden; transition: opacity 0.5s ease; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; padding: 8px 15px;">
            <span style="font-size: 1.2rem;">✅</span> <span class="texto"></span>
        </div>

        {{-- Notas do Encarregado de Educação --}}
        <h5 style="margin-top: 2rem; font-size: 1.6rem;">📝 Notas do Encarregado de Educação</h5>
<form method="POST" action="{{ route('ee.utente.guardarNota', $utente->id) }}">
    @csrf
    <div style="margin-bottom: 1rem;">
        <textarea id="novaNota-{{ $utente->id }}" name="conteudo"
                  style="width: 100%; padding: 10px; font-size: 1.4rem; border: 1px solid #ccc; border-radius: 6px;"
                  rows="3"
                  placeholder="Escreva aqui a sua nota..."></textarea>
    </div>
    <button type="button"
            data-utente="{{ $utente->id }}"
            class="btnGuardarNota"
            style="padding: 10px 16px; background-color: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 1.4rem; cursor: pointer;">
        Adicionar Nota
    </button>
</form>

        @php
            $notasOrdenadas = $utente->notasEe->sortByDesc('created_at');
        @endphp

        <div id="listaNotas" style="margin-top: 1rem;">
    <ul id="listaNotas-{{ $utente->id }}" style="list-style: none; padding: 0; margin: 0;">
        @foreach ($notasOrdenadas as $nota)
            <li data-id="{{ $nota->id }}"
                style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; margin-bottom: 8px; border: 1px solid #ccc; border-radius: 6px; background-color: #f9f9f9;">
                
                <div style="flex: 1; margin-right: 10px; font-size: 1.4rem;">
                    {{ $nota->conteudo }}<br>
                    <span style="font-size: 1.2rem; color: #888;">{{ $nota->created_at->format('d/m/Y H:i') }}</span>
                </div>

                @if ($nota->responsavel_id == session('ee_responsavel_id'))
                    <button type="button"
                            title="Eliminar nota"
                            data-id="{{ $nota->id }}"
                            data-utente="{{ $nota->asset_id }}"
                            class="btn-apagar-nota"
                            style="background-color: transparent; color: #c0392b; border: 1px solid #c0392b; border-radius: 4px; padding: 6px 10px; font-size: 1.4rem; cursor: pointer;">
                        🗑️
                    </button>
                @endif
            </li>
        @endforeach
    </ul>
</div>


        {{-- Incidentes --}}
        @if ($utente->maintenances->isNotEmpty())
    <div style="margin-top: 15px; border: 1px solid #ffc107; background-color: #fffde7;
                border-radius: 5px; padding: 12px; font-size: 1.5rem; max-height: 300px; overflow-y: auto;">
        <strong style="color: #856404;">⚠️ Incidentes registados:</strong>
        <ul style="padding-left: 20px; margin-top: 10px; list-style-type: none;">
            @foreach ($utente->maintenances as $incident)
                <li style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #e0c97d;">
                    <div style="font-size: 1.5rem; font-weight: 600;">
                    🚑 {{ $incident->title ?? 'Sem título' }}
                    </div>

                    @if ($incident->notes)
                        <div style="font-style: italic; color: #6c757d; margin-top: 4px; font-size: 1.4rem;">
                            📝 {{ $incident->notes }}
                        </div>
                    @endif

                    @php
                        $data = \Carbon\Carbon::parse($incident->created_at)->format('d/m/Y');
                        $autor = $incident->admin ? trim(($incident->admin->first_name ?? '') . ' ' . ($incident->admin->last_name ?? '')) : null;
                    @endphp

                    @if ($autor)
                        <div style="margin-top: 6px; font-size: 1.3rem; color: #555;">
                            📋 Reportado em {{ $data }} por {{ $autor }}
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif




        {{-- Programas --}}
        @php
            $inscrito = collect($programas)->filter(fn($p, $campo) => !empty($utente->$campo));
        @endphp
        @if ($inscrito->isNotEmpty())
            <div style="margin-top: 15px; border: 1px solid #6c757d; background-color: #f8f9fa; border-radius: 5px; padding: 12px; font-size: 1.5rem;">
                <strong style="color: #495057;">🛝 Programas Inscritos:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; list-style-type: disc;">
                    @foreach ($inscrito as $campo => $info)
                        <li style="margin-bottom: 6px;">
                            <div style="font-weight: bold;">{{ $info['icone'] }} {{ $info['nome'] }}</div>
                            <div style="color: #2c3e50; font-size: 1.4rem; margin-left: 15px;">📅 {{ $utente->$campo }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Entradas e Saídas --}}
        @if ($utente->ultimos_logs && count($utente->ultimos_logs))
            <div style="margin-top: 15px; border: 1px solid #17a2b8; background-color: #e0f7fa; border-radius: 5px; padding: 12px; font-size: 1.5rem;">
                <strong style="color: #388e3c;">🕓 Últimas Entradas/Saídas:</strong>
                <ul style="padding-left: 20px; margin-top: 10px; list-style-type: disc;">
                    @foreach ($utente->ultimos_logs as $log)
                        <li style="margin-bottom: 6px;">
                            {{ \Carbon\Carbon::parse($log->action_date)->format('d/m/Y H:i') }} –
                            @if ($log->action_type === 'checkin')
                                <span style="color: green;">🟢 Entrada</span>
                            @else
                                <span style="color: red;">🔴 Saída</span>
                            @endif
                            @if ($log->note)
                                <span style="font-style: italic; color: #6c757d;">({{ $log->note }})</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('ee.utente.historico', $utente->id) }}"
                   style="display: inline-block; margin-top: 8px; background-color: #6c757d; color: white; padding: 6px 10px; border-radius: 5px; text-decoration: none; font-size: 1.4rem;">
                    📖 Ver mais Histórico
                </a>
            </div>
        @endif
        
    </div>


    @endforeach


    <hr style="margin-top: 30px; margin-bottom: 20px;">
    @include('ee.partials.form-novo-responsavel')

    <div id="secao-responsaveis">
    @include('ee.partials.lista-responsaveis-por-educando', ['responsaveis' => $responsaveis])

    </div>


</div>



<div id="toast-container"
     style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
</div>
<style>
    .alerta-sem-foto {
    animation: pulseAlerta 1.5s ease-in-out infinite;
}

@keyframes pulseAlerta {
    0% { box-shadow: 0 0 0px rgba(255, 193, 7, 0.3); }
    50% { box-shadow: 0 0 10px rgba(255, 193, 7, 0.7); }
    100% { box-shadow: 0 0 0px rgba(255, 193, 7, 0.3); }
}

.card-utente {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.utente-header {
    
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 20px;
}

.col-foto {
    flex: 0 0 110px;
}


.utente-foto {
    width: 110px;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #ccc;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.col-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.utente-nome {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.col-botoes {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.qr-thumbnail {
    height: 70px;
    width: 70px;
    margin-top: 8px;
    cursor: pointer;
    border-radius: 8px;
}
.btn-utente {
    /* Seus estilos existentes como padding, border-radius, font-size, etc. */
    padding: 8px 12px; /* Verifique qual padding está a usar (vi 6px e 8px) */
    border-radius: 6px;
    font-size: 1.4rem;
    font-weight: 500; /* Do CSS principal */
    border: none;
    color: white;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: inline-block; /* Importante para o comportamento lado a lado, se aplicável */
    margin-top: 6px; /* Do CSS da lista */
    transition: background-color 0.3s; /* Do CSS da lista */

    /* ADIÇÕES SUGERIDAS: */
    max-width: 100%;
    box-sizing: border-box;
    white-space: normal;
    word-break: break-word; /* Opcional: útil se tiver palavras muito longas e inquebráveis */
}

.btn-utente.verde {
    background-color: #198754;
}

.btn-utente.azul {
    background-color: #007bff;
}

.badge-foto-sucesso {
    display: none;
    margin-top: 6px;
    font-size: 1.2rem;
    background: #d1e7dd;
    color: #0f5132;
    padding: 5px 10px;
    border-radius: 6px;
}

@media (max-width: 768px) {
    .utente-header {
        flex-direction: column;
        align-items: center;
    }

   .col-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-width: 0; /* ADICIONADO: Permite que a coluna encolha corretamente */
}

    .utente-nome {
        text-align: center;
    }

    .col-botoes {
        justify-content: center;
    }
}
.foto-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    padding-bottom: 10px;
}

.alerta-sem-foto {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 1.2rem;
    margin-top: 10px;
    text-align: center;
    max-width: 200px;
    animation: pulseAlerta 1.5s ease-in-out infinite;
}

.foto-controles {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 8px;
    gap: 6px;
}

@keyframes pulseAlerta {
    0% { box-shadow: 0 0 0px rgba(255, 193, 7, 0.3); }
    50% { box-shadow: 0 0 10px rgba(255, 193, 7, 0.6); }
    100% { box-shadow: 0 0 0px rgba(255, 193, 7, 0.3); }
}
.card-custom {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
    font-size: 1rem;
}

@media (min-width: 576px) {
    .card-custom {
        max-width: 970px;
    }
}

.utente-info {
    /* Estilos existentes que possa ter para esta classe */
    font-size: 1.4rem; /* Já aplicado inline no seu HTML */
    margin-bottom: 10px; /* Já aplicado inline no seu HTML */

    /* Adicione estas propriedades para quebrar palavras/emails longos */
    overflow-wrap: break-word;
    word-wrap: break-word; /* Alias mais antigo para compatibilidade, se necessário */
    /* Opcionalmente, para um controlo ainda mais fino da quebra de palavras: */
    /* word-break: break-all; */ /* Esta é mais agressiva e quebra a palavra em qualquer ponto. Use se 'overflow-wrap' não for suficiente. */
}

</style>

<!-- Modal Personalizado -->
<div id="modal-eliminar-nota" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center; font-family: sans-serif;">
    <div style="background: white; border-radius: 8px; padding: 24px; max-width: 480px; width: 90%; font-size: 1.4rem;">
        <h5 style="margin-bottom: 14px; color: #c0392b; font-size: 1.8rem;">❗ Eliminar Nota</h5>
        <p style="margin-bottom: 24px; font-size: 1.5rem; color: #333;">
            Tem a certeza que pretende eliminar esta nota? <br>Esta ação é <strong>irreversível</strong>.
        </p>
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" id="btn-cancelar-modal" 
                style="padding: 10px 18px; font-size: 1.4rem; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Cancelar
            </button>
            <button type="button" id="btn-confirmar-modal"
                style="padding: 10px 18px; font-size: 1.4rem; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Eliminar
            </button>
        </div>
    </div>
</div>

<div id="modal-qr" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.85); z-index: 9999; justify-content: center; align-items: center; transition: opacity 0.3s ease; padding: 5vw; box-sizing: border-box;">
    
    <div id="modal-qr-box" style="background: white; border-radius: 12px; padding: 20px; text-align: center;
        width: 100%; max-width: 600px; max-height: 90vh; overflow: auto; box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);">
        
        <h5 id="modal-titulo" style="margin-bottom: 15px; font-size: 2rem;">QR Code</h5>
        
        <img id="modal-imagem" src="" alt="QR Code"
             style="width: 100%; max-width: 100%; height: auto; margin-bottom: 20px; border-radius: 8px;">
        
        <button onclick="fecharModalQr()"
                style="padding: 12px 20px; background: #dc3545; color: white; font-size: 1.6rem;
                border: none; border-radius: 6px; cursor: pointer;">
            Fechar
        </button>
    </div>
</div>




@push('js')

<script>
    // ... (const qrcodes = @json($qrcodes); e outras funções globais como abrirModalQr, fecharModalQr)

document.addEventListener('DOMContentLoaded', function () {

    // =============================
    // ⏱ Contador da sessão
    // =============================
  
    const expiresAt = {{ session('ee_session_expires_at') ?? 'null' }};
    const contadorEl = document.getElementById('contador');

    if (expiresAt && contadorEl) {
        function atualizarContador() {
            const agora = Math.floor(Date.now() / 1000);
            const segundosRestantes = expiresAt - agora;

            if (segundosRestantes <= 0) {
                contadorEl.innerText = '00:00';
                clearInterval(intervaloContador); // Para o contador

                // ✅ Redirecionamento automático ao expirar
                window.location.href = '/ee/logout-redirect';

                return;
            }

            const minutos = Math.floor(segundosRestantes / 60).toString().padStart(2, '0');
            const segundos = (segundosRestantes % 60).toString().padStart(2, '0');
            contadorEl.innerText = `${minutos}:${segundos}`;
        }

        atualizarContador();
        var intervaloContador = setInterval(atualizarContador, 1000);
    }



    // ===============================================================
// ✅ Preferências (Notificações e Autorizações com toggle)
// ===============================================================
document.querySelectorAll('.preferencia-toggle').forEach(function (checkbox) {
    // Elementos específicos para o toggle (agora para ambos os tipos)
    // Usa a classe comum '.preferencia-item-com-toggle'
    const preferenciaItemDiv = checkbox.closest('.preferencia-item-com-toggle'); // MODIFICADO AQUI
    let textoToggleSimNao = null;

    if (preferenciaItemDiv) { // Verifica se este checkbox está dentro de um item com a estrutura de toggle
        const toggleArea = preferenciaItemDiv.querySelector('.toggle-area');
        if (toggleArea) {
            textoToggleSimNao = toggleArea.querySelector('.toggle-state-text');
        }

        // Adiciona stopPropagation para os toggles para evitar duplo clique
        // devido ao onclick no div.preferencia-item-com-toggle
        const labelSwitch = checkbox.closest('.toggle-switch');
        if (labelSwitch) {
            labelSwitch.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        }
    }

    checkbox.addEventListener('change', function () {
        const isChecked = this.checked;

        // 1. Atualiza o texto "Sim/Não" para os itens que têm a estrutura de toggle
        if (textoToggleSimNao) { // textoToggleSimNao foi definido acima e será null se não for um item com toggle
            textoToggleSimNao.textContent = isChecked ? 'Sim' : 'Não';
        }

        // 2. Lógica AJAX (mantém-se igual)
        const tipo = this.dataset.tipo;
        const utenteBox = this.closest('.preferencias-box');
        const utenteId = utenteBox.dataset.utenteId;

        const cardUtente = this.closest('.card-utente');
        const mensagemEl = cardUtente ? cardUtente.querySelector('.mensagem-preferencia') : null;

        if (!mensagemEl) {
            console.error('Elemento .mensagem-preferencia não encontrado dentro de .card-utente.');
            this.checked = !isChecked; // Reverte
            if (textoToggleSimNao) {
                textoToggleSimNao.textContent = this.checked ? 'Sim' : 'Não';
            }
            alert('Erro: Elemento de mensagem não encontrado. Contacte o suporte.');
            return;
        }

        const iconSpan = mensagemEl.children[0];
        const textSpan = mensagemEl.querySelector('.texto');

        fetch(`/ee/utente/${utenteId}/preferencia-ajax`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tipo, valor: isChecked })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().catch(() => ({})).then(errorData => {
                    throw { status: res.status, data: errorData, response: res };
                });
            }
            return res.json();
        })
        .then(data => {
            if (textSpan) textSpan.innerText = data.mensagem || 'Preferência atualizada com sucesso!';
            if (iconSpan) iconSpan.innerText = '✅';

            mensagemEl.style.backgroundColor = '#d4edda';
            mensagemEl.style.color = '#155724';
            mensagemEl.style.borderColor = '#c3e6cb';
            mensagemEl.classList.remove('alert-danger');
            mensagemEl.classList.add('alert-success');

            mensagemEl.style.visibility = 'visible';
            mensagemEl.style.opacity = '1';
            setTimeout(() => {
                mensagemEl.style.opacity = '0';
                setTimeout(() => {
                    mensagemEl.style.visibility = 'hidden';
                }, 500);
            }, 3000);
        })
        .catch(error => {
            console.error("Erro ao atualizar preferência:", error);

            this.checked = !isChecked; // Reverte o checkbox
            if (textoToggleSimNao) { // Atualiza o texto do toggle se existir
                textoToggleSimNao.textContent = this.checked ? 'Sim' : 'Não';
            }

            const errorMsg = (error.data && error.data.mensagem) ? error.data.mensagem : 'Erro ao atualizar preferências.';
            if (textSpan) textSpan.innerText = errorMsg;
            if (iconSpan) iconSpan.innerText = '⚠️';

            mensagemEl.style.backgroundColor = '#f8d7da';
            mensagemEl.style.color = '#842029';
            mensagemEl.style.borderColor = '#f5c2c7';
            mensagemEl.classList.remove('alert-success');
            mensagemEl.classList.add('alert-danger');

            mensagemEl.style.visibility = 'visible';
            mensagemEl.style.opacity = '1';
            setTimeout(() => {
                mensagemEl.style.opacity = '0';
                setTimeout(() => {
                    mensagemEl.style.visibility = 'hidden';
                    mensagemEl.classList.remove('alert-danger');
                    mensagemEl.classList.add('alert-success');
                    mensagemEl.style.backgroundColor = '';
                    mensagemEl.style.color = '';
                    mensagemEl.style.borderColor = '';
                }, 500);
            }, 3000);
        });
    });
});

    // =============================
    // 🖼️ Upload de Foto
    // =============================
    document.querySelectorAll('.input-foto').forEach(input => {
        input.addEventListener('change', function () {
            const file = this.files[0];
            const inputId = this.id;
            const utenteId = inputId.replace('input-foto-', '');
            const previewEl = document.getElementById('foto-preview-' + utenteId);
            const badge = document.getElementById('foto-sucesso-' + utenteId);

            if (!file || !utenteId || !previewEl) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                previewEl.src = e.target.result;
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('foto', file);

            fetch(`/ee/utente/${utenteId}/foto`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    previewEl.src = data.url; // Usa o URL retornado pelo servidor
                    mostrarToast('📸 Foto atualizada com sucesso!', 'success');
                    if (badge) {
                        badge.style.display = 'inline-block';
                        badge.style.opacity = '1';
                        badge.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => {
                            badge.style.opacity = '0';
                            setTimeout(() => badge.style.display = 'none', 300);
                        }, 3000);
                    }
                    // Se o alerta de "sem foto" estiver visível, esconde-o
                    const alertaSemFoto = previewEl.closest('.foto-wrapper').querySelector('.alerta-sem-foto');
                    if (alertaSemFoto) {
                        alertaSemFoto.style.display = 'none';
                    }

                } else {
                    mostrarToast('⚠️ Erro: ' + (data.message || 'Erro ao atualizar a foto.'), 'error');
                }
            })
            .catch(() => {
                mostrarToast('⚠️ Erro ao comunicar com o servidor.', 'error');
            });
        });
    });

    // ===============
    // Guardar Nota
    // ===============
    document.querySelectorAll('.btnGuardarNota').forEach(button => {
        button.addEventListener('click', function () {
            const utenteId = this.dataset.utente;
            const textarea = document.getElementById(`novaNota-${utenteId}`);
            const conteudo = textarea.value.trim();

            if (!conteudo) {
                mostrarToast("Por favor, escreva uma nota.", "error"); // Usar toast
                return;
            }

            const formData = new FormData();
            formData.append('conteudo', conteudo);

            fetch(`{{ url('/ee/utente') }}/${utenteId}/nota`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const lista = document.querySelector(`#listaNotas-${utenteId}`);

                    const novaNota = document.createElement('li');
                    novaNota.dataset.id = data.nota_id;
                    // Aplicar os estilos que já tem para os items da lista
                    novaNota.style.display = 'flex';
                    novaNota.style.justifyContent = 'space-between';
                    novaNota.style.alignItems = 'center';
                    novaNota.style.padding = '10px 15px';
                    novaNota.style.marginBottom = '8px';
                    novaNota.style.border = '1px solid #ccc';
                    novaNota.style.borderRadius = '6px';
                    novaNota.style.backgroundColor = '#f9f9f9';

                    let htmlInterno = `
                        <div style="flex: 1; margin-right: 10px; font-size: 1.4rem;">
                            ${data.conteudo}<br>
                            <span style="font-size: 1.2rem; color: #888;">${data.created_at}</span>
                        </div>`;

                    if (data.responsavel_id == {{ session('ee_responsavel_id') ?? 'null' }}) { // Adicionar 'null' como fallback
                        htmlInterno += `
                            <button type="button"
                                    class="btn-apagar-nota"
                                    title="Eliminar nota"
                                    data-id="${data.nota_id}"
                                    data-utente="${utenteId}"
                                    style="background-color: transparent; color: #c0392b; border: 1px solid #c0392b; border-radius: 4px; padding: 6px 10px; font-size: 1.4rem; cursor: pointer;">
                                🗑️
                            </button>`;
                    }
                    novaNota.innerHTML = htmlInterno;
                    lista.prepend(novaNota); // Adiciona no início da lista
                    textarea.value = '';
                    mostrarToast("📝 Nota adicionada com sucesso!", "success");
                    ativarListenersNotas(); // Reativa listeners para o novo botão de apagar
                } else {
                    mostrarToast("Erro ao guardar a nota: " + (data.message || ''), "error");
                }
            })
            .catch(err => {
                console.error("Erro AJAX ao guardar nota:", err);
                mostrarToast("Erro inesperado ao guardar nota.", "error");
            });
        });
    });

    let notaAEliminar = null; // Variável para guardar a referência da nota a eliminar

    function handleNotaDelete() {
        notaAEliminar = this.closest('li'); // 'this' é o botão clicado
        document.getElementById('modal-eliminar-nota').style.display = 'flex';
    }

    function ativarListenersNotas() {
        document.querySelectorAll('.btn-apagar-nota').forEach(button => {
            button.removeEventListener('click', handleNotaDelete); // Remove listener antigo para evitar duplicação
            button.addEventListener('click', handleNotaDelete);
        });
    }
    ativarListenersNotas(); // Ativa para notas já existentes na página

    // ===============
    // Eliminar Nota (Confirmação do Modal)
    // ===============
    document.getElementById('btn-cancelar-modal').addEventListener('click', function () {
        document.getElementById('modal-eliminar-nota').style.display = 'none';
        notaAEliminar = null;
    });

    document.getElementById('btn-confirmar-modal').addEventListener('click', function () {
        if (!notaAEliminar) return;

        const notaId = notaAEliminar.dataset.id;
        // O data-utente está no botão dentro do <li>
        const utenteIdDoBotao = notaAEliminar.querySelector('.btn-apagar-nota').dataset.utente;


        fetch(`/ee/utente/${utenteIdDoBotao}/nota/${notaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                notaAEliminar.remove();
                mostrarToast("🗑️ Nota eliminada com sucesso!", "success");
            } else {
                mostrarToast('Erro ao eliminar a nota: ' + (data.message || ''), "error");
            }
        })
        .catch(() => mostrarToast('Erro na comunicação com o servidor ao eliminar nota.', "error"))
        .finally(() => {
            document.getElementById('modal-eliminar-nota').style.display = 'none';
            notaAEliminar = null;
        });
    });

    // =============================
    // 🔔 Toast reutilizável
    // =============================
    window.mostrarToast = function (mensagem, tipo) {
        const container = document.getElementById('toast-container');
        if (!container) { // Adiciona verificação para o container do toast
            console.warn('Toast container #toast-container não encontrado.');
            // Fallback para alert se o container não existir
            alert(`${tipo === 'success' ? 'Sucesso' : 'Erro'}: ${mensagem}`);
            return;
        }
        const toast = document.createElement('div');
        toast.textContent = mensagem;
        toast.style.background = tipo === 'success' ? '#d1e7dd' : '#f8d7da';
        toast.style.color = tipo === 'success' ? '#0f5132' : '#842029';
        toast.style.border = '1px solid ' + (tipo === 'success' ? '#badbcc' : '#f5c2c7');
        toast.style.padding = '10px 15px';
        toast.style.marginBottom = '5px';
        toast.style.borderRadius = '5px';
        toast.style.boxShadow = '0 2px 6px rgba(0,0,0,0.1)';
        toast.style.fontSize = '1.4rem';
        toast.style.opacity = '0'; // Começa transparente para animar
        toast.style.transition = 'opacity 0.3s ease-in-out';

        container.appendChild(toast);

        // Forçar reflow para garantir que a transição de opacidade funcione
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300); // Remove após a transição de fade out
        }, 3500);
    };

});
window.abrirModalQr = function (id, nome) {
    const modal = document.getElementById('modal-qr');
    const titulo = document.getElementById('modal-titulo');
    const imagem = document.getElementById('modal-imagem');
    const qrcodes = @json($qrcodes);

    if (!qrcodes[id]) {
        alert("QR Code não encontrado.");
        return;
    }

    titulo.textContent = `QR Code de ${nome}`;
    imagem.src = `data:image/png;base64,${qrcodes[id]}`;
    modal.style.display = 'flex';
};


window.fecharModalQr = function () {
    const modal = document.getElementById('modal-qr');
    modal.style.display = 'none';
};

// Exemplo de como poderia tratar a resposta do backend na função aceitarRgpd:
function aceitarRgpd() {
    fetch('{{ url('/ee/aceitar-rgpd') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json' // Se enviar dados no body
        },
        // body: JSON.stringify({ consent: true }) // Exemplo se enviar dados
    })
    .then(response => {
        if (response.ok) { // Verifica se a resposta HTTP foi bem-sucedida (status 200-299)
            return response.json().catch(() => ({})); // Tenta parsear JSON, ou retorna objeto vazio se não houver corpo ou não for JSON
        } else {
            throw new Error('Falha ao registar consentimento.'); // Lança erro para o .catch()
        }
    })
    .then(data => { // data aqui será o JSON da resposta ou um objeto vazio
        document.getElementById('aviso-rgpd').style.display = 'none';
        // console.log(data.message || 'Consentimento aceite.'); // Exemplo se o backend retornar uma mensagem
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao registar consentimento. Por favor, tente novamente ou contacte o suporte.');
    });
}


</script>

@endpush
@endsection

