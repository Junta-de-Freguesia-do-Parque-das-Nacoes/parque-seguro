@extends('layouts.default')

@section('title', 'Histórico de Modificações')

@section('content')
<div class="container mt-5">
    <h1>Histórico de Modificações de {{ $responsavel->nome_completo }}</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Identificação</th>
                <th>Contacto</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Grau de Parentesco</th>
                <th>Data Início</th>
                <th>Data Fim</th>
                <th>Alterado Por</th>
                <th>Alterado Em</th>
                <th>Modificação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historico as $registro)
                <tr>
                    <td>{{ $registro->nome_completo }}</td>
                    <td>{{ $registro->nr_identificacao }}</td>
                    <td>{{ $registro->contacto }}</td>
                    <td>{{ $registro->email }}</td>
                    <td>{{ $registro->tipo_responsavel }}</td>
                    <td>{{ $registro->grau_parentesco }}</td>
                    <td>{{ $registro->data_inicio_autorizacao ? date('d/m/Y', strtotime($registro->data_inicio_autorizacao)) : '-' }}</td>
                    <td>{{ $registro->data_fim_autorizacao ? date('d/m/Y', strtotime($registro->data_fim_autorizacao)) : '-' }}</td>
                    <td>{{ $registro->alteradoPorNome ?? 'Desconhecido' }}</td>
                    <td>{{ date('d/m/Y H:i:s', strtotime($registro->alterado_em)) }}</td>
                    <td>{{ $registro->motivo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('responsaveis.index', ['utenteId' => $responsavel->utente_id]) }}" class="btn btn-primary">Voltar</a>
    <a href="{{ route('responsaveis.show', ['utenteId' => $utenteId, 'responsavelId' => $responsavel->id]) }}">Ver detalhes</a>

</div>
@endsection
