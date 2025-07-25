@extends('layouts.default')

@section('title', 'Editar Responsável')

@section('content')
<div class="container mt-5">
    <h1>Editar Responsável: {{ $responsavel->nome_completo }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('responsaveis.update', [ 'responsavelId' => $responsavel->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

<!-- Foto -->
<div class="form-group">
    <label for="foto">Foto (Máx: 2MB)</label>
    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">

    <br>
    <img id="fotoPreview" 
     src="{{ isset($responsavel->foto) ? route('responsaveis.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}" 
     alt="Foto do Responsável" 
     class="img-thumbnail mt-2" 
     style="max-width: 150px;">

    <div class="mt-2">
        <small class="text-muted d-block text-center">Apenas imagens até 2MB são permitidas.</small>
        <div id="fotoError" class="text-danger text-center mt-1" style="display: none;"></div>
    </div>

    @if($responsavel->foto)
        <br>
        <button type="button" class="btn btn-danger mt-2" onclick="removeFoto({{ $responsavel->id }})">
            Remover Foto
        </button>
    @endif

    <br>
    <img id="novaFotoPreview" src="#" alt="Pré-visualização da Nova Foto" class="img-thumbnail mt-2 d-block mx-auto" style="display:none; max-width: 150px;">
</div>

        <!-- Nome Completo -->
        <div class="form-group">
    <label for="nome_completo">Nome Completo <span class="text-danger">*</span></label>
    <input type="text" name="nome_completo" id="nome_completo" class="form-control" required value="{{ old('nome_completo', $responsavel->nome_completo ?? '') }}">
    @error('nome_completo')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>


        <!-- Número de Identificação -->
        <div class="form-group">
    <label for="nr_identificacao">Número de Identificação <span class="text-danger">*</span></label>
    <input type="text" name="nr_identificacao" id="nr_identificacao" class="form-control" required value="{{ old('nr_identificacao', $responsavel->nr_identificacao ?? '') }}">
    @error('nr_identificacao')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

        <!-- Contacto e Email -->
        <div class="form-group">
            <label for="contacto">Contacto</label>
            <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $responsavel->contacto) }}">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $responsavel->email) }}">
        </div>



      





<div style="padding-bottom: 80px;"></div>

<div class="d-flex justify-content-between">
    <!-- ✅ Botão "Salvar Alterações" -->
    <button type="submit" class="btn btn-success">Salvar Alterações</button>

    <!-- ✅ Botão "Cancelar" redireciona para a página do responsável -->
    <a href="{{ route('responsaveis.show', ['responsavelId' => $responsavel->id]) }}" class="btn btn-primary" role="button">Cancelar</a>
</div>






    </form>
</div>





<!-- Biblioteca para renderizar PDFs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>

<script>
   
// Atualiza a pré-visualização da foto ao selecionar um ficheiro
document.getElementById("foto").addEventListener("change", function (event) {
    let input = event.target;
    let file = input.files[0];
    let maxSize = 2 * 1024 * 1024; // 2MB
    let errorDiv = document.getElementById("fotoError");
    let preview = document.getElementById("fotoPreview");

    if (file) {
        if (file.size > maxSize) {
            errorDiv.textContent = `A imagem selecionada tem ${(file.size / (1024 * 1024)).toFixed(2)}MB e ultrapassa o limite de 2MB.`;
            errorDiv.style.display = "block";
            input.value = "";
            return;
        } else {
            errorDiv.style.display = "none";
        }

        let reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});


// Remover Foto
function removeFoto(responsavelId) {
    if (confirm('Tem certeza que deseja remover esta foto?')) {
        fetch(`/responsaveis/${responsavelId}/removeFoto`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Foto removida com sucesso!');
                  document.getElementById('fotoPreview').src = "{{ asset('img/anonimoadulto.png') }}";
              } else {
                  alert('Erro ao remover a foto.');
              }
          }).catch(error => console.error('Erro na requisição:', error));
    }
}


// Validação de Campos Obrigatórios
document.addEventListener("DOMContentLoaded", function () {
    let formResponsavel = document.getElementById("formResponsavel");

    if (formResponsavel) { // 🔥 Só adiciona o evento se o formulário existir
        formResponsavel.addEventListener("submit", function(event) {
            let requiredFields = ["nr_identificacao", "nome_completo"];
            let valid = true;

            requiredFields.forEach(field => {
                let input = document.getElementById(field);
                if (input && !input.value.trim()) { // 🔥 Evita erro se o campo não existir
                    input.classList.add("is-invalid");
                    valid = false;
                } else if (input) {
                    input.classList.remove("is-invalid");
                }
            });

            if (!valid) {
                event.preventDefault();
                alert("Preencha todos os campos obrigatórios!");
            }
        });
    }
});

</script>
<style>
    .documento-miniatura {
    width: 170px; /* Mantendo o tamanho original */
    height: 210px;
    object-fit: cover;
}

</style>
<div style="padding-bottom: 80px;"></div>
@endsection
