@extends('layouts.default')

@section('title', 'Gestão de Responsáveis')

@section('content')
<div class="container mt-4">
    <h1 class="d-flex align-items-center justify-content-between">
        Responsáveis
        <a href="{{ route('responsaveis.create') }}" class="btn btn-success">
            <i class="fas fa-user-plus"></i> Criar Responsável
        </a>
    </h1>

    <!-- 🔎 Barra de Pesquisa e Botões -->
    <div class="mb-3">
        <form action="{{ route('responsaveis.listar') }}" method="GET">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <!-- Campo de pesquisa -->
                    <input type="text" name="search" id="searchBox" class="form-control" placeholder="Pesquisar por nome, número de identificação ou email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-6 d-flex justify-content-between">
                    <!-- Botões -->
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                    <a href="{{ route('responsaveis.listar') }}" class="btn btn-primary">Limpar Pesquisa</a>
                    <button type="submit" name="find_ee_without_email" value="1" class="btn btn-warning">Encontrar EE sem Email</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabela Responsáveis -->
    <div class="table-responsive">
        <table class="table table-hover table-striped" id="responsaveisTable">
            <thead class="thead-dark">
                <tr>
                    <th>
                        <i class="fas fa-image"></i> Foto
                    </th>
                    <th>
                        <i class="fas fa-user"></i> Nome
                    </th>
                    <th>
                        <i class="fas fa-id-card"></i> Identificação
                    </th>
                    <th>
                        <i class="fas fa-phone"></i> Contacto
                    </th>
                    <th>
                        <i class="fas fa-envelope"></i> Email
                    </th>
                    <th>
                        <i class="fas fa-cogs"></i> Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($responsaveis as $responsavel)
                    <tr id="responsavelRow{{ $responsavel->id }}">
                        <td class="text-center">
                            <img src="{{ $responsavel->foto ? route('responsaveis.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}" 
                                 alt="Foto de {{ $responsavel->nome_completo }}" 
                                 class="rounded-circle" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td class="d-flex align-items-center">
                            <span class="copiar" onclick="copiarDados('{{ $responsavel->nome_completo }}')" style="cursor: pointer;">
                                @if($responsavel->is_encarregado_de_educacao)
                                    <strong class="text-primary">
                                        <span class="badge label-danger rounded-circle" style="font-size: 12px; padding: 5px 10px;">
                                            <i class="fas fa-user-tie"></i> EE
                                            {{ $responsavel->nome_completo }} 
                                        </span>
                                    </strong>
                                @else
                                    {{ $responsavel->nome_completo }}
                                @endif
                            </span>
                            <i class="fas fa-copy ml-2" style="cursor: pointer;" onclick="copiarDados('{{ $responsavel->nome_completo }}')"></i>
                        </td>
                        <td class="d-flex align-items-center">
                            <span class="copiar" onclick="copiarDados('{{ $responsavel->nr_identificacao }}')" style="cursor: pointer;">
                                {{ $responsavel->nr_identificacao }}
                            </span>
                            <i class="fas fa-copy ml-2" style="cursor: pointer;" onclick="copiarDados('{{ $responsavel->nr_identificacao }}')"></i>
                        </td>
                        <td class="d-flex align-items-center">
                            <span class="copiar" onclick="copiarDados('{{ $responsavel->contacto }}')" style="cursor: pointer;">
                                {{ $responsavel->contacto }}
                            </span>
                            <i class="fas fa-copy ml-2" style="cursor: pointer;" onclick="copiarDados('{{ $responsavel->contacto }}')"></i>
                        </td>
                        <td class="d-flex align-items-center">
                            <span class="copiar" onclick="copiarDados('{{ $responsavel->email }}')" style="cursor: pointer;">
                                {{ $responsavel->email }}
                            </span>
                            <i class="fas fa-copy ml-2" style="cursor: pointer;" onclick="copiarDados('{{ $responsavel->email }}')"></i>
                        </td>

                        <td class="text-center">
                            <a href="{{ route('responsaveis.show', ['responsavelId' => $responsavel->id]) }}" class="btn btn-sm btn-light border" title="Ver Detalhes">
                                <i class="fas fa-eye text-info"></i>
                            </a>
                            <a href="{{ route('responsaveis.edit', ['responsavelId' => $responsavel->id]) }}" class="btn btn-sm btn-light border" title="Editar">
                                <i class="fas fa-edit text-warning"></i>
                            </a>
                            <button class="btn btn-sm btn-light border" data-toggle="modal" data-target="#deleteModal{{ $responsavel->id }}" title="Remover">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal de Remoção -->
                    <div class="modal fade" id="deleteModal{{ $responsavel->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Remoção</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>{{ $responsavel->nome_completo }}</strong> pode estar associado a uma ou mais crianças. Esta ação irá remover a associação e todos os dados do responsável.</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('responsaveis.removerCompletamente', ['responsavelId' => $responsavel->id]) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Remover</button>
                                    </form>

                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($responsaveis->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">Nenhum responsável encontrado.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-between">
        <div>
            Mostrar {{ $responsaveis->firstItem() }} a {{ $responsaveis->lastItem() }} de {{ $responsaveis->total() }} registros
        </div>
        <div>
            {{ $responsaveis->appends(request()->input())->links() }}
        </div>
    </div>
</div>

<!-- 🔎 Pesquisa em Tempo Real -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Função para copiar os dados ao clicar
    window.copiarDados = function(valor) {
        navigator.clipboard.writeText(valor).then(() => {
            alert("Dado copiado para a área de transferência!");
        }).catch(err => {
            console.error("Erro ao copiar dados: ", err);
            alert("Falha ao copiar os dados.");
        });
    };
});
</script>

@endsection
