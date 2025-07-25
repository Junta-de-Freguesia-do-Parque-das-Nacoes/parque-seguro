@extends('layouts.default')

@section('title', 'Detalhes do Responsável')

@section('content')

<div class="container mt-4">
    <!-- 🔹 Cabeçalho -->
    <div class="d-flex flex-column flex-md-row  justify-content-between mb-3">
        <h1 class="left text-md-left">Detalhes do Responsável</h1>
    </div>

     <!-- 🔹 Seção Principal -->
    <div class="card shadow-sm p-4">
    <div class="row">
    <!-- 📸 Foto do Responsável -->
    <div class="col-md-3 text-center mb-3 mb-md-0">
        <img src="{{ $responsavel->foto ? route('responsaveis.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}" 
             alt="Foto de {{ $responsavel->nome_completo }}" 
             class="img-thumbnail shadow-sm rounded"
             style="max-width: 70%; height: auto; object-fit: cover;">

        <div class="mt-3 text-center">
            <a href="{{ route('responsaveis.edit', ['responsavelId' => $responsavel->id]) }}" 
               class="btn btn-warning btn-sm mt-3">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

            <!-- ℹ️ Informações do Responsável -->
            <div class="col-md-9">
                <h2 class="text-left text-md-left"><strong>{{ $responsavel->nome_completo }}</strong></h2>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Identificação:</strong> {{ $responsavel->nr_identificacao }}</p>
                        <p><strong>Telefone:</strong> {{ $responsavel->contacto }}</p>
                        <p><strong>Email:</strong> {{ $responsavel->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Criado Por:</strong>
                            {{ $responsavel->adicionadoPor?->first_name ?? 'Desconhecido' }}
                            {{ $responsavel->adicionadoPor?->last_name ?? '' }}
                            em {{ \Carbon\Carbon::parse($responsavel->adicionado_em)->format('d/m/Y H:i') }}
                        </p>

                        <p><strong>Modificado Por:</strong>
                            {{ $responsavel->modificadoPor?->first_name ?? 'Desconhecido' }}
                            {{ $responsavel->modificadoPor?->last_name ?? '' }}
                            em {{ \Carbon\Carbon::parse($responsavel->modificado_em)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 Botão Adicionar Utente -->
    <div class="mt-3 text-right">
        <button class="btn btn-success" data-toggle="modal" data-target="#adicionarUtenteModal">
            <i class="fas fa-user-plus"></i> Adicionar Utente
        </button>
    </div>

<!-- Modal para adicionar utente -->
<div class="modal fade" id="adicionarUtenteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Utente ao Responsável</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="adicionarUtenteForm">
                    @csrf
                    <input type="hidden" name="responsavel_id" value="{{ $responsavel->id }}">
                    <input type="hidden" name="utente_id" id="utente_id">

                    <!-- 🔍 Campo de Pesquisa -->
                    <div class="form-group">
                        <label for="searchUtente">Pesquisar Utente (Nome ou NIF)</label>
                        <input type="text" id="searchUtente" class="form-control" placeholder="Digite o Nome ou NIF..." autocomplete="off">
                        <div id="resultadosUtentes" class="list-group mt-2"></div>
                    </div>

                    <div class="form-group">
                        <label for="grau_parentesco">Grau de Parentesco</label>
                        <select class="form-control" id="grau_parentesco" name="grau_parentesco" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="Pai">Pai</option>
                            <option value="Mãe">Mãe</option>
                            <option value="Avô">Avô</option>
                            <option value="Avó">Avó</option>
                            <option value="Tio">Tio</option>
                            <option value="Tia">Tia</option>
                            <option value="Irmão">Irmão(ã)</option>
                            <option value="Padrasto">Padrasto</option>
                            <option value="Madrasta">Madrasta</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tipo_responsavel">Tipo de Responsável</label>
                        <select class="form-control" id="tipo_responsavel" name="tipo_responsavel" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="Encarregado de Educacao">Encarregado de Educação</option>
                            <option value="Autorizado">Autorizado</option>
                            <option value="Autorizado Excecional">Autorizado Excecional</option>
                        </select>
                    </div>




                    <div class="form-group">
                        <label for="data_inicio">Início da Autorização</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio_autorizacao">
                    </div>

                    <div class="form-group">
                        <label for="data_fim">Fim da Autorização</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim_autorizacao">
                    </div>
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-info-circle"></i> Se não definir as datas, o responsável terá autorização permanente.
                        <br><strong>Sugestão: </strong>Definir até o fim do ano letivo para renovação da autorização.
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Adicionar Utente</button>
                </form>
            </div>
        </div>
    </div>
</div>



    <!-- Seção de Crianças Associadas -->
<div class="card p-3 shadow-sm mt-4">
    <h3>Crianças Associadas ao Responsável <strong> {{ $responsavel->nome_completo }} </strong></h3>

    @if($criancasAssociadas->isNotEmpty())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Grau de Parentesco</th>
                    <th>Início da Autorização</th>
                    <th>Fim da Autorização</th>
                    <th>Tipo de Responsável</th>
                    <th>Estado Autorização</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    @foreach($criancasAssociadas as $crianca)
        <tr>
            <td>
                <a href="{{ url('/hardware/' . $crianca->utente_id) }}">
                    {{ $crianca->name }} <i class="fas fa-external-link-alt"></i>
                </a>
            </td>
            <td>{{ $crianca->grau_parentesco ?? '-' }}</td>
            <td>{{ $crianca->data_inicio_autorizacao ? date('d/m/Y', strtotime($crianca->data_inicio_autorizacao)) : '-' }}</td>
            <td>{{ $crianca->data_fim_autorizacao ? date('d/m/Y', strtotime($crianca->data_fim_autorizacao)) : '-' }}</td>
            <td>{{ $crianca->tipo_responsavel ?? '-' }}</td>
            <td>
                @if($crianca->estado_autorizacao == 'Autorizado')
                    <span class="badge label-success rounded-circle"><i class="fas fa-check-circle"></i> Autorizado</span>
                @elseif($crianca->estado_autorizacao == 'Autorizacao Expirada')
                    <span class="badge label-danger rounded-circle"><i class="fas fa-times-circle"></i> Expirado</span>
                @elseif($crianca->estado_autorizacao == 'Nao Iniciado')
                    <span class="badge label-warning rounded-circle"><i class="fas fa-exclamation-circle"></i> Pendente</span>
                @else
                    <span class="badge badge-secondary rounded-circle"><i class="fas fa-question-circle"></i> Desconhecido</span>
                @endif
            </td>
            <td>{{ $crianca->observacoes ?? '-' }}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="editarCrianca({{ json_encode($crianca) }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="abrirModalRemoverCrianca('{{ $crianca->id }}', '{{ $crianca->name }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>

        {{-- Segunda linha opcional com os dias não autorizados --}}
        @if(!empty($crianca->dias_nao_autorizados))
            @php
                $dias = collect(explode(',', $crianca->dias_nao_autorizados))
                    ->map(fn($d) => trim($d))
                    ->filter()
                    ->implode(', ');
            @endphp
            @if($dias)
                <tr class="bg-light">
                    <td colspan="8" class="text-muted small">
                        ❌ <strong>Não autorizado a recolher às:</strong> {{ $dias }}
                    </td>
                </tr>
            @endif
        @endif

</tbody>



                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">Nenhuma criança associada.</p>
    @endif
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editarCriancaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Associação</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editarCriancaForm">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group">
                        <label for="edit_grau_parentesco">Grau de Parentesco</label>
                        <select class="form-control" id="edit_grau_parentesco" name="grau_parentesco">
                            <option value="Pai">Pai</option>
                            <option value="Mãe">Mãe</option>
                            <option value="Avô">Avô</option>
                            <option value="Avó">Avó</option>
                            <option value="Tio">Tio</option>
                            <option value="Tia">Tia</option>
                            <option value="Irmão">Irmão(ã)</option>
                            <option value="Irmã">Irmão(ã)</option>
                            <option value="Padrasto">Padrasto</option>
                            <option value="Madrasta">Madrasta</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_tipo_responsavel">Tipo de Responsável</label>
                        <select class="form-control" id="edit_tipo_responsavel" name="tipo_responsavel">
                            <option value="Encarregado de Educacao">Encarregado de Educação</option>
                            <option value="Autorizado">Autorizado</option>
                            <option value="Autorizado Excecional">Autorizado Excecional</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_data_inicio">Início da Autorização</label>
                        <input type="date" class="form-control" id="edit_data_inicio" name="data_inicio_autorizacao">
                    </div>

                    <div class="form-group">
                        <label for="edit_data_fim">Fim da Autorização</label>
                        <input type="date" class="form-control" id="edit_data_fim" name="data_fim_autorizacao">
                    </div>

                    <div class="form-group">
    <label class="mb-2 d-block">Dias da Semana em que Não Está Autorizado a Recolher</label>
    <div class="d-flex flex-row flex-nowrap overflow-auto" style="gap: 16px;">
        @php
            $diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
        @endphp
        @foreach($diasSemana as $dia)
            <div class="form-check" style="min-width: 120px;">
                <input class="form-check-input"
                       type="checkbox"
                       name="dias_nao_autorizados[]"
                       value="{{ $dia }}"
                       id="edit_dia_{{ $dia }}">
                <label class="form-check-label" for="edit_dia_{{ $dia }}">
                    {{ $dia }}
                </label>
            </div>
        @endforeach
    </div>
</div>





                    <div class="form-group">
                        <label for="edit_observacoes">Observações</label>
                        <textarea class="form-control" id="edit_observacoes" name="observacoes"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Confirmação de Remoção -->
<div class="modal fade" id="confirmarRemocaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmarRemocaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmarRemocaoLabel">Remover Associação</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover este utente do responsável?</p>
                <strong id="remocaoNomeCrianca"></strong>
                <input type="hidden" id="remocaoIdCrianca">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarRemocaoBtn">Remover</button>
            </div>
        </div>
    </div>
</div>

   <!-- Documentos -->
   <div class="form-group">
 
    
   @if($responsavel->documentos->isNotEmpty())
    <div class="mt-2">
        <h5>Documentos atuais:</h5>
        <div class="row">
            @foreach($responsavel->documentos as $documento)
                @php
                    $extensao = pathinfo($documento->path, PATHINFO_EXTENSION);
                    $urlDocumento = route('responsaveis.documento', ['filename' => basename($documento->path)]);
                @endphp
                <div class="col-md-3 text-center mb-3" id="documento_{{ $documento->id }}">
                    @if(in_array($extensao, ['jpg', 'jpeg', 'png', 'gif']))
                        <!-- Miniatura de Imagem -->
                        <img src="{{ $urlDocumento }}" class="documento-miniatura img-thumbnail"
                             onerror="this.onerror=null; this.src='{{ asset('images/default-document.png') }}';">
                    @elseif($extensao === 'pdf')
                        <!-- Miniatura do PDF -->
                        <canvas class="pdf-thumbnail border documento-miniatura" data-pdf="{{ $urlDocumento }}" willReadFrequently="true"></canvas>

                    @else
                        <!-- Caso seja outro tipo de documento -->
                        <img src="{{ asset('images/default-document.png') }}" class="documento-miniatura img-thumbnail">
                    @endif
                    <br>
                    <button type="button" class="btn btn-primary btn-sm mt-1" onclick="viewDocumentModal('{{ route('responsaveis.documento', ['filename' => basename($documento->path)]) }}')">
                        📄 Ver Documento
                    </button>

                    <button type="button" class="btn btn-danger btn-sm mt-1"
                            onclick="removeDocumento({{ $responsavel->id }}, {{ $documento->id }})">
                        Remover
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif



    <!-- Upload de novo documento -->
<div class="card p-3 shadow-sm mt-4">
    <h3>Adicionar Documento (Autorização ou outro)</h3>
    <form id="uploadDocumentoForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="responsavel_id" value="{{ $responsavel->id }}">

        <div class="form-group">
            <label for="novo_documento">Selecionar Ficheiro</label>
            <input type="file" class="form-control" id="novo_documento" name="documento" accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg">
        </div>

        <button type="submit" class="btn btn-success">Carregar Documento</button>
    </form>

    <div id="uploadStatus" class="mt-3"></div>
</div>


    <!-- Modal para visualizar documentos -->
    <div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="width: 900px; height: 90vh;">
                <div class="modal-header">
                    <h5 class="modal-title">Visualizar Documento</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <iframe id="documentViewer" src="" width="100%" height="650px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <a id="downloadBtn" href="#" class="btn btn-primary" download target="_blank">Download</a>
                    <button type="button" class="btn btn-success" onclick="printPDF()">Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notas -->
<div class="card p-3 shadow-sm mt-4">
    <h3>Notas do Responsável</h3>

    <!-- Formulário para adicionar nota -->
    <form id="adicionarNotaForm">
        @csrf
        <input type="hidden" name="responsavel_id" value="{{ $responsavel->id }}">

        <div class="form-group">
            <textarea class="form-control" id="nova_nota" name="nota" rows="3" placeholder="Escreva uma nota..." required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Adicionar Nota</button>
    </form>

    <div id="statusNota" class="mt-3"></div>

    <!-- Lista de Notas -->
    <div id="listaNotas" class="mt-3"></div>
</div>



<!-- 📜 Histórico de Alterações -->
<div class="card p-3 shadow-sm mt-4">
    <h3>Histórico de Alterações</h3>
    @if($historicoCompleto->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Alterado por</th>
                        <th>Detalhes da Alteração</th>
                    </tr>
                </thead>
                <tbody>
    @foreach($historicoCompleto as $item)
        <tr>
            <!-- 📅 Data -->
            <td>{{ \Carbon\Carbon::parse($item->alterado_em)->format('d/m/Y H:i') }}</td>

            <!-- 👤 Alterado por -->
            <td>
                @if($item->first_name || $item->last_name)
                    {{ $item->first_name }} {{ $item->last_name }}
                @elseif($item->ee_nome)
                    Encarregado de Educação: {{ $item->ee_nome }}
                @else
                    Alteração não atribuída
                @endif
            </td>

            <!-- 📌 Detalhes -->
            <td>
                @if($item->nome_responsavel)
                    <p><strong>👤 Responsável:</strong> {{ $item->nome_responsavel }}</p>
                @endif

                @if($item->utente_id)
                    <p><strong>👶 Utente:</strong> 
                        <a href="{{ url('/hardware/' . $item->utente_id) }}" target="_blank">
                            {{ $item->nome_utente ?? 'ID ' . $item->utente_id }} <i class="fas fa-external-link-alt"></i>
                        </a>
                    </p>
                @endif

                @if($item->motivo)
                    @php
                        $linhas = explode("\n", $item->motivo);
                    @endphp
                    <p><strong>📌 Alterações:</strong></p>
                    <ul class="list-unstyled">
                        @foreach($linhas as $linha)
                            <li>{{ $linha }}</li>
                        @endforeach
                    </ul>
                @endif

                @if($item->data_inicio_autorizacao || $item->data_fim_autorizacao)
                    <p><strong>📅 Período de Autorização:</strong></p>
                    <ul class="list-unstyled">
                        @if($item->data_inicio_autorizacao)
                            <li><strong>Início:</strong> {{ \Carbon\Carbon::parse($item->data_inicio_autorizacao)->format('d/m/Y') }}</li>
                        @endif
                        @if($item->data_fim_autorizacao)
                            <li><strong>Fim:</strong> {{ \Carbon\Carbon::parse($item->data_fim_autorizacao)->format('d/m/Y') }}</li>
                        @endif
                    </ul>
                @endif

                @if($item->grau_parentesco || $item->tipo_responsavel)
                    <p><strong>🧬 Relação:</strong></p>
                    <ul class="list-unstyled">
                        @if($item->grau_parentesco)
                            <li><strong>Parentesco:</strong> {{ $item->grau_parentesco }}</li>
                        @endif
                        @if($item->tipo_responsavel)
                            <li><strong>Tipo:</strong> {{ $item->tipo_responsavel }}</li>
                        @endif
                    </ul>
                @endif

                @if($item->estado_autorizacao)
                    <p><strong>🔐 Estado:</strong> 
                        @php
                            $estado = $item->estado_autorizacao;
                            $cor = match ($estado) {
                                'Autorizado' => 'green',
                                'Nao Iniciado' => 'orange',
                                'Autorizacao Expirada', 'Nao Autorizado' => 'red',
                                default => 'gray'
                            };
                        @endphp
                        <span style="color: {{ $cor }}">{{ $estado }}</span>
                    </p>
                @endif
            </td>
        </tr>
    @endforeach
</tbody>



            </table>
        </div>
    @else
        <p class="text-muted text-center">Nenhuma alteração registada.</p>
    @endif
</div>


</div>


<!-- Biblioteca PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>

<script>
$(document).ready(function () {
    // 📑 Miniaturas de PDFs
    document.querySelectorAll(".pdf-thumbnail").forEach(canvas => {
        const pdfUrl = canvas.getAttribute("data-pdf");
        if (pdfUrl) {
            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                pdf.getPage(1).then(page => {
                    const scale = 0.5;
                    const viewport = page.getViewport({ scale });
                    const context = canvas.getContext("2d", { willReadFrequently: true });
                    if (!context) return;
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    page.render({ canvasContext: context, viewport });
                });
            }).catch(error => console.error("Erro ao carregar PDF:", error));
        }
    });

    // 🖼 Ver documento
    window.viewDocumentModal = function (url) {
        document.getElementById("documentViewer").src = url;
        document.getElementById("downloadBtn").href = url;
        $("#documentModal").modal("show");
    };

    // 🖨 Imprimir PDF
    window.printPDF = function () {
        const iframe = document.getElementById("documentViewer");
        if (iframe && iframe.contentWindow) iframe.contentWindow.print();
    };

    // 📎 Upload de documento
    $("#uploadDocumentoForm").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let botao = $(this).find("button[type=submit]");
        if (botao.prop("disabled")) return;
        botao.prop("disabled", true).text("A carregar...");

        $.ajax({
            url: "{{ route('responsaveis.uploadDocumento', ['responsavelId' => $responsavel->id]) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success(response) {
                alert(response.success ? "✅ Documento carregado com sucesso!" : "⚠️ " + (response.message || "Erro desconhecido"));
                if (response.success) location.reload();
            },
            error() {
                alert("⚠️ Nenhum documento foi carregado. Verifique o ficheiro.");
            },
            complete() {
                botao.prop("disabled", false).text("Carregar Documento");
            }
        });
    });

    // 🔍 Pesquisar utentes
    let searchTimeout;
    $("#searchUtente").on("input", function () {
        let search = $(this).val().trim();
        if (search.length < 2) return $("#resultadosUtentes").empty();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            $.ajax({
                url: "{{ route('responsaveis.buscarUtentesNaoAssociados', $responsavel->id) }}",
                type: "GET",
                data: { search },
                success(response) {
                    let html = response.data?.length
                        ? response.data.map(utente => `
                            <div class="list-group-item list-group-item-action utente-item"
                                data-id="${utente.id}" data-name="${utente.name}" data-nif="${utente.serial}">
                                <strong>${utente.name}</strong><br>
                                <small class="text-muted">NIF: ${utente.serial !== "Sem NIF" ? utente.serial : "N/A"}</small>
                            </div>`).join('')
                        : '<div class="list-group-item text-muted">Nenhum resultado encontrado</div>';
                    $("#resultadosUtentes").html(html);
                },
                error() {
                    $("#resultadosUtentes").html('<div class="list-group-item text-danger">Erro na pesquisa</div>');
                }
            });
        }, 300);
    });

    $(document).on("click", ".utente-item", function () {
        let nome = $(this).data("name");
        let nif = $(this).data("nif");
        $("#searchUtente").val(`${nome} (NIF: ${nif !== "Sem NIF" ? nif : "N/A"})`);
        $("#utente_id").val($(this).data("id")).change();
        $("#resultadosUtentes").empty();
    });

    // ➕ Adicionar utente
    $("#adicionarUtenteForm").on("submit", function (e) {
        e.preventDefault();
        let botao = $(this).find("button[type=submit]");
        if (botao.prop("disabled")) return;
        botao.prop("disabled", true).text("A adicionar...");

        $.ajax({
            url: "{{ route('responsaveis.adicionarUtente') }}",
            type: "POST",
            data: $(this).serialize(),
            success(response) {
                alert(response.success ? response.message : "⚠️ " + response.message);
                if (response.success) location.reload();
            },
            error(jqXHR) {
                let mensagemErro = "❌ Erro ao adicionar o utente.";
                if (jqXHR.status === 422 && jqXHR.responseJSON.errors) {
                    mensagemErro = "⚠️ Erro de validação:\n\n";
                    $.each(jqXHR.responseJSON.errors, (campo, mensagens) => {
                        mensagemErro += `- ${mensagens.join(', ')}\n`;
                    });
                } else if (jqXHR.responseJSON?.message) {
                    mensagemErro = " " + jqXHR.responseJSON.message;
                }
                alert(mensagemErro);
            },
            complete() {
                botao.prop("disabled", false).text("Adicionar Utente");
            }
        });
    });

    // 🗑 Remover associação
    window.abrirModalRemoverCrianca = function (id, nome) {
        $("#remocaoIdCrianca").val(id);
        $("#remocaoNomeCrianca").text(nome);
        $("#confirmarRemocaoModal").modal("show");
    };

    $("#confirmarRemocaoBtn").on("click", function () {
        let id = $("#remocaoIdCrianca").val();
        let botao = $(this);
        if (botao.prop("disabled")) return;
        botao.prop("disabled", true).text("A remover...");

        $.ajax({
            url: "{{ route('responsaveis.removerAssociacao') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", id },
            success(response) {
                alert(response.success ? "✅ " + response.message : "⚠️ Erro ao remover: " + response.message);
                if (response.success) {
                    $("#confirmarRemocaoModal").modal("hide");
                    location.reload();
                }
            },
            error() {
                alert("❌ Erro ao remover a associação.");
            },
            complete() {
                botao.prop("disabled", false).text("Confirmar Remoção");
            }
        });
    });

    // ✏️ Editar associação
    window.editarCrianca = function (crianca) {
        $("#edit_id").val(crianca.id || "");
        $("#edit_grau_parentesco").val(crianca.grau_parentesco || "");
        $("#edit_tipo_responsavel").val(crianca.tipo_responsavel || "");
        $("#edit_data_inicio").val(crianca.data_inicio_autorizacao || "");
        $("#edit_data_fim").val(crianca.data_fim_autorizacao || "");
        $("#edit_observacoes").val(crianca.observacoes || "");
        $(".form-check-input[name='dias_nao_autorizados[]']").prop("checked", false);
        if (crianca.dias_nao_autorizados) {
            crianca.dias_nao_autorizados.split(',').forEach(dia => {
                $(`#edit_dia_${dia.trim()}`).prop("checked", true);
            });
        }
        $("#editarCriancaModal").modal("show");
    };

    $("#editarCriancaForm").on("submit", function (e) {
        e.preventDefault();
        let botao = $(this).find("button[type=submit]");
        let id = $("#edit_id").val();
        if (!id) return alert("❌ ID da associação em falta.");

        botao.prop("disabled", true).text("A salvar...");
        let formData = new FormData(this);

        formData.delete('dias_nao_autorizados[]');
        $("input[name='dias_nao_autorizados[]']:checked").each(function () {
            formData.append('dias_nao_autorizados[]', $(this).val());
        });

        formData.append('id', id);

        $.ajax({
            url: "{{ route('responsaveis.atualizarAssociacao') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success(response) {
                alert(response.sucesso ? "✅ " + response.mensagem : response.mensagem);
                if (response.sucesso) location.reload();
            },
            error(xhr) {
                let resposta = xhr.responseJSON;
                let mensagemErro = "❌ Ocorreu um erro ao atualizar.";
                if (xhr.status === 422 && resposta.erros) {
                    mensagemErro = "⚠️ Erro de validação:\n\n";
                    $.each(resposta.erros, (campo, mensagens) => {
                        mensagemErro += `- ${mensagens.join(', ')}\n`;
                    });
                } else if (resposta?.mensagem) {
                    mensagemErro = " " + resposta.mensagem;
                }
                alert(mensagemErro);
            },
            complete() {
                botao.prop("disabled", false).text("Salvar Alterações");
            }
        });
    });

    // 📝 Notas
    const listaNotas = $("#listaNotas");
    function carregarNotas() {
        $.ajax({
            url: "{{ route('responsaveis.carregarNotas', ['responsavelId' => $responsavel->id]) }}",
            type: "GET",
            success(response) {
                listaNotas.empty();
                if (!response.length) return listaNotas.html('<p class="text-muted">Nenhuma nota adicionada.</p>');
                response.forEach(nota => {
                    let dataNota = new Date(nota.created_at).toLocaleString("pt-PT");
                    listaNotas.append(`
                        <div class="card p-2 mb-2">
                            <p class="mb-1">${nota.nota}</p>
                            <small class="text-muted">Por ${nota.usuario?.first_name ?? 'Desconhecido'} ${nota.usuario?.last_name ?? ''} em ${dataNota}</small>
                        </div>`);
                });
            },
            error() {
                listaNotas.html('<p class="text-danger">Erro ao carregar notas.</p>');
            }
        });
    }

    carregarNotas();

    $("#adicionarNotaForm").on("submit", function (e) {
        e.preventDefault();
        let botao = $(this).find("button[type=submit]");
        let statusNota = $("#statusNota");
        botao.prop("disabled", true).text("A adicionar...");

        $.ajax({
            url: "{{ route('responsaveis.adicionarNota', ['responsavelId' => $responsavel->id]) }}",
            type: "POST",
            data: $(this).serialize(),
            success(response) {
                statusNota.html(response.success
                    ? '<span class="text-success">Nota adicionada com sucesso!</span>'
                    : '<span class="text-danger">Erro ao adicionar nota.</span>');
                if (response.success) {
                    $("#nova_nota").val("");
                    carregarNotas();
                }
            },
            error() {
                statusNota.html('<span class="text-danger">Erro ao adicionar nota.</span>');
            },
            complete() {
                botao.prop("disabled", false).text("Adicionar Nota");
            }
        });
    });

    // 📎 Remover documento
    window.removeDocumento = function (responsavelId, documentoId) {
        if (!confirm('Tem certeza que deseja remover este documento?')) return;
        fetch(`/responsaveis/${responsavelId}/documentos/${documentoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success ? '✅ Documento removido com sucesso!' : `⚠️ Erro: ${data.message}`);
            if (data.success) document.getElementById(`documento_${documentoId}`).remove();
        })
        .catch(error => {
            console.error('❌ Erro na requisição:', error);
            alert('Erro ao remover documento.');
        });
    };
});
</script>





<style>
     .documento-miniatura {
        width: 170px;
        height: 210px;
        object-fit: cover;
        }

    .table-responsive {
            overflow-x: auto;
        }

</style>

<div style="padding-bottom: 80px;"></div>

@endsection