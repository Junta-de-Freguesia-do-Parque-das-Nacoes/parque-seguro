@extends('layouts.default')

@section('title', 'Adicionar Responsável')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">

<div class="container mt-5">
    <h1>Adicionar Responsável à criança {{ $utenteNome }}</h1>

    <!-- Mensagem de sucesso -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }} 
        <br>
        O nome do utente é: {{ session('utenteNome') }}
    </div>
    @endif

    <!-- Mensagem de orientação -->
    <div class="alert alert-info">
        <strong>Nota:</strong> Antes de preencher, verifique se o responsável já existe através do Número de Identificação.
    </div>


    @if (session('existeOutro'))
    <div class="alert alert-warning">
        ⚠️ Este utente já tem um Encarregado de Educação associado: {{ session('existeOutro')->nome_completo }} (NIF: {{ session('existeOutro')->nr_identificacao }}). Apenas um é permitido.
    </div>
    <script>
        // Desabilitar o campo de seleção de EE
        document.getElementById('tipo_responsavel').disabled = true;
    </script>
@endif

    <!-- Mensagens de erro -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Alerta de responsável existente -->
    <div id="alertaResponsavel" class="alert d-none"></div>

    <!-- Formulário de criação e associação de responsável -->
    <form method="POST" action="{{ route('responsaveis.associar', ['utenteId' => $utenteId]) }}" id="formResponsavel" enctype="multipart/form-data">
        @csrf
        <!-- Número de Identificação -->
        <div class="form-group position-relative">
            <label for="nr_identificacao">Número de Identificação</label>
            <div class="input-group">
                <select name="nr_identificacao" id="nr_identificacao" class="form-control" required style="width: 100%;">
                    <option value="" disabled>Digite para buscar ou inserir novo...</option>
                </select>
                <p> </p>
                <div class="input-group-append">
                    <button type="button" id="limparFormulario" class="btn btn-warning">Limpar</button>
                </div>
            </div>
        </div>

        <!-- Nome Completo -->
        <div class="form-group">
            <label for="nome_completo">Nome Completo</label>
            <input type="text" name="nome_completo" id="nome_completo" class="form-control" required value="{{ old('nome_completo') }}">

        </div>

        <!-- Foto -->
        <div class="form-group">
            <label for="foto">Foto</label>
            <small class="d-block text-muted">A foto deve ter boa qualidade para facilitar a identificação no ato do scan do QR Code.</small>
            <input type="file" name="foto" id="foto" class="form-control" accept="image/*" onchange="previewFoto(event)">
            <br>
            <img id="fotoPreview" 
     src="{{ isset($responsavel->foto) ? route('responsaveis.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}" 
     alt="Pré-visualização da Foto" 
     class="img-thumbnail" 
     style="max-width: 200px;">



        </div>

        <script>
            // Função de pré-visualização da foto
            function previewFoto(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var preview = document.getElementById('fotoPreview');
                    preview.src = reader.result;  // Exibe a imagem selecionada
                };
                reader.readAsDataURL(event.target.files[0]);  // Lê o arquivo como URL de dados
            }

            
        </script>

        <!-- Contacto -->
        <div class="form-group">
            <label for="contacto">Contacto</label>
            <input type="text" name="contacto" id="contacto" class="form-control" 
       pattern="^(?:\+351|00351)?(?:9[1236]\d{7})$" 
       title="Por favor, insira um número de telefone válido (ex: +351912345678)" 
       placeholder="Por favor, insira um número de telefone válido (ex: +351912345678)" 
       value="{{ old('contacto') }}" 
       minlength="9">


        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">

        </div>

        <!-- Grau de Parentesco -->
        <div class="form-group">
    <label for="grau_parentesco">Grau de Parentesco</label>
    <select class="form-control" id="grau_parentesco" name="grau_parentesco" required>
        <option value="" disabled {{ old('grau_parentesco') ? '' : 'selected' }}>Selecione...</option>
        <option value="Pai" {{ old('grau_parentesco') == 'Pai' ? 'selected' : '' }}>Pai</option>
        <option value="Mãe" {{ old('grau_parentesco') == 'Mãe' ? 'selected' : '' }}>Mãe</option>
        <option value="Avô" {{ old('grau_parentesco') == 'Avô' ? 'selected' : '' }}>Avô</option>
        <option value="Avó" {{ old('grau_parentesco') == 'Avó' ? 'selected' : '' }}>Avó</option>
        <option value="Tio" {{ old('grau_parentesco') == 'Tio' ? 'selected' : '' }}>Tio</option>
        <option value="Tia" {{ old('grau_parentesco') == 'Tia' ? 'selected' : '' }}>Tia</option>
        <option value="Irmão" {{ old('grau_parentesco') == 'Irmão' ? 'selected' : '' }}>Irmão(ã)</option>
        <option value="Padrasto" {{ old('grau_parentesco') == 'Padrasto' ? 'selected' : '' }}>Padrasto</option>
        <option value="Madrasta" {{ old('grau_parentesco') == 'Madrasta' ? 'selected' : '' }}>Madrasta</option>
        <option value="Outro" {{ old('grau_parentesco') == 'Outro' ? 'selected' : '' }}>Outro</option>
    </select>
</div>




        


        {{-- Verifique se a variável de sessão foi definida --}}
@php
    $isEncarregadoDeEducacao = session('isEncarregadoDeEducacao', false);
@endphp



{{-- Verifique se a variável de erro foi passada corretamente --}}
@if(session('isEncarregadoDeEducacao'))
    <div class="alert alert-warning">
        Esta criança já tem um Encarregado de Educação associado: <strong>{{ session('error') }}</strong>
    </div>
@endif

<!-- Tipo de Responsável -->
<div class="form-group">
    <label for="tipo_responsavel">Tipo de Responsável</label>
    <select class="form-control" id="tipo_responsavel" name="tipo_responsavel" required>
        <option value="" disabled selected>Selecione uma opção</option>
        <option value="Encarregado de Educacao" @if(old('tipo_responsavel') == 'Encarregado de Educacao') selected @endif @if($isEncarregadoDeEducacao) disabled @endif>Encarregado de Educação</option>
        <option value="Autorizado" @if(old('tipo_responsavel') == 'Autorizado') selected @endif>Autorizado</option>
        <option value="Autorizado Excecional" @if(old('tipo_responsavel') == 'Autorizado Excecional') selected @endif>Autorizado Excecional</option>
    </select>
</div>







        <!-- Datas de Autorização -->
        <div class="form-group">
            <label for="data_inicio">Início da Autorização</label>
            <input type="date" class="form-control" id="data_inicio" name="data_inicio_autorizacao" value="{{ old('data_inicio_autorizacao') }}">

        </div>

        <div class="form-group">
            <label for="data_fim">Fim da Autorização</label>
            <input type="date" class="form-control" id="data_fim" name="data_fim_autorizacao" value="{{ old('data_fim_autorizacao') }}">

        </div>

        <div class="alert alert-info mt-2">
            <i class="fas fa-info-circle"></i> Se não definir as datas, o responsável terá autorização permanente.
            <br><strong>Sugestão: </strong>Definir até o fim do ano letivo para renovação da autorização.
        </div>

        <!-- Observações -->
        <div class="form-group">
            <label for="observacoes">Observações</label>
            <textarea class="form-control" id="observacoes" name="observacoes">{{ old('observacoes') }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
    <button type="submit" id="btnAdicionar" class="btn btn-success">Adicionar Responsável</button>
    <a href="{{ url()->previous() }}" class="btn btn-primary">Voltar</a>
</div>

    </form>
</div>




<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // Função para limpar o valor da pesquisa
    function cleanInput(input) {
        // Remove espaços extras no início e no final
        let cleanedInput = input.trim();

        // Normaliza caracteres especiais (remove quebras de linha, tabs, etc.)
        cleanedInput = cleanedInput.replace(/[\n\r\t]/g, '');

        return cleanedInput;
    }

    // Inicializa o Select2
    $("#nr_identificacao").select2({
    placeholder: "Digite para buscar...",
    minimumInputLength: 5, // Aguarda 5 caracteres antes de pesquisar
    tags: true, // Permite inserir um novo NIF se não for encontrado
    createTag: function (params) {
        
    },
    ajax: {
        url: "{{ route('responsaveis.buscar') }}", // Ajuste conforme necessário
        dataType: "json",
        delay: 300, // Pequeno atraso para evitar múltiplas chamadas
        data: function (params) {
            let cleanedValue = cleanInput(params.term);
            return { nr_identificacao: cleanedValue };
        },
        processResults: function (data, params) {
            // Cria a lista de resultados
            let results = data.success ? data.data.map(function (responsavel) {
                return {
                    id: responsavel.nr_identificacao,
                    text: `${responsavel.nome_completo} (${responsavel.nr_identificacao})`,
                    responsavel: responsavel
                };
            }) : [];

            // Se o NIF não existir, adiciona a opção de criar um novo responsável
            if (!data.success || data.data.length === 0) {
                results.push({
                    id: params.term,  // O valor digitado será o ID
                    text: `Adicionar novo responsável: ${params.term}`,  // Texto para novo responsável
                    newOption: true  // Marca como uma nova opção
                });
            }

            return {
                results: results
            };
        }
    }
});

// Quando uma opção é selecionada (novo responsável ou existente)
$("#nr_identificacao").on("select2:select", function (e) {
    let responsavel = e.params.data.responsavel;

    if (responsavel) {
        // Preenche os campos automaticamente se o responsável for encontrado
        $("#nome_completo").val(responsavel.nome_completo).prop('readonly', true);
        $("#contacto").val(responsavel.contacto).prop('readonly', true);
        $("#email").val(responsavel.email).prop('readonly', true);
        $("#grau_parentesco").val(responsavel.grau_parentesco).prop('readonly', true);
        $("#tipo_responsavel").val(responsavel.tipo_responsavel).prop('readonly', true);
        $("#fotoPreview").attr("src", responsavel.foto ? `/storage/${responsavel.foto}` : '/img/anonimoadulto.png');
        

        
        // Torna os campos readonly (não editáveis)
        $("#nr_identificacao").prop('readonly', true); // Evita edição do campo de NIF

        // Exibe a mensagem de alerta com o responsável encontrado
        $("#alertaResponsavel").removeClass('d-none').addClass('alert-success').text(`Responsável encontrado: ${responsavel.nome_completo} (${responsavel.nr_identificacao})`);
    } else {
        // Aqui, você pode mostrar os campos de criação manual se o NIF não for encontrado
        $("#nome_completo").val('').prop('readonly', false);
        $("#contacto").val('').prop('readonly', false);
        $("#email").val('').prop('readonly', false);
        $("#grau_parentesco").val('').prop('readonly', false);
        $("#tipo_responsavel").val('').prop('readonly', false);
        $("#fotoPreview").attr("src", '/img/anonimoadulto.png');
        
        // Exibe alerta que o usuário deve preencher os dados manualmente
        $("#alertaResponsavel").removeClass('d-none').addClass('alert-warning').text(`Não encontramos um responsável com o NIF ${e.params.data.id}. Por favor, preencha os dados manualmente.`);
    }
});

    // Verifica se o responsável já existe e preenche os campos automaticamente
    $("#nr_identificacao").on("select2:select", function (e) {
    let responsavel = e.params.data.responsavel;

    if (responsavel) {
        // Preenche os campos automaticamente
        $("#nome_completo").val(responsavel.nome_completo).prop('readonly', true);
        $("#contacto").val(responsavel.contacto).prop('readonly', true);
        $("#email").val(responsavel.email).prop('readonly', true);
        $("#grau_parentesco").val(responsavel.grau_parentesco).prop('readonly', true);
        $("#tipo_responsavel").val(responsavel.tipo_responsavel).prop('readonly', true);
        $("#fotoPreview").attr("src", responsavel.foto ? `/storage/${responsavel.foto}` : '/img/anonimoadulto.png');
        
        // Torna os campos readonly (não editáveis)
        $("#nr_identificacao").prop('readonly', true); // Evita edição do campo de NIF também

        // Exibe a mensagem de alerta com o responsável encontrado
        $("#alertaResponsavel").removeClass('d-none').addClass('alert-success').text(`Responsável encontrado: ${responsavel.nome_completo} (${responsavel.nr_identificacao})`);
    }
});


    // Limpar formulário
    $("#limparFormulario").click(function () {
        // Recarregar a página para limpar todos os campos e restaurar o estado
        location.reload();
    });

        


});
</script>
<div style="padding-bottom: 80px;"></div>
@endsection
