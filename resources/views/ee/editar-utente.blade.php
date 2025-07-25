@extends('layouts.ee-master')

@section('title', 'Atualizar Informações de ' . collect(explode(' ', $utente->name))->only(0, -1)->implode(' '))

<h2 class="fw-bold text-left"></h2>
@section('content')

<style>
/* Base geral */
body {
    font-family: Arial, sans-serif;
    font-size: 1.4rem;
    line-height: 1.5;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
    color: #212529;
}

/* Cartão personalizado */
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
    font-size: 1.4rem;
}

/* Limita largura do cartão em ecrãs médios para cima */
@media (min-width: 576px) {
    .card-custom {
        max-width: 500px;
    }
}

/* Tipografia adaptada para ecrãs pequenos */
@media (max-width: 575.98px) {
    body {
        font-size: 1.05rem; /* aumenta apenas o corpo, não o html */
    }

    .form-control,
.btn,
label,
input,
select,
textarea {
    font-size: 1.4rem;
}


h2, h5, h6, label {
    font-size: 1.4rem;
}

}

/* Botão de voltar ao dashboard */
.btn-voltar-dashboard {
    display: inline-block;
    margin-bottom: 1.5rem;
    padding: 10px 18px;
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 8px;
    text-decoration: none;
    font-size: 1rem;
    transition: background-color 0.2s ease;
}

.btn-voltar-dashboard:hover {
    background-color: #e0e0e0;
    color: #000;
}

/* Estilo para os títulos das secções */
.card-custom h5 {
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 1.3rem;
    color: #333;
    font-weight: 600;
}

.card-custom h5 span {
    font-size: 1.4rem;
    margin-right: 10px;
}
</style>


<a href="{{ route('ee.gestao') }}" class="btn-voltar-dashboard">← Voltar ao Dashboard</a>



{{-- FORMULÁRIO PRINCIPAL DE EDIÇÃO --}}
<form method="POST" action="{{ route('ee.utente.update', $utente->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')


    {{-- DADOS DO EDUCANDO --}}
    <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h5 style="margin-bottom: 1rem; display: flex; align-items: center; font-size: 1.3rem; color: #333; font-weight: 600;">
        <span style="font-size: 1.4rem; margin-right: 10px;">🧒</span> Dados do Educando
    </h5>

    <div class="mb-3">
        <label>Nome completo</label>
        <input type="text" name="name" value="{{ $utente->name }}" class="form-control">
    </div>

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="_snipeit_data_nascimento_34">Data de Nascimento</label>
            <input type="date" name="_snipeit_data_nascimento_34" value="{{ old('_snipeit_data_nascimento_34', $utente->_snipeit_data_nascimento_34) }}" class="form-control">
        </div>

        @php
            use Carbon\Carbon;
            $idade = $utente->_snipeit_data_nascimento_34 ? Carbon::parse($utente->_snipeit_data_nascimento_34)->age : '';
        @endphp

        <div class="mb-3 col-md-6">
            <label for="_snipeit_idade_35">Idade</label>
            <input type="text" name="_snipeit_idade_35" value="{{ old('_snipeit_idade_35', $idade) }}" class="form-control" readonly>
        </div>
    </div>
    </div>

    {{-- ESCOLA --}}

    <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h5 style="margin-bottom: 1rem; display: flex; align-items: center; font-size: 1.3rem; color: #333; font-weight: 600;">
        <span style="font-size: 1.4rem; margin-right: 10px;">🏫</span> Escola
    </h5>
    <div class="mb-3">
        <label for="company_id">Escola a frequentar</label>
        <select name="company_id" id="company_id" class="form-control">
            <option value="">Escolha a Escola</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id', $utente->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="_snipeit_ano_esc_58">Ano de Escolaridade</label>
            <select class="form-control" id="_snipeit_ano_esc_58" name="_snipeit_ano_esc_58">
                <option value="">Escolha o Ano</option>
                @foreach(['pré-escolar','1º ano','2º ano','3º ano','4º ano','5º ano','6º ano','7º ano'] as $ano)
                    <option value="{{ $ano }}" {{ old('_snipeit_ano_esc_58', $utente->_snipeit_ano_esc_58 ?? '') == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 col-md-6">
            <label>Turma</label>
            <select name="_snipeit_turma_59" id="_snipeit_turma_59" class="form-control">
                <option value="">Escolha a Turma</option>
                @foreach(['A', 'B', 'C'] as $turma)
                    <option value="{{ $turma }}" {{ old('_snipeit_turma_59', $utente->_snipeit_turma_59) == $turma ? 'selected' : '' }}>{{ $turma }}</option>
                @endforeach
            </select>
        </div>
    </div>
    </div>

    {{-- MORADA --}}
<div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h5 style="margin-bottom: 1rem; display: flex; align-items: center; font-size: 1.3rem; color: #333; font-weight: 600;">
        <span style="font-size: 1.4rem; margin-right: 10px;">🏠</span> Morada
    </h5>

    <div class="row">
        <div class="mb-3 col-md-4">
            <label for="_snipeit_distrito_61">Distrito</label>
            <select name="_snipeit_distrito_61" id="_snipeit_distrito_61" class="form-control" required>
                <option value="">Escolha o Distrito</option>
                @foreach(['Aveiro','Beja','Braga','Bragança','Castelo Branco','Coimbra','Évora','Faro','Guarda','Leiria','Lisboa','Portalegre','Porto','Santarém','Setúbal','Viana do Castelo','Vila Real','Viseu','Região Autónoma dos Açores','Região Autónoma da Madeira'] as $distrito)
                    <option value="{{ $distrito }}" {{ old('_snipeit_distrito_61', $utente->_snipeit_distrito_61 ?? '') == $distrito ? 'selected' : '' }}>{{ $distrito }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 col-md-4">
            <label for="_snipeit_concelho_74">Concelho</label>
            <select name="_snipeit_concelho_74" id="_snipeit_concelho_74" class="form-control" data-selected="{{ old('_snipeit_concelho_74', $utente->_snipeit_concelho_74 ?? '') }}" required>
                <option value="">Escolha o Concelho</option>
            </select>
        </div>

        <div class="mb-3 col-md-4">
            <label for="_snipeit_freguesia_62">Freguesia</label>
            <select name="_snipeit_freguesia_62" id="_snipeit_freguesia_62" class="form-control" data-selected="{{ old('_snipeit_freguesia_62', $utente->_snipeit_freguesia_62 ?? '') }}" required>
                <option value="">Escolha a Freguesia</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label>Morada (Andar/Fracção/Lugar/Bairro)</label>
        <input type="text" name="_snipeit_morada_no_andar_fracao_lugar_bairro_32" value="{{ old('_snipeit_morada_no_andar_fracao_lugar_bairro_32', $utente->_snipeit_morada_no_andar_fracao_lugar_bairro_32) }}" class="form-control">
    </div>

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="_snipeit_codigo_postal_33">Código Postal</label>
            <input type="text" name="_snipeit_codigo_postal_33" id="_snipeit_codigo_postal_33" value="{{ old('_snipeit_codigo_postal_33', $utente->_snipeit_codigo_postal_33 ?? '') }}" class="form-control">
        </div>
        <div class="mb-3 col-md-6">
            <label for="_snipeit_localidade_75">Localidade</label>
            <input type="text" name="_snipeit_localidade_75" id="_snipeit_localidade_75" value="{{ old('_snipeit_localidade_75', $utente->_snipeit_localidade_75 ?? '') }}" class="form-control">
        </div>
    </div>
    </div>
    {{-- DOCUMENTO --}}
    <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h5 style="margin-bottom: 1rem; display: flex; align-items: center; font-size: 1.3rem; color: #333; font-weight: 600;">
        <span style="font-size: 1.4rem; margin-right: 10px;">🪪</span> Documentos de Identificação
    </h5>

    <div class="row">
    {{-- Documento de Identificação --}}
    <div class="mb-3 col-md-6">
        <label for="serial">Nº Documento</label>
        <input type="text" name="serial" id="serial" class="form-control" value="{{ old('serial', $utente->serial) }}">
    </div>

    <div class="mb-3 col-md-6">
        <label for="_snipeit_data_validade_documento_de_identificacao_37">Data de Validade</label>
        <input type="date" name="_snipeit_data_validade_documento_de_identificacao_37" id="_snipeit_data_validade_documento_de_identificacao_37"
               class="form-control"
               value="{{ old('_snipeit_data_validade_documento_de_identificacao_37', $utente->_snipeit_data_validade_documento_de_identificacao_37 ? \Carbon\Carbon::parse($utente->_snipeit_data_validade_documento_de_identificacao_37)->format('Y-m-d') : '') }}"
               required>
    </div>

    {{-- NIF e Utente SNS --}}
    <div class="mb-3 col-md-6">
        <label for="_snipeit_nif_30">NIF</label>
        <input type="text" name="_snipeit_nif_30" id="_snipeit_nif_30" class="form-control"
               value="{{ old('_snipeit_nif_30', $utente->_snipeit_nif_30) }}" maxlength="9" required>
    </div>

    <div class="mb-3 col-md-6">
        <label for="_snipeit_utente_sns_29">Utente SNS</label>
        <input type="text" name="_snipeit_utente_sns_29" id="_snipeit_utente_sns_29" class="form-control"
               value="{{ old('_snipeit_utente_sns_29', $utente->_snipeit_utente_sns_29) }}" maxlength="9" required>
    </div>

    {{-- Segurança Social e Contacto de Emergência --}}
    <div class="mb-3 col-md-6">
        <label for="_snipeit_seg_social_31">Segurança Social</label>
        <input type="text" name="_snipeit_seg_social_31" id="_snipeit_seg_social_31" class="form-control"
               value="{{ old('_snipeit_seg_social_31', $utente->_snipeit_seg_social_31) }}" maxlength="11" required>
    </div>

    <div class="mb-3 col-md-6">
        <label for="_snipeit_contacto_de_emergencia_47">Contacto de Emergência</label>
        <input type="text" name="_snipeit_contacto_de_emergencia_47" id="_snipeit_contacto_de_emergencia_47"
               class="form-control"
               value="{{ old('_snipeit_contacto_de_emergencia_47', $utente->_snipeit_contacto_de_emergencia_47) }}">
    </div>
</div>
</div>

    

   
{{-- Fim do formulário principal de atualização --}}
<div id="bloco-sucesso" style="margin-top: 3rem !important;">
    <button type="submit" class="btn btn-primary" style="margin-top: 10px; background: green;">
        Guardar os dados
    </button>
    </form>
  
    <div id="feedback-sucesso-container" style="min-height: 48px; margin-top: 10px;">
        <div id="feedback-sucesso"
             role="alert"
             style="opacity: 0; visibility: hidden; transition: opacity 0.5s ease; background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; border-radius: 4px; padding: 10px 14px; display: flex; align-items: center;">
            <span style="font-size: 1.2rem;"></span>
            <span id="mensagem-feedback-sucesso" style="margin-left: 8px;">
                {{ session('success') ?? '' }}
            </span>
        </div>
    </div>


 


    <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h5 style="margin-bottom: 1rem; display: flex; align-items: center; font-size: 1.3rem; color: #333; font-weight: 600;">
        <span style="font-size: 1.4rem; margin-right: 10px;">📎</span> Anexos
    </h5>

    <form id="form-upload-anexo" action="{{ route('ee.utente.uploadAnexo', $utente->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="ficheiro">Ficheiro</label>
            <input type="file" name="ficheiro" id="ficheiro" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="nota">Nota associada</label>
            <textarea name="nota" id="nota" class="form-control" rows="2" placeholder="Ex: Comprovativo médico, declaração, etc."></textarea>
        </div>

        <button type="submit" class="btn btn-success" style="margin-top: 10px" id="btn-upload-anexo">Carregar Ficheiro com Nota</button>
    </form>

    @if ($utente->uploads && $utente->uploads->count())
        <div class="mt-3">
            <h6 class="mb-2">📁 Ficheiros já anexados:</h6>
            <ul class="list-group" id="listaFicheiros">
            <div class="alert alert-info mt-3" style="font-size: 14px;">
    <strong>ℹ️ Informação:</strong> Os documentos submetidos não podem ser removidos diretamente por este portal.
    <br>
    Caso pretenda eliminar ou corrigir algum documento, contacte-nos através do email
    <a href="mailto:parque.seguro@jf-parquedasnacoes.pt">parque.seguro@jf-parquedasnacoes.pt</a>.
</div>

                @foreach($utente->uploads as $ficheiro)
                    <li class="list-group-item">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                            <a href="{{ route('ee.utente.verFicheiro', ['id' => $utente->id, 'filename' => $ficheiro->filename]) }}" target="_blank">
    {{ $ficheiro->filename }}
</a>

                                @if($ficheiro->note)
                                    <p class="mb-0"><small><strong>Nota:</strong> {{ $ficheiro->note }}</small></p>
                                @endif
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($ficheiro->created_at)->format('d/m/Y H:i') }}</small>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

</div>
{{-- INFORMAÇÕES DE SAÚDE --}}
<div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
    <h5 style="margin-bottom: 1rem; display: flex; align-items: center; font-size: 1.3rem; color: #333; font-weight: 600;">
        <span style="font-size: 1.4rem; margin-right: 10px;">🏥</span> Informações de saúde
    </h5>

    <div class="alert alert-info" role="alert" style="margin-top: 0.5rem; margin-bottom: 1rem; font-size: 14px;">
        <strong>ℹ️ Informação:</strong> Os dados de saúde são <strong>exclusivamente informativos</strong> e não podem ser alterados neste portal.
        <br>
        Qualquer alteração ou comunicação de novas situações de saúde deverá ser feita através do e-mail
        <a href="mailto:parque.seguro@jf-parquedasnacoes.pt">parque.seguro@jf-parquedasnacoes.pt</a>.
    </div>

    <div class="mb-3">
        <label>Doença(s) ou limitação de saúde (alergias, cuidados especiais, etc.)</label>
        <input type="text" name="_snipeit_tem_algum_problema_de_saude_41" value="{{ old('_snipeit_tem_algum_problema_de_saude_41', $utente->_snipeit_tem_algum_problema_de_saude_41) }}" class="form-control" disabled>
    </div>
    <div class="mb-3">
        <label>Restrições Alimentares</label>
        <input type="text" name="_snipeit_restricoes_alimentares_42" value="{{ old('_snipeit_restricoes_alimentares_42', $utente->_snipeit_restricoes_alimentares_42) }}" class="form-control" disabled>
    </div>
</div>

    <!-- Modal Personalizado -->
    <div id="modal-eliminar-nota" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 8px; padding: 20px; max-width: 400px; width: 90%;">
        <h5 style="margin-bottom: 10px; color: #c0392b;">❗ Eliminar Nota</h5>
        <p style="margin-bottom: 20px;">Tem a certeza que pretende eliminar esta nota? Esta ação é irreversível.</p>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" id="btn-cancelar-modal" class="btn btn-secondary">Cancelar</button>
            <button type="button" id="btn-confirmar-modal" class="btn btn-danger">Eliminar</button>
        </div>
    </div>
    </div>


<div id="spinner-verificacao-virus" style="
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 99999;
    background-color: white;
    padding: 20px 30px;
    border-radius: 10px;
    border: 2px solid #198754;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    font-size: 1.6rem;
    font-weight: 600;
    color: #198754;
">
    🛡️ A verificar o ficheiro por vírus... Por favor aguarde.
</div>

@endsection
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let notaAEliminar = null;

    // ============
    // Regiões PT
    // ============
    let dadosPortugal = {};
    const distritoSelect = document.getElementById('_snipeit_distrito_61');
    const concelhoSelect = document.getElementById('_snipeit_concelho_74');
    const freguesiaSelect = document.getElementById('_snipeit_freguesia_62');

    fetch('/js/freguesias_concelhos_distritos_utf8.json')
        .then(response => response.json())
        .then(data => {
            dadosPortugal = data;
            const distritoAtual = distritoSelect.value;
            const concelhoAtual = concelhoSelect.dataset.selected;
            const freguesiaAtual = freguesiaSelect.dataset.selected;

            if (distritoAtual) {
                preencherConcelhos(distritoAtual, concelhoAtual);
                if (concelhoAtual) preencherFreguesias(distritoAtual, concelhoAtual, freguesiaAtual);
            }
        });

    distritoSelect.addEventListener('change', function () {
        preencherConcelhos(this.value);
        freguesiaSelect.innerHTML = '<option value="">Escolha a Freguesia</option>';
    });

    concelhoSelect.addEventListener('change', function () {
        preencherFreguesias(distritoSelect.value, this.value);
    });

    function preencherConcelhos(distrito, concelhoSelecionado = null) {
        concelhoSelect.innerHTML = '<option value="">Escolha o Concelho</option>';
        if (!dadosPortugal[distrito]) return;
        Object.keys(dadosPortugal[distrito]).forEach(concelho => {
            const option = document.createElement('option');
            option.value = concelho;
            option.textContent = concelho;
            if (concelho === concelhoSelecionado) option.selected = true;
            concelhoSelect.appendChild(option);
        });
    }

    function preencherFreguesias(distrito, concelho, freguesiaSelecionada = null) {
        freguesiaSelect.innerHTML = '<option value="">Escolha a Freguesia</option>';
        const freguesias = dadosPortugal[distrito]?.[concelho] || [];
        freguesias.forEach(freguesia => {
            const option = document.createElement('option');
            option.value = freguesia;
            option.textContent = freguesia;
            if (freguesia === freguesiaSelecionada) option.selected = true;
            freguesiaSelect.appendChild(option);
        });
    }

    // ===============
    // Upload Anexo
    // ===============
    const formUpload = document.getElementById('form-upload-anexo');
    const btnUpload = document.getElementById('btn-upload-anexo');
    formUpload.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(formUpload);
    btnUpload.disabled = true;
    btnUpload.textContent = 'A carregar...';

    // ⏳ MOSTRA O SPINNER
    document.getElementById('spinner-verificacao-virus').style.display = 'block';

    fetch(formUpload.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                let ul = document.querySelector('#listaFicheiros');
                if (!ul) {
                    const title = document.createElement('h6');
                    title.className = 'mb-2 mt-3';
                    title.innerText = '📁 Ficheiros já anexados:';
                    ul = document.createElement('ul');
                    ul.className = 'list-group';
                    formUpload.insertAdjacentElement('afterend', title);
                    title.insertAdjacentElement('afterend', ul);
                }

                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="${data.url}" target="_blank">${data.filename}</a>
                            ${data.filenote ? `<p class="mb-0"><small><strong>Nota:</strong> ${data.filenote}</small></p>` : ''}
                        </div>
                        <small class="text-muted">${data.created_at}</small>
                    </div>
                `;
                ul.prepend(li);
                document.getElementById('ficheiro').value = '';
                document.getElementById('nota').value = '';
                alert('Ficheiro carregado com sucesso!');
            } else {
                alert(data.error || 'Erro ao carregar ficheiro.');
            }
        })
        .catch(err => {
            console.error('Erro AJAX:', err);
            alert('Erro inesperado ao carregar ficheiro.');
        })
        .finally(() => {
            // ✅ ESCONDE O SPINNER
            document.getElementById('spinner-verificacao-virus').style.display = 'none';

            btnUpload.disabled = false;
            btnUpload.textContent = 'Carregar Ficheiro com Nota';
        });
});

   

    // ======================
    // Alertas Visuais
    // ======================
    const alertaFoto = document.getElementById('alerta-foto');
    const mensagemFoto = document.getElementById('mensagem-alerta-foto');

    if (mensagemFoto && mensagemFoto.textContent.trim() !== '') {
        alertaFoto.style.opacity = '1';
        alertaFoto.style.visibility = 'visible';

        setTimeout(() => {
            alertaFoto.style.opacity = '0';
            alertaFoto.style.visibility = 'hidden';
        }, 3000);
    }

    const alertaSucesso = document.getElementById('feedback-sucesso');
    const mensagemSucesso = document.getElementById('mensagem-feedback-sucesso');

    if (mensagemSucesso && mensagemSucesso.textContent.trim() !== '') {
        alertaSucesso.style.opacity = '1';
        alertaSucesso.style.visibility = 'visible';

        const bloco = document.getElementById('bloco-sucesso');
        if (bloco && typeof bloco.scrollIntoView === 'function') {
            setTimeout(() => {
                bloco.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

        setTimeout(() => {
            alertaSucesso.style.opacity = '0';
            alertaSucesso.style.visibility = 'hidden';
        }, 3000);
    }




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


});
</script>


@endpush
