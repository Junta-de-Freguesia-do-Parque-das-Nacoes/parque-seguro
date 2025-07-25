@extends('layouts.default')

@section('title', 'Gestão de Autorização de Recolha')

@section('content')
<div class="container mt-4">
    <h1 class="d-flex align-items-center">
        Autorizados à Recolha de {{ $utente->name }}
        @if($utente->image)
    <img src="{{ route('assets.foto', ['filename' => basename($utente->image)]) }}" 
         alt="Foto de {{ $utente->name }}" 
         class="rounded-circle ml-3 d-none d-md-inline" 
         style="width: 50px; height: 50px; object-fit: cover;">
@else
    <img src="{{ asset('img/anonimoadulto.png') }}" 
         alt="Sem Foto" 
         class="rounded-circle ml-3 d-none d-md-inline" 
         style="width: 50px; height: 50px; object-fit: cover;">
@endif

    </h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col text-right">
            <a href="{{ url('/hardware/' . $utente->id) }}" class="btn btn-primary btn-sm">
                Voltar aos Detalhes do Utente
            </a>
        </div>
    </div>

    @if(true)
    <a href="{{ route('responsaveis.createAssociado', $utente->id) }}" class="btn btn-success mb-3">
    + Adicionar Responsável
</a>


        <h2>Lista de Responsáveis</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Identificação</th>
                        <th>Parentesco</th>
                        <th>Contacto</th>
                        <th>Email</th>
                       
                        
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($responsaveis as $responsavel)
                        @php
                            $classeEstado = '';
                            if ($responsavel->estado_autorizacao == 'Autorizacao Expirada') {
                                $classeEstado = 'table-danger';
                            } elseif ($responsavel->estado_autorizacao == 'Nao Iniciado') {
                                $classeEstado = 'table-warning';
                            }
                        @endphp

                        <tr class="{{ $classeEstado }}">
                        <td class="text-center">
    <img src="{{ $responsavel->foto ? route('responsaveis.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}" 
         alt="Foto de {{ $responsavel->nome_completo }}" 
         class="rounded" 
         style="width: 50px; height: 50px; object-fit: cover;">
</td>


<td>
    @if($responsavel->responsavel_tipo == 'Encarregado de Educacao')
        <strong class="text-primary">
            
            <span class="badge label-danger rounded-circle" style="font-size: 12px; padding: 5px 10px;">
                <i class="fas fa-user-tie"></i> EE
                {{ $responsavel->nome_completo }} 
            </span>
        </strong>
    @else
        {{ $responsavel->nome_completo }}
    @endif
</td>


                            <td>{{ $responsavel->nr_identificacao }}</td>
                            <td>{{ $responsavel->grau_parentesco }}</td>
                            <td>{{ $responsavel->contacto }}</td>
                            <td class="text-truncate" style="max-width: 150px;">{{ $responsavel->email }}</td>
                           


<td>
    @if($responsavel->autorizacao_estado == 'Autorizado')
        <span class="badge label-success rounded-circle"><i class="fas fa-check-circle"></i> Autorizado</span>
    @elseif($responsavel->autorizacao_estado == 'Autorizacao Expirada')
        <span class="badge label-danger rounded-circle"><i class="fas fa-times-circle"></i> Expirado</span>
    @elseif($responsavel->autorizacao_estado == 'Nao Iniciado')
        <span class="badge label-warning rounded-circle"><i class="fas fa-exclamation-circle"></i> Pendente</span>
    @else
        <span class="badge badge-secondary rounded-circle"><i class="fas fa-question-circle"></i> Desconhecido</span>
    @endif
</td>




                            <td class="text-center">
    <!-- Ver Detalhes -->
    <a href="{{ route('responsaveis.show', ['responsavelId' => $responsavel->id]) }}" class="btn btn-sm btn-light border" title="Ver Detalhes">
        <i class="fas fa-eye text-info"></i>
    </a>

 

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
                    <p>O que deseja fazer com <strong>{{ $responsavel->nome_completo }}</strong>?</p>
                    <p>Ele ainda pode estar associado a outras crianças.</p>
                </div>
                <div class="modal-footer">
                    <!-- Botão para remover apenas a associação -->
                    <form action="{{ route('responsaveis.destroy', ['utenteId' => $utente->id, 'responsavelId' => $responsavel->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="tipo_remocao" value="apenas_associacao">
                        <button type="submit" class="btn btn-warning">Remover Associação</button>
                    </form>

                    <!-- Botão para remover completamente -->
                    <form action="{{ route('responsaveis.destroy', ['utenteId' => $utente->id, 'responsavelId' => $responsavel->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="tipo_remocao" value="remover_tudo">
                        <button type="submit" class="btn btn-danger">Remover Tudo</button>
                    </form>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</td>

                        </tr>
                    @endforeach

                    @if($responsaveis->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center">Nenhum responsável encontrado.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
</div>

<div style="padding-bottom: 80px;"></div>
@endsection

