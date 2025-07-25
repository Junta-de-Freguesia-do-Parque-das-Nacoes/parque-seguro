@extends('layouts.default')

@section('title', 'Histórico de Entradas e Saídas de ' . $utente->name)

@section('content')
<div class="container">
    <h1>Histórico de Entradas e Saídas de {{ $utente->name }}</h1>
	
	    <div class="row mb-3">
        <div class="col-md-12 text-right">
            <a href="{{ url('/hardware/' . $utente->id) }}" class="btn btn-primary btn-sm">
                Voltar aos Detalhes do Utente
            </a>
        </div>
    </div>

    <!-- Formulário para filtro de datas -->
    <form method="GET" action="{{ route('history.show', $utente->id) }}">
        <div class="form-row">
            <div class="col">
                <label for="start_date">Data de Início:</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col">
                <label for="end_date">Data de Fim:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col">
                <label for="action_type">Tipo de Ação:</label>
                <select name="action_type" id="action_type" class="form-control">
                    <option value="">Todos</option>
                    <option value="checkin" {{ request('action_type') == 'checkin' ? 'selected' : '' }}>Entradas</option>
                    <option value="checkout" {{ request('action_type') == 'checkout' ? 'selected' : '' }}>Saídas</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Filtrar</button>
                <a href="{{ route('history.show', $utente->id) }}" class="btn btn-primary" style="margin-top: 32px;">Limpar Filtro</a>
            </div>
        </div>
    </form>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID do Log</th>
                <th>Tipo de Ação</th>
                <th>Responsável JFPN</th>
                <th>Pessoa Autorizada</th>
                <th>Data da Ação</th>
                
            </tr>
        </thead>
        <tbody>
            @forelse($checkoutsAndCheckins as $log)
                <tr>
                    <td>{{ $log->log_id }}</td>
                    <td>{{ ucfirst($log->action_type) }}</td>
                    <td>{{ $log->responsible_first_name }} {{ $log->responsible_last_name }}</td>
                    <td>{{ $log->action_note }}</td>
                    <td>{{ $log->action_date }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum log encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
  
    <!-- Links de paginação -->
    <div class="d-flex justify-content-between">
        <div>
            Mostrar {{ $checkoutsAndCheckins->firstItem() }} a {{ $checkoutsAndCheckins->lastItem() }} de {{ $checkoutsAndCheckins->total() }} Registos
        </div>
        <div>
            {{ $checkoutsAndCheckins->links() }} <!-- Adiciona os links de paginação -->
        </div>
    </div>

<div class="text-right" style="margin-top: 20px;">
    <a href="{{ route('history.export.excel', $utente->id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="btn btn-primary">Exportar para Excel</a>
</div>

</div>
@endsection
