@extends('layouts.default')

@section('title', 'Editar Opções do Programa')

@section('content')
<div class="box">
    <div class="box-body">
        <h3>Editar opções de: <strong>{{ $campo->name }}</strong></h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('programas.opcoes.atualizar', $campo->id) }}">
            @csrf
            @method('PUT') {{-- Corrigido aqui para refletir o método correto da rota --}}

            <div class="form-group">
                <label for="valores">Valores do campo (um por linha):</label>
                <textarea name="valores" id="valores" class="form-control" rows="10" {{ $temInscritos ? 'readonly' : '' }}>{{ implode("\n", $valores) }}</textarea>
                @if($temInscritos)
                    <small class="text-warning">
                        ⚠️ Não é possível editar os valores enquanto existirem utentes inscritos neste programa.
                    </small>
                @endif
            </div>

            <button type="submit" class="btn btn-primary" {{ $temInscritos ? 'disabled' : '' }}>Guardar alterações</button>
            <a href="{{ route('programas.gestao') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@stop
