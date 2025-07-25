@extends('layouts/default')

@section('title', 'Notificações Portal EE')

@push('css')
<style>
    /* Estilos base para notificações já lidas (mantenha os seus) */
    .notificacao-lida td {
        background-color: #f9f9f9 !important;
    }
    /* ... outros estilos .notificacao-lida que já tem ... */

    /* --- ESTILOS PARA A LINHA DESTACADA --- */

    /* Estilos gerais para TODAS as células (td) de uma linha destacada */
    /* Aplica-se quer a notificação já estivesse lida ou não */
    tr.notificacao-destacada td {
        font-weight: bold !important; /* Texto a negrito */
        /* Se quiser uma cor de texto diferente para toda a linha destacada: */
        /* color: #333 !important; */
    }

    /* Fundo e animação para as células da linha destacada */
    /* Estes seletores garantem que se sobrepõem ao .notificacao-lida td */
    tr.notificacao-lida.notificacao-destacada td,
    tr:not(.notificacao-lida).notificacao-destacada td {
        background-color: #d9edf7 !important; /* Cor de fundo azul claro para o destaque */
        animation: flashDestacadoTd 0.5s ease-in-out 0s 4 alternate; /* Animação de piscar */
    }

    /* Borda à esquerda da linha destacada (aplicada à primeira célula) */
    tr.notificacao-destacada td:first-child {
        border-left: 4px solid #0073e6 !important; /* Borda azul escura */
    }

    /* --- ESTILOS ESPECÍFICOS PARA A CÉLULA DA MENSAGEM (3ª célula) QUANDO DESTACADA --- */
    tr.notificacao-destacada td:nth-child(3) { /* Alveja a terceira célula (mensagem) */
        /* ADICIONE AQUI OS ESTILOS QUE DESEJA PARA "MARCAR" A MENSAGEM ESPECÍFICA */

        /* Exemplo 1: Sublinhar o texto da mensagem */
        text-decoration: underline !important;
        text-decoration-color: #0073e6 !important; /* Cor do sublinhado */
        text-underline-offset: 3px !important; /* Espaço entre texto e sublinhado */

        /* Exemplo 2: Mudar a cor do texto da mensagem para algo mais proeminente */
        /* color: #0056b3 !important; */

        /* Exemplo 3: Adicionar um estilo de fonte diferente, como itálico */
        /* font-style: italic !important; */

        /* Exemplo 4: Se quiser um fundo ligeiramente diferente APENAS para a célula da mensagem */
        /* Isto sobreporia o background-color definido acima para esta célula específica */
        /* background-color: #e6f3ff !important; */
    }

    /* Definição da animação para o piscar das células */
    @keyframes flashDestacadoTd {
        from {
            background-color: #cce5ff; /* Cor do "flash" (um azul um pouco mais claro) */
        }
        to {
            background-color: #d9edf7; /* Cor de fundo base do destaque (para onde a animação retorna) */
        }
    }
#loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 10000; /* Alto z-index para ficar por cima */
        display: none; /* << IMPORTANTE: Garanta que isto está aqui e não foi alterado para 'flex' ou 'block' */
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 1.5em;
    }
    /* ... (seus outros estilos como loading-overlay, etc.) ... */

</style>
@endpush

@section('content')
<div id="loading-overlay">A carregar...</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Notificações (<span id="contador-nao-lidas">0</span> não lidas)</h3>
        <div class="box-tools pull-right">
            <button id="btnMarcarTodasLidas" class="btn btn-sm btn-success" style="margin-left: 10px;">Marcar todas como lidas</button>
        </div>
    </div>

    {{-- Filtros --}}
    <form id="formFiltrosNotificacoes" method="GET" action="{{ route('notificacoes.index') }}" class="mb-3 p-3 border-bottom bg-light">
        <div class="row">
            <div class="col-md-2">
                <select name="lida" class="form-control filter-input">
                    <option value="">Todas</option>
                    <option value="1" {{ request('lida') === '1' ? 'selected' : '' }}>Apenas Lidas</option>
                    <option value="0" {{ request('lida') === '0' ? 'selected' : '' }}>Apenas Por Ler</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="data_inicio" class="form-control filter-input" value="{{ request('data_inicio') }}" placeholder="De">
            </div>
            <div class="col-md-2">
                <input type="date" name="data_fim" class="form-control filter-input" value="{{ request('data_fim') }}" placeholder="Até">
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-control filter-input">
                    <option value="">Todos os Tipos</option>
                    @if(isset($tiposDisponiveis))
                        @foreach($tiposDisponiveis as $tipoOption)
                            <option value="{{ $tipoOption }}" {{ request('tipo') === $tipoOption ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $tipoOption)) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="pesquisa" class="form-control filter-input" placeholder="Pesquisar na mensagem" value="{{ request('pesquisa') }}">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('notificacoes.index') }}" class="btn btn-default" id="btnLimparFiltros">Limpar Filtros</a>
            </div>
        </div>
    </form>

    {{-- Container para a tabela de notificações e paginação --}}
    <div id="notificacoesContainer" class="box-body table-responsive">
        @include('notificacoes.partials.lista_notificacoes', ['notificacoes' => $notificacoes])
    </div>
</div>
@endsection

@push('scripts')
<script>
// Função destacadora definida no escopo global
function destacarNotificacaoSeExistir() {
    console.log('[Notificações Debug] Iniciando destacarNotificacaoSeExistir(). Hora:', new Date().toLocaleTimeString()); // LOG ADICIONADO
    const hash = window.location.hash;
    console.log('[Notificações Debug] Hash da URL:', hash); // LOG ADICIONADO

    if (hash && hash.startsWith('#notificacao-')) { // Adicionei 'hash &&' para segurança
        console.log('[Notificações Debug] Tentando encontrar o elemento com o seletor:', hash); // LOG ADICIONADO
        const alvo = document.querySelector(hash);

        if (alvo) {
            console.log('[Notificações Debug] Elemento ENCONTRADO:', alvo); // LOG ADICIONADO
            console.log('[Notificações Debug] Classes atuais do alvo ANTES de adicionar "notificacao-destacada":', alvo.className); // LOG ADICIONADO

            alvo.classList.add('notificacao-destacada');
            console.log('[Notificações Debug] Classe "notificacao-destacada" ADICIONADA. Classes atuais:', alvo.className); // LOG ADICIONADO

            // Forçar um reflow do browser pode ajudar em casos raros de timing de renderização.
            void alvo.offsetWidth;

            alvo.scrollIntoView({ behavior: 'smooth', block: 'center' });
            console.log('[Notificações Debug] scrollIntoView() chamado para o alvo.'); // LOG ADICIONADO

            // Aumentar o tempo do timeout para facilitar a observação
            setTimeout(() => {
                console.log('[Notificações Debug] Timeout: Removendo "notificacao-destacada" do elemento:', alvo); // LOG ADICIONADO
                alvo.classList.remove('notificacao-destacada');
                console.log('[Notificações Debug] Classe "notificacao-destacada" REMOVIDA. Classes atuais:', alvo.className); // LOG ADICIONADO
            }, 8000); // Aumentado para 8 segundos

        } else {
            console.error('[Notificações Debug] Elemento NÃO ENCONTRADO com o seletor:', hash); // LOG ADICIONADO
            const idsNaPagina = Array.from(document.querySelectorAll('tr[id^="notificacao-"]')).map(tr => tr.id);
            console.log('[Notificações Debug] IDs de notificação TR presentes na página:', idsNaPagina); // LOG ADICIONADO
        }
    } else {
        console.log('[Notificações Debug] Nenhum hash relevante (começando com #notificacao-) foi encontrado na URL.'); // LOG ADICIONADO
    }
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('Script de Notificações DOMContentLoaded'); // Log inicial

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const loadingOverlay = document.getElementById('loading-overlay');
    const formFiltros = document.getElementById('formFiltrosNotificacoes');
    const notificacoesContainer = document.getElementById('notificacoesContainer');
    const contadorNaoLidasEl = document.getElementById('contador-nao-lidas');

    // Valida elementos críticos
    if (!csrfToken) console.error('CSRF token não encontrado!');
    if (!loadingOverlay) console.error('Elemento loading-overlay não encontrado!');
    if (!formFiltros) console.error('Elemento formFiltrosNotificacoes não encontrado!');
    if (!notificacoesContainer) console.error('Elemento notificacoesContainer não encontrado!');
    if (!contadorNaoLidasEl) console.error('Elemento contador-nao-lidas não encontrado!');

    function showLoading(action = 'Ação') {
        if (loadingOverlay) loadingOverlay.style.display = 'flex';
    }
    function hideLoading(action = 'Ação') {
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    }

    function exibirMensagem(mensagem, tipo = 'success') {
        console.log(`Mensagem (${tipo}): ${mensagem}`);
    }

    function atualizarContadorNotificacoes() {
        fetch('{{ route("notificacoes.contagem") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (contadorNaoLidasEl && typeof data.nao_lidas !== 'undefined') {
                contadorNaoLidasEl.textContent = data.nao_lidas;
            }
        });
    }

    async function handleFetchResponse(response, actionContext = "Ação") {
        if (!response.ok) {
            const errorText = await response.text();
            try {
                const json = JSON.parse(errorText);
                throw new Error(json.message || errorText);
            } catch {
                throw new Error(errorText);
            }
        }
        const contentType = response.headers.get("content-type");
        if (contentType.includes("application/json")) {
            return response.json();
        } else {
            throw new Error("Resposta inesperada (não JSON).");
        }
    }

    function aplicarFiltros(url) {
        showLoading();
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => handleFetchResponse(response, 'aplicarFiltros'))
        .then(data => {
            if (data.html) {
                notificacoesContainer.innerHTML = data.html;
                window.history.pushState({ path: url }, '', url);
                reattachAllListeners();
                destacarNotificacaoSeExistir(); // ⬅️ Aqui é aplicado após filtros
            }
        })
        .catch(error => exibirMensagem(error.message, 'error'))
        .finally(() => hideLoading());
    }

    function handleMarcarLidaClick(event) {
        if (!event.target.classList.contains('btn-marcar-lida')) return;
        event.preventDefault();
        const id = event.target.dataset.id;
        showLoading();
        fetch(`/notificacoes/${id}/marcar-lida`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => handleFetchResponse(response, 'marcarLida'))
        .then(data => {
            const row = document.getElementById(`notificacao-${id}`);
            if (row) {
                row.classList.add('notificacao-lida');
                const statusCell = row.querySelector('.status-lida-cell');
                if (statusCell) statusCell.innerHTML = '<span class="label label-success">Lida</span>';
                const btn = row.querySelector('.btn-marcar-lida');
                if (btn) btn.classList.add('hidden');
            }
            atualizarContadorNotificacoes();
        })
        .catch(error => exibirMensagem(error.message, 'error'))
        .finally(() => hideLoading());
    }

    function handleMarcarTodasLidasClick() {
        if (!confirm('Deseja marcar todas como lidas?')) return;
        showLoading();
        fetch('{{ route("notificacoes.marcarTodasLidas") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => handleFetchResponse(response, 'marcarTodas'))
        .then(() => {
            document.querySelectorAll('#notificacoesTableBody tr').forEach(row => {
                row.classList.add('notificacao-lida');
                const status = row.querySelector('.status-lida-cell');
                if (status) status.innerHTML = '<span class="label label-success">Lida</span>';
                const btn = row.querySelector('.btn-marcar-lida');
                if (btn) btn.classList.add('hidden');
            });
            atualizarContadorNotificacoes();
        })
        .catch(error => exibirMensagem(error.message, 'error'))
        .finally(() => hideLoading());
    }

    function handlePaginacaoClick(event) {
        if (event.target.tagName === 'A' && event.target.closest('.pagination')) {
            event.preventDefault();
            aplicarFiltros(event.target.href);
        }
    }

    function reattachAllListeners() {
        if (notificacoesContainer) {
            notificacoesContainer.removeEventListener('click', handleMarcarLidaClick);
            notificacoesContainer.addEventListener('click', handleMarcarLidaClick);
            notificacoesContainer.removeEventListener('click', handlePaginacaoClick);
            notificacoesContainer.addEventListener('click', handlePaginacaoClick);
        }
    }

    // Eventos iniciais
    if (formFiltros) {
        formFiltros.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(formFiltros);
            const queryString = new URLSearchParams(formData).toString();
            const url = '{{ route("notificacoes.index") }}' + (queryString ? '?' + queryString : '');
            aplicarFiltros(url);
        });
    }

    const btnLimparFiltros = document.getElementById('btnLimparFiltros');
    if (btnLimparFiltros) {
        btnLimparFiltros.addEventListener('click', function (e) {
            e.preventDefault();
            if (formFiltros) {
                formFiltros.reset();
                formFiltros.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            }
            aplicarFiltros('{{ route("notificacoes.index") }}');
        });
    }

    const btnMarcarTodasLidas = document.getElementById('btnMarcarTodasLidas');
    if (btnMarcarTodasLidas) {
        btnMarcarTodasLidas.addEventListener('click', handleMarcarTodasLidasClick);
    }

    atualizarContadorNotificacoes();
    reattachAllListeners();
    destacarNotificacaoSeExistir(); // ⬅️ Também na primeira carga

});
</script>

@endpush
