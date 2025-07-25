@extends('layouts.default')

@section('title', 'Adicionar Responsável')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">

<div class="container mt-5">
    <h1>Adicionar Responsável</h1>

    <!-- Mensagem de orientação -->
    <div class="alert alert-info">
        <strong>Nota:</strong> Antes de preencher, verifique se o responsável já existe através do Número de Identificação.
    </div>

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
    <div id="alertaResponsavel" class="alert alert-warning d-none"></div>

    <!-- Formulário de criação de responsável -->
    <form id="formResponsavel" action="{{ route('responsaveis.storeNovo') }}" method="POST" enctype="multipart/form-data">

        @csrf

        <!-- Número de Identificação -->
        <div class="form-group position-relative">
            <label for="nr_identificacao">Número de Identificação</label>
            <div class="input-group">
                <select name="nr_identificacao" id="nr_identificacao" class="form-control" required style="width: 100%;">
                    <option value="" disabled>Digite para buscar ou inserir novo...</option>
                </select>
                <div class="input-group-append">
                    <button type="button" id="limparFormulario" class="btn btn-warning">Limpar</button>
                </div>
            </div>
        </div>

        <!-- Nome Completo -->
        <div class="form-group">
            <label for="nome_completo">Nome Completo</label>
            <input type="text" name="nome_completo" id="nome_completo" class="form-control" required>
        </div>

        <!-- Foto -->
        <div class="form-group">
            <label for="foto">Foto</label>
            <small class="d-block text-muted">A foto deve ter boa qualidade para facilitar a identificação no ato do scan do QR Code.</small>
            <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
            <br>
            <img id="fotoPreview" 
                src="{{ asset('img/anonimoadulto.png') }}"
                alt="Pré-visualização da Foto" 
                class="img-thumbnail" 
                style="max-width: 200px;">
        </div>

        <!-- Contacto -->
        <div class="form-group">
            <label for="contacto">Contacto</label>
            <input type="text" name="contacto" id="contacto" class="form-control">
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>

        <!-- Documento -->
        <div class="form-group">
            <label for="documento">Documento (Autorização ou outro)</label>
            <input type="file" name="documento" id="documento" class="form-control">
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
    $("#nr_identificacao").select2({
        placeholder: "Digite para buscar...",
        minimumInputLength: 5, // Aguarda 5 caracteres antes de pesquisar
        tags: true, // Permite inserir um novo NIF se não for encontrado
        createTag: function (params) {
            return { id: params.term, text: `Novo responsável: ${params.term}`, newOption: true };
        },
        ajax: {
            url: "{{ route('responsaveis.buscar') }}",
            dataType: "json",
            delay: 300, // Pequeno atraso para evitar múltiplas chamadas
            data: function (params) {
                return { nr_identificacao: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.success ? data.data.map(function (responsavel) {
                        return {
                            id: responsavel.nr_identificacao,
                            text: `${responsavel.nome_completo} (${responsavel.nr_identificacao})`,
                            responsavel: responsavel
                        };
                    }) : []
                };
            }
        }
    });

    // 📌 Verifica se o responsável já existe e alerta
    $("#nr_identificacao").on("select2:select", function (e) {
        let responsavel = e.params.data.responsavel;

        if (responsavel) {
            console.log("✅ Responsável encontrado:", responsavel);

            $("#alertaResponsavel").html(`
                <strong>Atenção:</strong> O NIF <strong>${responsavel.nr_identificacao}</strong> já está registado para <strong>${responsavel.nome_completo}</strong>.
            `).removeClass("d-none").show();

            $("#btnAdicionar").prop("disabled", true); // Desativa o botão de adicionar

        } else {
            console.warn("🆕 Novo NIF inserido manualmente.");
            $("#alertaResponsavel").addClass("d-none").hide();
            $("#btnAdicionar").prop("disabled", false); // Permite adicionar um novo
        }
    });

    // 🧹 Botão "Limpar"
    $("#limparFormulario").on("click", function () {
        $("#nr_identificacao").val(null).trigger("change");
        $("#alertaResponsavel").addClass("d-none").hide();
        $("#btnAdicionar").prop("disabled", false);
    });

    // 🖼 Atualiza a pré-visualização da foto ao selecionar um novo arquivo
    $("#foto").change(function (event) {
        let input = event.target;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#fotoPreview").attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
});
</script>

<div style="padding-bottom: 80px;"></div>
@endsection
