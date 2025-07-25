@extends('layouts.default')

@section('title', 'Histórico Geral de Entradas e Saídas')

@section('content')
<div class="container" style="padding-bottom: 100px;">
    <h1>Histórico Geral de Entradas e Saídas</h1>

    <!-- Formulário de Filtro -->
    <!-- Formulário de Filtro -->
<form method="GET" action="{{ route('history.all') }}">
    <div class="form-row">
        <div class="col">
            <label for="asset_name">Nome ou Código do Utente:</label>
            <input type="text" id="asset_name" name="asset_name" class="form-control" placeholder="Digite o nome ou código do utente" value="{{ request('asset_name') }}">
            <div id="autocomplete-results" style="position: absolute; z-index: 9999; background: white; border: 1px solid #ccc;"></div>
        </div>
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
                <option value="checkin" {{ request('action_type') == 'checkin' ? 'selected' : '' }}>Entrada</option>
                <option value="checkout" {{ request('action_type') == 'checkout' ? 'selected' : '' }}>Saída</option>
            </select>
        </div>
		<p></p>
<div class="col mt-4">
    <button type="submit" class="btn btn-primary">Filtrar</button>
    <!-- Botão para Limpar Pesquisa -->
    <button type="button" class="btn btn-primary" onclick="window.location='{{ route('history.all') }}'">Limpar</button>
</div>
    </div>
</form>


    <!-- Tabela de Resultados -->
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID do Log</th>
                <th>Nome do Utente</th>
                <th>Código do Utente</th>
                <th>Tipo de Ação</th>
                <th>Responsável</th>
                <th>Nota</th>
                <th>Data da Ação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($checkoutsAndCheckins as $log)
                <tr>
                    <td>{{ $log->log_id }}</td>
                    <td>{{ $log->utente_name }}</td>
                    <td>{{ $log->utente_code }}</td> <!-- Código do Utente -->
                    <td>{{ ucfirst($log->action_type) }}</td>
                    <td>{{ $log->responsible_first_name }} {{ $log->responsible_last_name }}</td>
                    <td>{{ $log->action_note }}</td>
                    <td>{{ $log->action_date }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Nenhum registo encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginação -->
    <div class="d-flex justify-content-between">
        <div>
            Mostrar {{ $checkoutsAndCheckins->firstItem() }} a {{ $checkoutsAndCheckins->lastItem() }} de {{ $checkoutsAndCheckins->total() }} registros
        </div>
        <div>
            {{ $checkoutsAndCheckins->links() }}
        </div>
    </div>
	<div class="text-right" style="margin-top: 20px;">
    <a href="{{ route('history.export.all.excel', request()->all()) }}" class="btn btn-primary">
        Exportar para Excel
    </a>
</div>

</div>

<!-- Script de Autocomplete -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let debounceTimeout;

        $('#asset_name').on('keyup', function () {
            const query = $(this).val();

            if (query.length < 2) {
                $('#autocomplete-results').html('');
                return;
            }

            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {
                $.ajax({
                    url: "{{ route('autocomplete.utentes') }}",
                    type: "GET",
                    data: { q: query },
                    success: function (data) {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                html += `<div class="autocomplete-item" style="padding: 5px; cursor: pointer;" data-name="${item.name}">
                                            ${item.name} - Código: ${item.asset_tag}
                                         </div>`;
                            });
                        } else {
                            html = '<div style="padding: 5px;">Nenhum resultado encontrado</div>';
                        }

                        $('#autocomplete-results').html(html).show();
                    }
                });
            }, 300);
        });

        // Seleciona um item da lista de autocomplete
        $(document).on('click', '.autocomplete-item', function () {
            const selectedName = $(this).data('name');
            $('#asset_name').val(selectedName);
            $('#autocomplete-results').html('');
        });

        // Esconde a lista quando clicar fora
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#asset_name, #autocomplete-results').length) {
                $('#autocomplete-results').html('');
            }
        });
    });
</script>
@endsection
