<style>
    .card-responsavel {
        border: 1px solid #004080;
        padding: 15px;
        margin-bottom: 30px;
        border-radius: 6px;
        background-color: white; /* Adicionado para o fundo branco */
    }
    .utente-box {
        background-color: #f9f9f9;
        border-radius: 6px;
        padding: 10px;
        margin-top: 10px; /* Ajustado para consistência */
    }
    .foto-utente {
        width: 110px;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ccc;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .btn-utente {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 1.4rem;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 6px;
        border: none;
        text-align: center;
        text-decoration: none;
    }

    .btn-utente.verde {
        background-color: #28a745;
        color: white;
    }

    .btn-utente.verde:hover {
        background-color: #1e7e34;
    }

    .btn-utente.azul {
        background-color: #007bff;
        color: white;
    }

    .btn-utente.azul:hover {
        background-color: #0056b3;
    }

    @media (max-width: 768px) {
        .utente-box {
            font-size: 1.3rem;
        }
    }

    .foto-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        padding-bottom: 10px;
    }

    .foto-controles {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 8px;
        gap: 6px;
    }

    .alerta-sem-foto {
        font-size: 0.9rem;
        color: #dc3545;
        margin-top: 5px;
        text-align: center;
    }

    .badge-foto-sucesso {
        display: none;
        background-color: #28a745;
        color: white;
        border-radius: 6px;
        padding: 5px 8px;
        font-size: 0.9rem;
        margin-top: 5px;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 1rem;
        box-sizing: border-box;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
        font-weight: bold;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-success:hover {
        background-color: #1e7e34;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-left: 5px;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .feedback-assoc {
        margin-left: 10px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .spinner-qrcodes {
    border: 0.15em solid #f3f3f3; /* Cor de fundo */
    border-top: 0.15em solid #ffffff; /* Cor do spinner */
    border-radius: 50%;
    width: 1rem;
    height: 1rem;
    animation: spin 0.6s linear infinite;
    display: inline-block;
    vertical-align: middle;
    margin-right: 6px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

</style>

@foreach ($responsaveis as $responsavel)
<div class="card-responsavel" id="responsavel-card-{{ $responsavel->id }}">
        {{-- Coluna da Foto e Informação do Responsável --}}
        <div style="display: flex; gap: 20px; align-items: flex-start;">
            {{-- Coluna da Foto --}}
            <div class="col-foto">
                <div class="foto-wrapper">
                    <img src="{{ $responsavel->foto ? route('ee.responsavel.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}"
                         alt="Foto de {{ $responsavel->nome_completo }}"
                         class="utente-foto"
                         id="foto-preview-resp-{{ $responsavel->id }}">

                    @if (!$responsavel->foto)
                        <div class="alerta-sem-foto">
                            ⚠️ Para maior segurança, adicione uma foto do autorizado.<br> Facilita a identificação nas recolhas.
                        </div>
                    @endif

                    <div class="foto-controles">
                        <input type="file" class="input-foto-resp" accept="image/*" id="input-foto-resp-{{ $responsavel->id }}" hidden>
                        <button type="button" class="btn-utente verde" onclick="document.getElementById('input-foto-resp-{{ $responsavel->id }}').click();">
                            📤 Atualizar Foto
                        </button>
                        <div class="badge-foto-sucesso" id="foto-sucesso-resp-{{ $responsavel->id }}">✅ Foto atualizada</div>
                    </div>
                </div>
            </div>

            {{-- Coluna da Informação --}}
            <div class="col-info" style="flex-grow: 1;">
                <div id="dados-visuais-{{ $responsavel->id }}">
                    <div class="utente-nome"> {{ $responsavel->nome_completo }}</div>
                    @php
                        $isEEPrincipal = false;
                        foreach ($responsavel->utentes as $utenteVerificacao) {
                            if ($utenteVerificacao->pivot->tipo_responsavel === 'Encarregado de Educacao') {
                                $isEEPrincipal = true;
                                break;
                            }
                        }
                    @endphp
                    @if ($isEEPrincipal)
                        <span style="font-size: 1.4rem; color: #007bff; font-weight: bold; display: block; margin-top: 5px;">🎓 É o Encarregado de Educação</span>
                    @endif
                    <div class="utente-info" style="font-size: 1.4rem; margin-bottom: 10px;">
                        📇 <strong>Nº Identificação: </strong> {{ $responsavel->nr_identificacao }}<br>
                        📧 <strong>E-mail: </strong>{{ $responsavel->email ?? '-' }}<br>
                        📞 <strong>Contacto: </strong>{{ $responsavel->contacto ?? '-' }}
                    </div>
                    @if (!$isEEPrincipal)
    <button type="button" class="btn-utente azul" onclick="editarResponsavel({{ $responsavel->id }})">
        ✏️ Editar dados do autorizado
    </button>
    @if (!$isEEPrincipal && $responsavel->utentes->count() > 1)
    <button type="button"
            class="btn-utente btn-danger"
            onclick="removerResponsavel({{ $responsavel->id }})"
            style="margin-left: 10px;">
        🗑️ Remover autorizado de todas as crianças
    </button>
@endif

    
@else
    <button type="button" class="btn-utente azul" style="opacity: 0.5; cursor: not-allowed;" disabled>
        ✏️ Editar dados do autorizado
    </button>
@endif
@if ($isEEPrincipal) {{-- O botão só aparece para o Encarregado de Educação Principal --}}
    <form action="{{ route('ee.enviar.qrcodes') }}" method="POST" style="margin-top: 10px;"
          id="form-enviar-qrcodes-{{ $responsavel->id }}"
          onsubmit="enviarQrCodesAjax(event, this, {{ $responsavel->id }}); return false;"> {{-- ADICIONADO return false; AQUI TAMBÉM --}}
        @csrf
        <button type="submit" class="btn-utente verde" id="btn-enviar-qrcodes-{{ $responsavel->id }}">
            <span class="spinner-qrcodes" id="spinner-qrcodes-{{ $responsavel->id }}" style="display: none; width: 1rem; height: 1rem; border-width: .15em;" role="status" aria-hidden="true"></span>
            ✉️ Enviar QR Codes por e-mail aos autorizados
        </button>
        <div id="feedback-qrcodes-{{ $responsavel->id }}" style="margin-top: 8px; font-weight: bold; font-size: 0.9rem;"></div>
    </form>
@endif

                </div>
                <div id="mensagem-edicao-{{ $responsavel->id }}" style="margin-top: 8px; font-weight: bold;"></div>

                <form id="form-edicao-{{ $responsavel->id }}"
      onsubmit="submeterEdicaoResponsavel(event, {{ $responsavel->id }}, '{{ route('ee.responsavel.atualizarDados', $responsavel->id) }}')"
      style="display: none; margin-top: 15px;">
    @csrf

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
    <input type="text" name="nome_completo" value="{{ $responsavel->nome_completo }}" class="form-control" style="font-size: 1.4rem;" placeholder="Nome completo">
    <input type="text" name="nr_identificacao" value="{{ $responsavel->nr_identificacao }}" class="form-control" style="font-size: 1.4rem;" placeholder="Nº Identificação">
    <input type="email" name="email" value="{{ $responsavel->email }}" class="form-control" style="font-size: 1.4rem;" placeholder="Email">
    <input type="text" name="contacto" value="{{ $responsavel->contacto }}" class="form-control" style="font-size: 1.4rem;" placeholder="Contacto">
</div>


    <div style="margin-top: 10px; text-align: right;">
        <button type="submit" class="btn btn-success btn-sm">💾 Guardar</button>
        <button type="button" onclick="cancelarEdicao({{ $responsavel->id }})" class="btn btn-danger btn-sm">❌ Cancelar</button>
    </div>
</form>

            </div>
        </div>

        @if (!$isEEPrincipal && $responsavel->utentes->isNotEmpty())
            <h4 style="margin-top: 20px;">Crianças Associadas</h4>
            @foreach ($responsavel->utentes as $utente)
            @php
    $pivot = $utente->pivot;
    $pivotIdReal = \DB::table('responsaveis_utentes')
        ->where('responsavel_id', $responsavel->id)
        ->where('utente_id', $utente->id)
        ->value('id');

    $pivotId = $pivotIdReal;
    $responsavelId = $responsavel->id;

    $estadoTexto = $pivot->estado_autorizacao ?? 'Nao Autorizado';

    switch ($estadoTexto) {
        case 'Autorizado':
            $icone = '✅';
            $cor = 'green';
            break;
        case 'Nao Iniciado':
            $icone = '⏳';
            $cor = 'orange';
            break;
        case 'Autorizacao Expirada':
            $icone = '❌';
            $cor = 'red';
            break;
        case 'Nao Autorizado':
        default:
            $icone = '❌';
            $cor = 'red';
            $estadoTexto = 'Não autorizado';
            break;
    }
@endphp

<div class="utente-box" data-utente-id="{{ $utente->id }}" style="margin-top: 10px;">
    <img src="{{ $utente->image ? route('ee.utente.foto', ['filename' => $utente->image]) : asset('img/anoninochild.jpg') }}"
         alt="Foto de {{ $utente->name }}"
         style="width: 50px; height: 65px; object-fit: cover; border-radius: 6px; border: 1px solid #ccc; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">

    <div style="font-size: 1.3rem; flex-grow: 1;"
         id="frase-estado-{{ $pivotId }}"
         data-responsavel="{{ $responsavel->nome_completo }}"
         data-utente="{{ $utente->name }}">
        O responsável <strong>{{ $responsavel->nome_completo }}</strong>
        {{ $estadoTexto === 'Autorizado' ? 'está' : 'não está' }} autorizado para a recolha de
        <strong>{{ $utente->name }}</strong> de acordo com as definições abaixo.
        <span style="color: {{ $cor }}; font-weight: bold;" id="estado-autorizacao-{{ $pivotId }}">
            {{ $icone }} {{ $estadoTexto }}
        </span>
    </div>

    {{-- ✅ Botão de remoção pontual --}}
    <button type="button"
            class="btn-utente btn-danger"
            onclick="removerResponsavelUnico({{ $responsavel->id }}, {{ $utente->id }})"
            style="margin-top: 10px;">
        🗑️ Remover este autorizado apenas desta criança
    </button>
</div>


                @if (!$utente->image)
                    <div class="alerta-sem-foto" style="margin-top: 5px;">
                        ⚠️ Para maior segurança, adicione uma foto do educando.<br> Facilita a identificação nas recolhas.
                    </div>
                @endif

                @php
                    $pivotIdReal = \DB::table('responsaveis_utentes')
                        ->where('responsavel_id', $responsavel->id)
                        ->where('utente_id', $utente->id)
                        ->value('id');
                    $ehEE = $pivot->tipo_responsavel === 'Encarregado de Educacao';
                @endphp

                

                <form method="POST" enctype="multipart/form-data" action="{{ route('ee.associacao.atualizar') }}" class="form-atualizar-associacao" data-pivot-id="{{ $pivotIdReal }}" style="margin-top: 10px;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $pivotIdReal }}">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
    <div class="form-group">
        <label for="grau_parentesco" style="font-size: 1.4rem;">Grau de Parentesco</label>
        <select class="form-control" id="grau_parentesco" name="grau_parentesco" required
                style="font-size: 1.4rem;"
                @if ($pivot->tipo_responsavel === 'Encarregado de Educacao') disabled @endif>
            <option value="" disabled {{ empty($pivot->grau_parentesco) ? 'selected' : '' }}>Selecione...</option>
            <option value="Pai" {{ $pivot->grau_parentesco == 'Pai' ? 'selected' : '' }}>Pai</option>
            <option value="Mãe" {{ $pivot->grau_parentesco == 'Mãe' ? 'selected' : '' }}>Mãe</option>
            <option value="Avô" {{ $pivot->grau_parentesco == 'Avô' ? 'selected' : '' }}>Avô</option>
            <option value="Avó" {{ $pivot->grau_parentesco == 'Avó' ? 'selected' : '' }}>Avó</option>
            <option value="Tio" {{ $pivot->grau_parentesco == 'Tio' ? 'selected' : '' }}>Tio</option>
            <option value="Tia" {{ $pivot->grau_parentesco == 'Tia' ? 'selected' : '' }}>Tia</option>
            <option value="Irmão" {{ $pivot->grau_parentesco == 'Irmão' ? 'selected' : '' }}>Irmão(ã)</option>
            <option value="Padrasto" {{ $pivot->grau_parentesco == 'Padrasto' ? 'selected' : '' }}>Padrasto</option>
            <option value="Madrasta" {{ $pivot->grau_parentesco == 'Madrasta' ? 'selected' : '' }}>Madrasta</option>
            <option value="Outro" {{ $pivot->grau_parentesco == 'Outro' ? 'selected' : '' }}>Outro</option>
        </select>
    </div>
    <div>
        <label style="font-size: 1.4rem;">Início Autorização</label>
        <input type="date" name="data_inicio_autorizacao" value="{{ $pivot->data_inicio_autorizacao }}"
               class="form-control" style="font-size: 1.4rem;" {{ $ehEE ? 'disabled' : '' }}>
    </div>
    <div>
        <label style="font-size: 1.4rem;">Fim Autorização</label>
        <input type="date" name="data_fim_autorizacao" value="{{ $pivot->data_fim_autorizacao }}"
               class="form-control" style="font-size: 1.4rem;" {{ $ehEE ? 'disabled' : '' }}>
    </div>
                

                        

                        {{-- Observações (livres) --}}
<div style="grid-column: 1 / -1;">
    <label style="font-size: 1.4rem;">Observações</label>
    <textarea name="observacoes" id="observacoes-{{ $pivotId }}" class="form-control" style="font-size: 1.4rem;" rows="3"
              placeholder="Ex: Só autorizado a partir das 17h.">{{ $pivot->observacoes }}</textarea>
</div>

{{-- Código para obter $pivot e $pivotId --}}
@php
    $pivot = $utente->pivot; // Ou como estiver a obter o objeto pivot
    // Tente usar o ID diretamente do objeto pivot se disponível e correto
    $pivotIdParaForm = $pivot->id ?? DB::table('responsaveis_utentes')
                                        ->where('responsavel_id', $responsavel->id)
                                        ->where('utente_id', $utente->id)
                                        ->value('id');
@endphp


{{-- O seu código existente para o input hidden e as checkboxes --}}
{{-- Certifique-se de usar $pivotIdParaForm para consistência nos IDs --}}

<input type="hidden" name="dias_nao_autorizados" id="dias-nao-autorizados-{{ $pivotIdParaForm }}"
       value="{{ $pivot->dias_nao_autorizados }}">

<div style="grid-column: 1 / -1; margin-top: 10px;">
    <label>Dias da semana em que <strong>❌ não está autorizado</strong> a recolher:</label>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
        @php
            // Repetir a lógica para garantir que as variáveis usadas pelas checkboxes estão corretas
            $dias_para_checkbox = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
            $marcados_original_cb = $pivot->dias_nao_autorizados ?? '';
            $marcados_array_cb = !empty($marcados_original_cb) ? explode(',', $marcados_original_cb) : [];
            $marcados_para_checkbox = array_map('trim', $marcados_array_cb);
        @endphp
        @foreach ($dias_para_checkbox as $dia_cb)
            <label style="display: flex; align-items: center; gap: 5px; font-size: 1.2rem;">
                <input type="checkbox"
                       class="checkbox-dia"
                       data-dia="{{ $dia_cb }}"
                       data-target="dias-nao-autorizados-{{ $pivotIdParaForm }}"
                       {{ in_array($dia_cb, $marcados_para_checkbox) ? 'checked' : '' }}>
                {{ $dia_cb }}
            </label>
        @endforeach
    </div>
</div>

                




</div>
                    <div style="margin-top: 10px; text-align: right;">
                        <button type="submit" class="btn btn-success btn-sm">💾 Guardar</button>
                        <span class="feedback-assoc" style="margin-left: 10px; font-weight: bold;"></span>
                    </div>
                </form>
            @endforeach
                
        @endif
    </div>
@endforeach

@push('js')
<script>
    // Função para editar os dados do responsável
    function editarResponsavel(id) {
        const visualDiv = document.getElementById('dados-visuais-' + id);
        const formDiv = document.getElementById('form-edicao-' + id);

        if (visualDiv && formDiv) {
            visualDiv.style.display = 'none';   // Esconde os dados visuais
            formDiv.style.display = 'block';    // Mostra o formulário de edição
        }
    }

    // Função para cancelar a edição e voltar aos dados visuais
    function cancelarEdicao(id) {
        const visualDiv = document.getElementById('dados-visuais-' + id);
        const formDiv = document.getElementById('form-edicao-' + id);

        if (visualDiv && formDiv) {
            formDiv.style.display = 'none';   // Esconde o formulário de edição
            visualDiv.style.display = 'block';    // Mostra os dados visuais
        }
    }

    function atualizarEstadoAutorizacao(pivotId, form) {
    const inicio = form.querySelector('[name="data_inicio_autorizacao"]').value;
    const fim = form.querySelector('[name="data_fim_autorizacao"]').value;

    // Normaliza para data apenas (00:00 do dia)
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const inicioDate = inicio ? new Date(inicio) : null;
    const fimDate = fim ? new Date(fim) : null;

    if (inicioDate) inicioDate.setHours(0, 0, 0, 0);
    if (fimDate) fimDate.setHours(0, 0, 0, 0);

    let autorizado = false;
    let pendente = false;

    if ((!inicioDate || inicioDate <= hoje) && (!fimDate || fimDate >= hoje)) {
        autorizado = true;
    } else if (inicioDate && inicioDate > hoje) {
        pendente = true;
    }

    const estadoEl = document.getElementById('estado-autorizacao-' + pivotId);
    const fraseEl = document.getElementById('frase-estado-' + pivotId);

    let icone, cor, texto;
    if (autorizado) {
        icone = '✅';
        cor = 'green';
        texto = 'Autorizado';
    } else if (pendente) {
        icone = '⏳';
        cor = 'orange';
        texto = 'Pendente';
    } else {
        icone = '❌';
        cor = 'red';
        texto = 'Não autorizado';
    }

    if (estadoEl) {
        estadoEl.textContent = `${icone} ${texto}`;
        estadoEl.style.color = cor;
    }

    if (fraseEl) {
        const nomeResponsavel = fraseEl.dataset.responsavel;
        const nomeUtente = fraseEl.dataset.utente;
        fraseEl.innerHTML = `O responsável <strong>${nomeResponsavel}</strong> ${texto === 'Autorizado' ? 'está' : texto === 'Pendente' ? 'estará brevemente' : 'não está'} autorizado para a recolha de <strong>${nomeUtente}</strong>. <span style="color: ${cor}; font-weight: bold;">${icone} ${texto}</span>`;
    }
}




    // Função para submissão de dados de edição do responsável (sem reload)
    function submeterEdicaoResponsavel(event, responsavelId, url) {
    event.preventDefault();

    const form = document.getElementById(`form-edicao-${responsavelId}`);
    const dados = new FormData(form);
    const mensagemEl = document.getElementById(`mensagem-edicao-${responsavelId}`);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: dados
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }
        return data;
    })
    .then(data => {
        if (mensagemEl) {
            mensagemEl.textContent = '✅ Dados atualizados com sucesso!';
            mensagemEl.style.color = 'green';
        }

        const visualDiv = document.getElementById(`dados-visuais-${responsavelId}`);
        const nomeResponsavel = visualDiv?.querySelector('.utente-nome');
        const infoResponsavel = visualDiv?.querySelector('.utente-info');

        if (nomeResponsavel) {
            nomeResponsavel.textContent = ` ${data.nome_completo}`;
        }

        if (infoResponsavel) {
            infoResponsavel.innerHTML = `
                📇 <strong>Nº Identificação: </strong> ${data.nr_identificacao}<br>
                📧 <strong>E-mail: </strong> ${data.email || '-'}<br>
                📞 <strong>Contacto: </strong> ${data.contacto || '-'}
            `;
        }

        form.style.display = 'none';
        if (visualDiv) visualDiv.style.display = 'block';
    })
    .catch(err => {
        if (err.errors) {
            let mensagem = '';
            if (err.errors.email) mensagem += `⚠️ ${err.errors.email[0]}\n`;
            if (err.errors.nr_identificacao) mensagem += `⚠️ ${err.errors.nr_identificacao[0]}`;
            mostrarToast(mensagem.trim(), 'error');
        } else {
            console.error(err);
            if (mensagemEl) {
                mensagemEl.textContent = '❌ Erro de comunicação.';
                mensagemEl.style.color = 'red';
            }
        }
    });
}


    // Função para mostrar toasts de sucesso/erro
    function mostrarToast(mensagem, tipo = 'success') {
        const toast = document.createElement('div');
        toast.innerText = mensagem;
        toast.style.position = 'fixed';
        toast.style.bottom = '30px';
        toast.style.right = '30px';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '8px';
        toast.style.fontSize = '1.4rem';
        toast.style.color = 'white';
        toast.style.zIndex = 9999;
        toast.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';
        toast.style.opacity = 0;
        toast.style.transition = 'opacity 0.4s ease';
        toast.style.backgroundColor = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107'
        }[tipo] || '#17a2b8';
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.style.opacity = 1);
        setTimeout(() => {
            toast.style.opacity = 0;
            setTimeout(() => toast.remove(), 500);
        }, 3500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const associacaoForms = document.querySelectorAll('.form-atualizar-associacao');
        associacaoForms.forEach(form => {
            form.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this); // ← Criar primeiro
    if (formData.has('id')) {
    formData.set('id', this.dataset.pivotId);
} else {
    formData.append('id', this.dataset.pivotId);
}

    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const feedbackElement = this.querySelector('.feedback-assoc');

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw data;
        return data;
    })
    .then(data => {
        feedbackElement.textContent = '✅ Dados atualizados com sucesso!';
        feedbackElement.style.color = 'green';

        const pivotId = this.dataset.pivotId;
        if (pivotId) {
            atualizarEstadoAutorizacao(pivotId, this);
        }

        setTimeout(() => feedbackElement.textContent = '', 3000);
    })
    .catch(error => {
        console.error('Erro na atualização da associação:', error);
        feedbackElement.textContent = error.message || '❌ Erro ao salvar os dados.';
        feedbackElement.style.color = 'red';
        setTimeout(() => feedbackElement.textContent = '', 3000);
    });
});


            // Adiciona event listeners para atualizar o estado de autorização ao mudar as datas
            const dataInicio = form.querySelector('[name="data_inicio_autorizacao"]');
            const dataFim = form.querySelector('[name="data_fim_autorizacao"]');
            const pivotId = form.dataset.pivotId;

            if (dataInicio && dataFim && pivotId) {
                dataInicio.addEventListener('change', () => atualizarEstadoAutorizacao(pivotId, form));
                dataFim.addEventListener('change', () => atualizarEstadoAutorizacao(pivotId, form));
            }
        });
    });

    document.querySelectorAll('.input-foto-resp').forEach(input => {
    input.addEventListener('change', function () {
        const file = this.files[0];
        const inputId = this.id;
        const respId = inputId.replace('input-foto-resp-', '');
        const previewEl = document.getElementById('foto-preview-resp-' + respId);
        const badge = document.getElementById('foto-sucesso-resp-' + respId);

        if (!file || !respId || !previewEl) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewEl.src = e.target.result;
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('foto', file);

        fetch(`/ee/responsavel/${respId}/foto`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                previewEl.src = data.url;
                mostrarToast('📸 Foto atualizada com sucesso!', 'success');
                if (badge) {
                    badge.style.display = 'inline-block';
                    badge.style.opacity = '1';
                    setTimeout(() => {
                        badge.style.opacity = '0';
                        setTimeout(() => badge.style.display = 'none', 300);
                    }, 3000);
                }
            } else {
                mostrarToast('⚠️ ' + (data.message || 'Erro ao atualizar a foto.'), 'error');
            }
        })
        .catch(() => {
            mostrarToast('⚠️ Erro ao comunicar com o servidor.', 'error');
        });
    });
});

function removerResponsavel(id) {
    if (!confirm('Tem a certeza que deseja remover este responsável e todas as suas associações aos seus educandos?')) return;

    fetch(`/ee/responsavel/${id}/remover`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarToast('✅ Responsável removido com sucesso.', 'success');
            const card = document.getElementById(`responsavel-card-${id}`);
            if (card) card.remove();
        } else {
            mostrarToast(data.error || '❌ Erro ao remover o responsável.', 'error');
        }
    })
    .catch(() => {
        mostrarToast('❌ Erro ao comunicar com o servidor.', 'error');
    });
}

document.querySelectorAll('.checkbox-dia').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const targetId = this.dataset.target;
        const inputHidden = document.getElementById(targetId);

        if (!inputHidden) return;

        const allCheckboxes = document.querySelectorAll(`.checkbox-dia[data-target="${targetId}"]`);
        const selecionados = Array.from(allCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.dia);

        inputHidden.value = selecionados.join(',');
    });
});
function enviarQrCodesAjax(event, formEl, responsavelId) {
    event.preventDefault();

    const btn = document.getElementById(`btn-enviar-qrcodes-${responsavelId}`);
    const spinner = document.getElementById(`spinner-qrcodes-${responsavelId}`);
    const feedbackEl = document.getElementById(`feedback-qrcodes-${responsavelId}`);

    if (!btn || !spinner || !feedbackEl) return;

    spinner.style.display = 'inline-block';
    btn.disabled = true;
    feedbackEl.textContent = '';

    fetch(formEl.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        spinner.style.display = 'none';
        btn.disabled = false;

        if (data.success) {
            feedbackEl.textContent = '✅ ' + data.message;
            feedbackEl.style.color = 'green';
        } else {
            feedbackEl.textContent = '❌ ' + (data.message || 'Erro desconhecido.');
            feedbackEl.style.color = 'red';
        }
    })
    .catch(error => {
        spinner.style.display = 'none';
        btn.disabled = false;
        feedbackEl.textContent = '❌ Erro ao comunicar com o servidor.';
        feedbackEl.style.color = 'red';
        console.error('Erro ao enviar QR Codes:', error);
    });
}

function removerResponsavelUnico(responsavelId, utenteId) {
    if (!confirm('Tem a certeza que deseja remover este responsável apenas desta criança?')) return;

    fetch(`/ee/responsavel/${responsavelId}/remover`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ utente_id: utenteId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarToast('✅ Responsável removido desta criança com sucesso.', 'success');

            // Remove visualmente apenas a utente-box dessa criança
            const utenteBox = document.querySelector(`#responsavel-card-${responsavelId} .utente-box[data-utente-id="${utenteId}"]`);
            if (utenteBox) utenteBox.remove();

            // Verifica se o responsável ainda tem outras crianças associadas
            const restanteBoxes = document.querySelectorAll(`#responsavel-card-${responsavelId} .utente-box`);
            if (restanteBoxes.length === 0) {
                const card = document.getElementById(`responsavel-card-${responsavelId}`);
                if (card) card.remove();
            }

        } else {
            mostrarToast(data.error || '❌ Erro ao remover responsável desta criança.', 'error');
        }
    })
    .catch(() => {
        mostrarToast('❌ Erro ao comunicar com o servidor.', 'error');
    });
}




</script>
@endpush